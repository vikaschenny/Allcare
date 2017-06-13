<?php
/** **************************************************************************
 *	WMT.CLASS.PHP
 *	This file contains the standard classes for the dermatology implementation
 *	of OpenEMR. The file must be included in each dermatology form file or the
 *	implementation will not function correctly.
 *
 *  NOTES:
 *  1) __CONSTRUCT - always uses the record ID to retrieve data from the database
 *  2) GET - uses alternate selectors to find and return associated object
 *  3) FIND - returns only the object ID without data using alternate selectors
 *  4) LIST - returns an array of IDs meeting specific selector criteria
 *  5) FETCH - returns an array of data meeting specific criteria
 *   
 * 
 *  @package WMT
 *  @version 2.0 - Updated SQL statements to newer version
 *  @copyright Williams Medical Technologies, Inc.
 *  @author Ron Criswell <ron.criswell@MDTechSvcs.com>
 * 
 *************************************************************************** */

/** 
 * Provides a representation of the patient data record. Fields are dymanically
 * processed based on the current database definitions. 
 *
 * @package WMT
 * @subpackage Standard
 * @category Patient
 * @tutorial This object will vary by implementation
 */
class wmtPatient {
	// generated values
	public $format_name;
	public $birth_date;
	public $age;
	
	/**
	 * Constructor for the 'patient' class which retrieves the requested 
	 * patient information from the database or creates an empty object.
	 * 
	 * @param int $id patient record identifier
	 * @param boolean $update - DEPRECATED
	 * @return object instance of patient class
	 */
	public function __construct($id = false) {
		if(!$id) return false;

		$query = "SELECT * FROM patient_data WHERE id = ?";
		$data = sqlQuery($query,array($id));
	
		if ($data && $data['pid']) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtPatient::_construct - no patient record with id ('.$this->id.').');
		}
		
		// preformat commonly used data elements
		$this->format_name = ($this->title)? "$this->title " : "";
		$this->format_name .= ($this->fname)? "$this->fname " : "";
		$this->format_name .= ($this->mname)? substr($this->mname,0,1).". " : "";
		$this->format_name .= ($this->lname)? "$this->lname " : "";

		if ($this->DOB && strtotime($this->DOB) !== false) { // strtotime returns FALSE or -1 for invalid date
			//Criswell - 2013-07-22 - Correct for mis configured PHP systems with no timezone set
			$this->age = floor( (strtotime('today') - strtotime($this->DOB)) / 31556926 );
			$this->birth_date = date('Y-m-d', strtotime($this->DOB));
		}
		
		return;
	}	

	/**
	 * Inserts data from a form object into the database. The columns of the patient table
	 * are used to select the appropriate data from the patient object provided.
	 *
	 * @static
	 * @param wmtPatient $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtPatient $object) {
		if($object->id)
			throw new Exception ("wmtPatient::insert - object already contains identifier");

		// insert record
		$query = '';
		$params = array();
		$object->activity = 1;
		$fields = sqlListFields($table); // need parent fields
		
		foreach ($object as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") continue;
						
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the insert
		$object->id = sqlInsert("INSERT INTO patient_data SET $query",$params);

		return $object->id;
	}

	/**
	 * Updates database with information from the current object.
	 * 
	 * @return boolean update success flag
	 */
	public function update() {
		if(! $this->id)
			throw new Exception ("wmtPatient::update - current object does not contain identifier");

		// set appropriate date values
		$title = ($this->title)? $this->title : 'undefined';
		
		// build query from object
		$query = '';
		$params = array();
		$fields = self::listFields();
		
		foreach ($this as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") $value = "";
			
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}
		
		// run the update
		$params[] = $this->id;		
		sqlInsert("UPDATE patient_data SET $query WHERE id = ?",$params);
		
		return true;
	}
	
	
	/**
	 * Returns an array of valid database fields for the object.
	 * 
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields() {
		return sqlListFields('patient_data');
	}
	
	/**
	 * Retrieve a patient object by PID value. Uses the base constructor for the 'patient' class 
	 * to create and return the object. 
	 * 
	 * @static
	 * @param int $pid patient record identifier
	 * @param boolean $update - DEPRECATED
	 * @return object instance of patient class
	 */
	public static function getPidPatient($pid, $update = false) {
		if(!$pid)
			throw new Exception('wmtPatient::getPidPatient - no patient identifier provided.');
		
		$data = sqlQuery("SELECT id FROM patient_data WHERE pid = ?",array($pid));
		
		if(!$data || !$data['id']) return false;

		return new wmtPatient($data['id']);
	}
}

/** 
 * Provides standardized base class for an encounter which
 * is typically extended for specific types of encounters.
 *
 * @package WMT
 * @subpackage Encounter
 */
class wmtEncounter {
	public $id;
	public $date;
	public $reason;
	public $facility;
	public $facility_id;
	public $pid;
	public $encounter;
	public $onset_date;
	public $sensitivity;
	public $billing_note;
	public $pc_catname;
	public $pc_catid;
	public $provider_id;
	public $supervisor_id;
	public $referral_source;
	public $billing_facility;
	
	/**
	 * Constructor for the 'encounter' class which retrieves the requested 
	 * record from the database or creates an empty object.
	 * 
	 * @param int $id record identifier
	 * @param boolean $update - DEPRECATED
	 * @return object instance of class
	 */
	public function __construct($id = false) {
		if(!$id) return false;

		$query = "SELECT fe.*, pc.pc_catname FROM form_encounter fe ";
		$query .= "LEFT JOIN openemr_postcalendar_categories pc ON fe.pc_catid = pc.pc_catid ";
		$query .= "WHERE fe.id = ? ";
		$query .= "ORDER BY fe.date, fe.id";
		$results = sqlStatement($query,array($id));
	
		if ($data = sqlFetchArray($results)) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				if ($key == 'date' || $key == 'onset_date') {
					$value = date('Y-m-d', strtotime($value));
				}
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtEncounter::_construct - no encounter record with id ('.$id.').');
		}
	}	
		
	/**
	 * Inserts data from an error object into the database.
	 * 
	 * @static
	 * @param Errors $iderror_object
	 * @return null
	 */
	public static function insert(wmtEncounter $object) {
		if($object->id)
			throw new Exception ("wmtEncounter::insert - object already contains identifier");

		// get facility name from id
		$fres = sqlQuery("SELECT name FROM facility WHERE id = ?",array($object->facility_id));
		$object->facility = $fres['name'];

		// create basic encounter
		$object->encounter = generate_id(); // in sql.inc
		
		// verify dates (strtotime returns false on invalid date)
		if (! strtotime($object->date)) $object->date = date('Y-m-d');
		if (! strtotime($object->onset_date)) $object->onset_date = $object->date;

		// build sql insert from object
		$query = '';
		$params = array();
		$fields = self::listFields();
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD') continue;
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the insert
		$object->id = sqlInsert("INSERT INTO form_encounter SET $query",$params);

		return $object->id;
	}

	/**
	 * Updates data from an object into the database.
	 * 
	 * @static
	 * @param wmtEncounter $object
	 * @return null
	 */
	public function update() {
		if(! $object->id)
			throw new Exception ("wmtEncounter::update - no identifier provided in object");

		// build sql update from object
		$query = '';
		$fields = self::listFields();
		$params = array($this->id); // keys
		foreach ($this as $key => $value) {
			if (!in_array($key, $fields) || $key == 'id') continue;
			if ($value == 'YYYY-MM-DD') continue;
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the update
		sqlInsert("UPDATE form_encounter SET $query WHERE id = ?",$params);

		return;
	}

	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function listPidEncounters($pid) {
		if (!$pid) return FALSE;

		$query = "SELECT fe.encounter, fe.id FROM form_encounter fe ";
		$query .= "LEFT JOIN issue_encounter ie ON fe.id = ie.list_id ";
		$query .= "LEFT JOIN lists l ON ie.list_id = l.id ";
		$query .= "WHERE fe.pid = ? AND l.enddate IS NULL ";
		$query .= "ORDER BY fe.date, fe.encounter";

		$results = sqlStatement($query,array($pid));
	
		$txList = array();
		while ($data = sqlFetchArray($results)) {
			$txList[] = array('id' => $data['id'], 'encounter' => $data['encounter']);
		}
		
		return $txList;
	}

	/**
	 * Retrieve the encounter record by encounter number.
	 * 
	 * @param int $id lists record identifier
	 * @param boolean $update - DEPRECATED
	 * @return object instance of lists class
	 */
	public static function getEncounter($encounter) {
		if (!$encounter) return FALSE;

		$query = "SELECT id FROM form_encounter WHERE encounter = ?";
		$data = sqlQuery($query,array($encounter));
		
		return new wmtEncounter($data['id']);
	}
}

/** 
 * Provides a representation of the insurance information. Fields are statically defined
 * but are stored in multiple database tables. The information is integrated  
 *
 * @package WMT
 * @subpackage Standard
 * @category Insurance
 * 
 */
class wmtInsurance {
	// generated values
	public $subscriber_format_name;
	public $subscriber_birth_date;
	public $subscriber_age;
	
	/**
	 * Constructor for the 'wmtInsurance' class which retrieves the requested 
	 * patient insurance information from the database or creates an empty object.
	 * 
	 * @param int $id insurance data record identifier
	 * @return object instance of patient insurance class
	 */
	public function __construct($id = false) {
		if(!$id) return false;

		$query = "SELECT a.*, i.*, c.name AS company_name, c.id AS company_id, c.freeb_type AS plan_type, c.cms_id FROM insurance_data i ";
		$query .= "LEFT JOIN insurance_companies c ON i.provider = c.id ";
		$query .= "LEFT JOIN addresses a ON a.foreign_id = c.id ";
		$query .= "WHERE i.id = ? LIMIT 1 ";
		
		$data = sqlQuery($query,array($id));
		if ($data && $data['provider']) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtInsurance::_construct - no insurance record with id ('.$id.').');
		}
		
		if ($this->subscriber_DOB && strtotime($this->subscriber_DOB)) { // strtotime returns FALSE on date error
			$this->subscriber_age = floor( (strtotime('today') - strtotime($this->subscriber_DOB)) / 31556926);
			$this->subscriber_birth_date = date('Y-m-d', strtotime($this->subscriber_DOB));
		}
		
		return;
	}	

	/**
	 * Retrieve a insurance object by PID value. Uses the base constructor for the 'insurance' class 
	 * to create and return the object. 
	 * 
	 * @static
	 * @param int $id patient record identifier
	 * @param string $type 'primary', 'secondary', 'tertiary'
	 * @return array object list of insurance objects
	 */
	public static function getPidInsurance($pid, $type = null) {
		if(! $pid)
			throw new Exception('wmtInsurance::getPidInsurance - no patient identifier provided.');
		
		$query = "SELECT id, type, date FROM insurance_data WHERE pid = ? ";
		if ($type) $query .= "AND type = ? ";
		$query .= "AND provider != '' AND provider IS NOT NULL "; 
		$query .= "ORDER BY date DESC ";

		$list = array();
		$params = array();
		$params[] = $pid;
		if ($type) $params[] = strtolower($type);

		$results = sqlStatement($query,$params);
		while ($data = sqlFetchArray($results)) {
			if ($data['type'] == 'primary' && !$list[0]) $list[0] = new wmtInsurance($data['id']);
			if ($data['type'] == 'secondary' && !$list[1]) $list[1] = new wmtInsurance($data['id']);
			if ($data['type'] == 'tertiary' && !$list[2]) $list[2] = new wmtInsurance($data['id']);
		}
		
		return $list;
	}
	
	/**
	 * Retrieve a insurance object by PID value that was active on a given date. 
	 * Uses the base constructor for the 'insurance' class 
	 * to create and return the object. 
	 * 
	 * @static
	 * @param int $pid patient record identifier
	 * @param date $date insurance as of date
	 * @param string $type 'primary', 'secondary', 'tertiary'
	 * @return array object list of insurance objects
	 */
	public static function getPidInsDate($pid, $date, $type = null) {
		if(! $pid)
			throw new Exception('wmtInsurance::getPidInsDate - no patient identifier provided.');

		if(!$date || strtotime($date) === false) // strtotime returns FALSE or -1 on invalid date
			throw new Exception('wmtPatient::getPidInsDate - invalid date provided.');

		$query = "SELECT id, type, date FROM insurance_data WHERE pid = ? ";
		$query .= "AND provider != '' AND provider IS NOT NULL "; 
		$query .= "AND date <= ? ";
		if ($type) $query .= "AND type = ? ";
		$query .= "ORDER BY date DESC ";

		$list = array();
		$params = array();
		$params[] = $pid;
		$params[] = date('Y-m-d',strtotime($date));
		if ($type) $params[] = strtolower($type);
		
		$results = sqlStatement($query,$params);
		while ($data = sqlFetchArray($results)) {
			if ($data['type'] == 'primary' && !$list[0]) $list[0] = new wmtInsurance($data['id']);
			if ($data['type'] == 'secondary' && !$list[1]) $list[1] = new wmtInsurance($data['id']);
			if ($data['type'] == 'tertiary' && !$list[2]) $list[2] = new wmtInsurance($data['id']);
		}
		
		return $list;
	}
	
	/**
	 * Retrieve a single insurance company.
	 * 
	 * @static
	 * @param int $provider insurance provider identifier
	 * @return array insurance company data record
	 */
	public static function getCompany($provider) {
		if(! $provider)
			throw new Exception('wmtPatient::getCompany - no insurance company provider identifier.');
		
		$record = array();
		if ($provider == 'self') {
			$record['name'] = "Self Insured";
		}
		else {
			$query = "SELECT ia.*, ip.*, ic.id AS company_id, ic.name AS company_name, ic.freeb_type AS plan_type FROM insurance_companies ic ";
			$query .= "LEFT JOIN addresses ia ON ia.foreign_id = ic.id ";
			$query .= "LEFT JOIN phone_numbers ip ON ip.foreign_id = ic.id ";
			$query .= "WHERE ic.id = ? LIMIT 1 ";
			$record = sqlQuery($query,array($provider));
		}
				
		return $record;
	}
}


/**
 * This class provides methods for retrieving and maintaining
 * immunization records.
 * 
 * @package WMT
 * @subpackage Standard
 * @category Immunizations
 *
 */
class wmtImmunization {
	public $id;
	public $patient_id;
	public $administered_date;
	public $immunization_id; // list 'immunizations'
	public $cvs_code;
	public $manufacturer;
	public $lot_number;
	public $administered_by_id; // user id
	public $administered_by;
	public $education_date;
	public $vis_date;
	public $note;
	public $created_date;
	public $update_date;
	public $created_by; // user id
	public $updated_by; // user id
	public $amount_administered;
	public $amount_administered_unit;
	public $expiration_date;
	public $route;
	public $administration_site;
	public $added_erroneously;
	
	public $cpt4; // from 'immunizations' list
	public $title; // from 'immunizations' list

	/**
	 * Constructor for the 'wmtImmunization' class which retrieves the requested
	 * information from the database or creates an empty object.
	 *
	 * @param int $id immunization record identifier
	 * @return object instance of immunization class
	 */
	public function __construct($id = false) {
		if(!$id) return false;

		$query = "SELECT i.*, l.title, l.notes AS cpt4 FROM immunizations i ";
		$query .= "LEFT JOIN list_options l ON i.immunization_id = l.option_id AND l.list_id = 'immunizations' ";
		$query .= "WHERE i.id = ? LIMIT 1 ";

		$data = sqlQuery($query,array($id));
		if ($data && $data['immunization_id']) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtImmunization::_construct - no immunization record with id ('.$id.').');
		}
		
		return;
	}

	/**
	 * Inserts data from a form object into the database. The columns of the patient table
	 * are used to select the appropriate data from the patient object provided.
	 *
	 * @static
	 * @param wmtImmunization $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtImmunization $object) {
		if($object->id)
			throw new Exception ("wmtImmunization::insert - object already contains identifier");

		// insert parent record
		$query = '';
		$params = array();
		$object->activity = 1;
		$fields = self::listFields();
		
		foreach ($object as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") $value = "";
			
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the insert
		$object->id = sqlInsert("INSERT INTO immunizations SET $query",$params);

		return $object->id;
	}

	/**
	 * Updates database with information from the current object.
	 * 
	 * @return boolean update success flag
	 */
	public function update() {
		if(! $this->id)
			throw new Exception ("wmtImmunization::update - current object does not contain identifier");

		// set appropriate date values
		$title = ($this->title)? $this->title : 'undefined';
		
		// build query from object
		$query = '';
		$params = array();
		$fields = self::listFields();
		foreach ($this as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}
		
		// run the update		
		$params[] = $this->id;		
		sqlInsert("UPDATE immunizations SET $query WHERE id = ?",$params);
		
		return true;
	}
	
	
	/**
	 * Returns an array of valid database fields for the object.
	 * 
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields() {
		return sqlListFields('immunizations');
	}
	
	/**
	 * Returns an array list of form objects for a single PID
	 * given PID.
	 *
	 * @static
	 * @param int $pid patient identifier
	 * @return array $objectList list of selected form objects
	 */
	public static function fetchPidImmun($pid) {
		if (!$pid)
			throw new Exception('wmtImmunization::fetchPidImmun - missing parameters');

		$query = "SELECT id FROM immunizations WHERE patient_id = ? ";
		$query .= "ORDER BY administered_date";

		$results = sqlStatement($query,array($pid));

		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtImmunization($data['id']);
		}

		return $objectList;
	}

	/**
	 * Build a table to display current immunizations.
	 *
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 * @deprecated
	 */
	public static function immunTable($pid) {
		$immuns = wmtImmunization::fetchPidImmun($pid);
	
		$output = "<table><tr>\n";
		$output .= "<th class=\"wmtHeader\" style=\"white-space:nowrap;width:40%\">Immunization</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"min-width:100px\">Date</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"min-width:120px\">CVX Code</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:30%\">Comments</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:30px\">Action</th>\n";
		$output .= "</tr>\n";
	
		if (count($immuns) > 0) {
			foreach ($immuns as $immun) {
				$raw_date = strtotime($immun->administered_date);
				$admin_date = ($raw_date)? date('Y-m-d',$raw_date) : 'UNAVAILABLE';
				$output .= "<tr class=\"wmtLabel\">";
				$output .= "<td>".$immun->title."</td>\n";
				$output .= "<td>".$admin_date."</td>\n";
				$output .= "<td>".$immun->cvx_code."</td>\n";
				$output .= "<td>" . $immun->note."</td>\n";
				$output .= "<td><a class='iframe css_button_small' href='".$GLOBALS['webroot']."/library/wmt/edit_immunization.php?issue=".$immun->id."'>";
				$output .= "<span>Edit</span></a></td>";
				$output .= "</tr>\n";
			}
		}
		else { // no issues
			$output .= "<tr class=\"wmtLabel\">\n";
			$output .= "<td colspan=\"4\">None on file</td>\n";
			$output .= "</tr>\n";
	
		}
	
		$output .= "</table>\n";
		return $output;
	}
}

/**
 * Provides standardized processing for most forms.
 *
 * @package WMT
 * @subpackage Forms
 */
class wmtForm {
	public $id;
	public $date;
	public $pid;
	public $user;
	public $groupname;
	public $authorized;
	public $activity;
	public $status;
	public $priority;

	// control elements
	protected $form_name;
	protected $form_table;
	protected $form_title;

	/**
	 * Constructor for the 'form' class which retrieves the requested
	 * information from the database or creates an empty object.
	 *
	 * @param string $form_table database table
	 * @param int $id record identifier
	 * @return object instance of form class
	 */
	public function __construct($form_name, $id = false) {
		if (!$form_name)
			throw new Exception('wmtForm::_construct - no form name provided.');

		// store table name in object
		$this->form_name = $form_name;
		$this->form_table = 'form_'.$form_name;

		// create empty record or retrieve
		if (!$id) return false;

		// retrieve data
		$query = "SELECT f.*, t.* FROM $this->form_table t ";
		$query .= "LEFT JOIN forms f ON f.form_id = t.id AND f.formdir = ? ";
		$query .= "WHERE t.id = ?";
		$data = sqlQuery($query,array($form_name,$id));

		if ($data && $data['pid']) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtForm::_construct - no record with id ('.$this->form_table.' - '.$id.').');
		}

		// preformat commonly used data elements
		$this->date = ($this->date)? date('Y-m-d',strtotime($this->date)) : date('Y-m-d');

		return;
	}

	/**
	 * Inserts data from a form object into the database.
	 *
	 * @static
	 * @param wmtForm $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtForm $object) {
		if(!$object->form_name || !$object->form_table)
			throw new Exception ("wmtForm::insert - object missing form information");

		if($object->id)
			throw new Exception ("wmtForm::insert - object already contains identifier");

		// insert record
		$query = '';
		$params = array();
		$object->activity = 1;
		$fields = sqlListFields($object->form_table); // need parent fields
		
		foreach ($object as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") continue;
						
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the insert
		$object->id = sqlInsert("INSERT INTO $object->form_table SET $query",$params);
		
		return $object->id;
	}

	/**
	 * Updates database with information from the given object.
	 *
	 * @return null
	 */
	public function update() {
		if(!$this->form_name || !$this->form_table)
			throw new Exception ("wmtForm::update - object missing form information");

		if(!$this->id)
			throw new Exception ("wmtForm::update - current object does not contain identifier");

		// build query from object
		$query = '';
		$params = array();
		$this->activity = 1;
		$fields = self::listFields($this->form_table);
		
		foreach ($this as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") $value = "";
			
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}
		
		// run the update
		$params[] = $this->id;		
		sqlInsert("UPDATE $this->form_table SET $query WHERE id = ?",$params);
		
		return;
	}

	/**
	 * Returns an array list objects associated with the
	 * given PATIENT and optionally a given TYPE. If no TYPE is given
	 * then all issues for the PATIENT are returned.
	 *
	 * @static
	 * @param int $pid patient identifier
	 * @param string $type type of list to select
	 * @param bool $active active items only flag
	 * @return array $objectList list of selected list objects
	 */
	public static function fetchPidList($form_name, $pid, $active=true) {
		if (!$form_name || !$pid)
			throw new Exception('wmtForm::fetchPidItem - missing parameters');

		$query = "SELECT form_id FROM forms ";
		$query .= "WHERE formdir = ? AND pid = ? ";
		if ($active) $query .= "AND deleted = 0 ";
		$query .= "ORDER BY date";

		$results = sqlStatement($query, array($form_name,$pid));

		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtForm($form_name,$data['form_id']);
		}

		return $objectList;
	}

	/**
	 * Returns an array list objects associated with the
	 * given ENCOUNTER and optionally a given TYPE. If no TYPE is given
	 * then all issues for the ENCOUNTER are returned.
	 *
	 * @static
	 * @param int $encounter encounter identifier
	 * @param string $type type of list to select
	 * @param bool $active active items only flag
	 * @return array $objectList list of selected list objects
	 */
	public static function fetchEncounterList($form_name, $encounter, $active=true) {
		if (!$form_name || !$encounter)
			throw new Exception('wmtForm::fetchEncounterItem - missing parameters');

		$query = "SELECT form_id FROM forms ";
		$query .= "WHERE formdir = ? AND encounter = ? ";
		if ($active) $query .= "AND deleted = 0 ";
		$query .= "ORDER BY date, id";

		$results = sqlStatement($query,array($form_name,$encounter));

		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtForm($form_name,$data['form_id']);
		}

		return $objectList;
	}

	/**
	 * Returns the most recent form object or an empty object based
	 * on the PID provided.
	 *
	 * @static
	 * @param string $form_name form type name
	 * @param int $pid patient identifier
	 * @param bool $active active items only flag
	 * @return object $form selected object
	 */
	public static function fetchRecent($form_name, $pid, $active=true) {
		if (!$form_name || !$pid)
			throw new Exception('wmtForm::fetchRecent - missing parameters');

		$query = "SELECT form_id FROM forms ";
		$query .= "WHERE formdir = ? AND pid = ? ";
		if ($active) $query .= "AND deleted = 0 ";
		$query .= "ORDER BY date DESC, id DESC";

		$data = sqlQuery($query,array($form_name,$pid));
		
		return new wmtForm($form_name,$data['form_id']);
	}

	/**
	 * Returns an array of valid database fields for the object.
	 *
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields($form_table) {
		if (!$form_table)
			throw new Exception('wmtForm::listFields - no form table name provided.');

		$fields = sqlListFields($form_table);

		return $fields;
	}

}

/**
 * Provides standardized processing for procedure order forms.
 *
 * @package WMT
 * @subpackage Forms
 */

class wmtOrder extends wmtForm {
	/* Inherited from wmtForm
	public $id;
	public $date;
	public $pid;
	public $user;
	public $groupname;
	public $authorized;
	public $activity;
	public $status;
	public $priority;
	
	protected $form_name;
	protected $form_table;
	protected $form_title;
	*/
	
	private $form_type;
	
	public $procedure_order_id;
	public $provider_id; // references users.id, the ordering provider
	public $patient_id; // references patient_data.pid
	public $encounter_id; // references form_encounter.encounter
	public $date_collected;
	public $date_ordered;
	public $order_priority;
	public $order_status; // pending,routed,complete,canceled
	public $patient_instructions;
	public $activity; // 0 if deleted
	public $control_id; // CONTROL ID that is sent back from lab
	public $lab_id; // references procedure_providers.ppid
	public $specimen_type; // from the Specimen_Type list
	public $specimen_location; // from the Specimen_Location list
	public $specimen_volume; // from a text input field
	public $date_transmitted; // time of order transmission, null if unsent
	public $clinical_hx; // clinical history text that may be relevant to the order
	
	/**
	 * Constructor for the 'form' class which retrieves the requested
	 * information from the database or creates an empty object.
	 *
	 * @param string $form_table database table
	 * @param int $id record identifier
	 * @return object instance of form class
	 */
	public function __construct($form_type = "order", $id = false) {
		// retrieve form data
		$this->form_type = $form_type;

		// create empty record with no id
		if (!$id) return false;

		parent::__construct($form_type, $id);
		if (!$this->id)
			throw new Exception('wmtOrder::_construct - no base $form_type record with id ('.$id.').');
				
		// retrieve remaining data
		if (!$this->order_number)
			throw new Exception('wmtOrder::_construct - no procedure order number.');
		
		$query = "SELECT * FROM procedure_order WHERE procedure_order_id = ?";
		$data = sqlQuery($query,array($this->order_number));
		if (!$data['procedure_order_id'])
			throw new Exception('wmtOrder::_construct - no procedure order record with procedure_order_id ('.$this->order_number.').');
		
		// load everything returned into object
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

		return;
	}

	/**
	 * Inserts data from a form object into the database.
	 *
	 * @static
	 * @param wmtOrder $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtOrder $object) {
		if($object->id)
			throw new Exception ("wmtOrder::insert - object already contains identifier");

		$table = "form_".$object->form_type;
		
		// insert parent record
		$query = '';
		$params = array();
		$object->activity = 1;
		$fields = sqlListFields($table); // need parent fields
		
		foreach ($object as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") continue;
			if ($key == 'date') $value = date('Y-m-d H:i:s');
						
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the parent insert
		$object->id = sqlInsert("INSERT INTO $table SET $query",$params);

				
		// build sql insert for child
		$query = '';
		$params = array();
		$fields = sqlListFields('procedure_order'); // need only sup rec fields
		
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") continue;
				
			if ($key == 'procedure_order_id') $value = $object->order_number;
			if ($key == 'patient_id') $value = $object->pid; 
			if ($key == 'encounter_id') $value = $object->encounter;
			if ($key == 'control_id') $value = $object->control_id;
			
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the child insert
		sqlInsert("REPLACE procedure_order SET $query",$params);

		return $object->id;
	}

	/**
	 * Updates database with information from the given object.
	 *
	 * @return boolean update success flag
	 */
	public function update() {
		if(! $this->id)
			throw new Exception ("wmtOrder::update - current object does not contain identifier");

		if (! $this->form_type)
			throw new Exception ("wmtOrder::update - no form type set for object");

		$table = "form_".$this->form_type;
		
		// build sql update for parent
		$query = '';
		$params = array();
		$fields = sqlListFields($table);
		foreach ($this as $key => $value) {
			if ($key == 'id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") continue;
			if ($key == 'date') continue; // don't change original date!!
				
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? '' : $value;
		}

		// run the parent update
		$params[] = $this->id;
		sqlStatement("UPDATE $table SET $query WHERE id = ?",$params);

		// build sql update for child
		$query = '';
		$params = array();
		$fields = sqlListFields('procedure_order');
		foreach ($this as $key => $value) {
			if ($key == 'procedure_order_id') continue;
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD' || $value == "_blank") $value = "";

			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? '' : $value;
		}

		// run the update
		$params[] = $this->order_number;
		sqlStatement("UPDATE procedure_order SET $query WHERE procedure_order_id = ?",$params);

		return true;
	}

	/**
	 * Search and retrieve an order object by order number
	 *
	 * @static
	 * @parm string $order_num Order number for the order
	 * @return wmtOrder $object
	 */
	public static function fetchOrder($form_type = "order", $order_num, $lab_id, $pid, $pat_DOB = false) {
		if(! $order_num)
			throw new Exception ("wmtOrder::fetchOrder - no order number provided");

		if(! $lab_id)
			throw new Exception ("wmtOrder::fetchOrder - no lab identifier provided");

		$table = "form_".$form_type;

		$query = ("SELECT id FROM $table WHERE order_number = ? AND lab_id = ? AND (pid > 999999990 OR pid = ?) ");
		$params[] = $order_num;
		$params[] = $lab_id;
		$params[] = $pid;

		if ($pat_DOB) { 
			$query .= "AND pat_DOB = ? ";
			$params[] = $pat_DOB;
		}
		
		$order = sqlQuery($query,$params);
		if (!$order || !$order['id']) return false;
		
		return new wmtOrder($form_type, $order['id']);
	}

	/**
	 * Returns an array of valid database fields for the object.
	 *
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields($form_type = "order") {
		$table = "form_".$form_type;
		$fields1 = sqlListFields($table);
		$fields2 = sqlListFields('procedure_order');
		return array_merge($fields1,$fields2);
	}

}

/**
 * Provides standardized processing for procedure order forms.
 *
 * @package WMT
 * @subpackage Forms
 */

class wmtOrderItem {
	public $procedure_order_id;
	public $procedure_order_seq;
	public $procedure_code; 
	public $procedure_name; 
	public $procedure_source; // 1=original order, 2=added after order sent
	public $procedure_type; // S=single, P=profile
	public $lab_id; // associated provider
	public $diagnoses; // array() diagnoses and maybe other coding (e.g. ICD9:111.11)
	public $do_not_send; // 0 = normal, 1 = do not transmit to lab
	
	/**
	 * Constructor for the 'form' class which retrieves the requested
	 * information from the database or creates an empty object.
	 *
	 * @param int $id record identifier
	 * @return object instance of form class
	 */
	public function __construct($proc_order_id = false, $proc_order_seq = false, $update = false) {
		// create empty record with no id
		if (!$proc_order_id || !$proc_order_seq) return false;
		
		// retrieve data
		$query = "SELECT * FROM procedure_order_code poc WHERE procedure_order_id = ? AND procedure_order_seq = ?";
		$results = sqlStatement($query, array($proc_order_id, $proc_order_seq));

		if ($data = sqlFetchArray($results)) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = ($update)? formDataCore($value) : $value;
			}
		}
		else {
			throw new Exception('wmtOrderItem::_construct - no procedure order item record with key ('.$proc_order_id.' '.$proc_order_seq.').');
		}

		return;
	}

	/**
	 * Inserts data from a form object into the database.
	 *
	 * @static
	 * @param wmtForm $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtOrderItem $object) {
		// set appropriate default values
		$object->do_not_send = 0;

		// build sql insert from object
		$query = '';
		$params = array();
		$fields = wmtOrderItem::listFields();
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD') continue;
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the insert
		sqlInsert("INSERT INTO procedure_order_code SET $query",$params);

		return;
	}

	/**
	 * Updates database with information from the given object.
	 *
	 * @return null
	 */
	public function update() {
		// set appropriate default values
		$object->do_not_send = 0;

		// build sql update from object
		$query = '';
		$fields = wmtOrderItem::listFields();
		$params = array($this->procedure_order_id, $procedure_order_seq); // keys
		foreach ($this as $key => $value) {
			if (!in_array($key, $fields) || $key == 'procedure_order_id') continue;
			if ($value == 'YYYY-MM-DD') continue;
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the update
		sqlInsert("UPDATE procedure_order_code SET $query WHERE procedure_order_id = ? AND procedure_order_seq = ? ",$params);

		return;
	}

	/**
	 * Returns an array list of procedure order item objects associated with the
	 * given order.
	 *
	 * @static
	 * @param int $proc_order_id Procedure order identifier (parent order)
	 * @return array $objectList list of selected objects
	 */
	public static function fetchItemList($proc_order_id = false) {
		if (!$proc_order_id) return false;

		$query = "SELECT procedure_order_seq FROM procedure_order_code ";
		$query .= "WHERE procedure_order_id = ? ";
		$query .= "ORDER BY procedure_order_seq";

		$results = sqlStatement($query, array($proc_order_id));

		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtOrderItem($proc_order_id,$data['procedure_order_seq']);
		}

		return $objectList;
	}

	/**
	 * Returns an array list of procedure order item keys (seq num) associated with the
	 * given procedure code item from an order.  Used to match results.
	 *
	 * @static
	 * @param int $proc_order_id Procedure order identifier (parent order)
	 * @return array $objectList list of selected objects
	 */
	public static function fetchOrderItems($proc_order_id = false) {
		if (!$proc_order_id) return false;

		$query = "SELECT * FROM procedure_order_code ";
		$query .= "WHERE procedure_order_id = ? AND procedure_source = 1 ";
		$query .= "ORDER BY procedure_order_seq";

		$results = sqlStatement($query, array($proc_order_id));

		$orderedList = array();
		while ($data = sqlFetchArray($results)) {
			$orderedList[$data['procedure_code']] = $data['procedure_order_seq'];
		}

		return $orderedList;
	}

	/**
	 * Returns an array of valid database fields for the object.
	 *
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields() {
		$fields = sqlListFields('procedure_order_code');
		return $fields;
	}
}

/**
 * Provides standardized processing for procedure result forms.
 *
 * @package WMT
 * @subpackage Forms
 */

class wmtResult {
	public $procedure_report_id;
	public $procedure_order_id;
	public $procedure_order_seq;
	public $date_collected;
	public $date_report;
	public $source;
	public $specimen_num;
	public $report_status;
	public $review_status;
	public $report_notes;
	
	/**
	 * @param int $id record identifier
	 * @param boolean $update - DEPRECATED
	 * @return object instance of result class
	 */
	public function __construct($id = false) {
		// create empty record with no id
		if (!$id) return false;

		$query = "SELECT * FROM procedure_report WHERE procedure_report_id = ?";
		$data = sqlQuery($query,array($id));
		if (!$data['procedure_report_id'])
			throw new Exception('wmtResult::_construct - no procedure report record with procedure_report_id ('.$id.').');
		
		// load everything returned into object
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

		return;
	}

	/**
	 * Inserts data from a form object into the database.
	 *
	 * @static
	 * @param wmtOrder $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtResult $object) {
		if($object->procedure_report_id)
			throw new Exception ("wmtResult::insert - object already contains identifier");

		// build sql insert from object
		$query = '';
		$params = array();
		$fields = wmtResult::listFields(); 
		
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields)) continue;
			
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the child insert
		$id = sqlInsert("INSERT INTO procedure_report SET $query",$params);

		return $id;
	}

	/**
	 * Updates database with information from the given object.
	 *
	 * @return null
	 */
	public function update() {
		// build sql update from object
		$query = '';
		$params = array();
		$fields = $this->listFields();
		foreach ($this as $key => $value) {
			if (!in_array($key, $fields) || $key == 'procedure_report_id') continue;
			if ($value == 'YYYY-MM-DD') continue;
			
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the update
		sqlInsert("UPDATE procedure_report SET $query WHERE procedure_report_id = $this->procedure_report_id ",$params);

		return;
	}

	/**
	 * Search and retrieve an order object by order number
	 *
	 * @static
	 * @parm string $order_num Order number for the order
	 * @return wmtOrder $object
	 */
	public static function fetchResult($order_num, $order_seq, $update=false) {
		if(!$order_num) return false;

		$result = sqlQuery("SELECT procedure_report_id FROM procedure_report WHERE procedure_order_id = ? AND procedure_order_seq = ?",
				array($order_num, $order_seq));
		
		if (!$result['procedure_report_id']) return false;
		$result_data = new wmtResult($result['procedure_report_id'], $update);

		return $result_data;
	}

	/**
	 * Search and retrieve an order object by order number
	 *
	 * @static
	 * @parm string $order_num Order number for the order
	 * @return wmtOrder $object
	 */
	public static function fetchReflex($order_num, $reflex_code, $reflex_set) {
		if(!$order_num || !$reflex_code) return false;

		$query = "SELECT procedure_result_id FROM procedure_report rep ";
		$query .= "LEFT JOIN procedure_result res ON rep.procedure_report_id = res.procedure_report_id ";
		$query .= "WHERE rep.procedure_order_id = ? AND res.result_code = ? AND res.result_set = ? ";
		$result = sqlQuery($query,array($order_num, $reflex_code, $reflex_set));
		
		if (!$result['procedure_result_id']) return false;
		$result_data = new wmtResultItem($result['procedure_result_id']);

		return $result_data;
	}

	/**
	 * Returns an array of valid database fields for the object.
	 *
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields() {
		$fields = sqlListFields('procedure_report');
		return $fields;
	}

}

/**
 * Provides standardized processing for procedure result item data.
 *
 * @package WMT
 * @subpackage Forms
 */

class wmtResultItem {
	public $procedure_result_id;
	public $procedure_report_id;
	public $result_data_type; 
	public $result_code;
	public $result_text;
	public $date;
	public $facility;
	public $units;
	public $result;
	public $normal; // range is a reserved word
	public $abnormal;
	public $comments;
	public $result_status;
	
	/**
	 * Constructor for the 'form' class which retrieves the requested
	 * information from the database or creates an empty object.
	 *
	 * @param int $id record identifier
	 * @param boolean $update - DEPRECATED
	 * @return object instance of form class
	 */
	public function __construct($proc_result_id = false) {
		// create empty record with no id
		if (!$proc_result_id) return false;
		
		// retrieve data
		$query = "SELECT * FROM procedure_result WHERE procedure_result_id = ?";
		$results = sqlStatement($query, array($proc_result_id));

		if ($data = sqlFetchArray($results)) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtResultItem::_construct - no procedure result item record with key ('.$proc_result_id.')');
		}

		return;
	}

	/**
	 * Inserts data from a form object into the database.
	 *
	 * @static
	 * @param wmtForm $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtResultItem $object) {
		// build sql insert from object
		$query = '';
		$params = array();
		$fields = wmtResultItem::listFields();
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD') continue;
			
			// substitutions
			if ($key == 'abnormal' && $value == 'N') $value = '';
			
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the insert
		sqlInsert("INSERT INTO procedure_result SET $query",$params);

		return;
	}

	/**
	 * Updates database with information from the given object.
	 *
	 * @return null
	 */
	public function update() {
		// set appropriate default values
		$object->do_not_send = 0;

		// build sql update from object
		$query = '';
		$fields = wmtResultItem::listFields();
		$params = array($this->procedure_result_id); // keys
		foreach ($this as $key => $value) {
			if (!in_array($key, $fields) || $key == 'procedure_result_id') continue;
			if ($value == 'YYYY-MM-DD') continue;
					
			// substitutions 
			if ($key == 'abnormal' && $value == 'N') $value = '';
			if ($key == 'result_status') $value = ListLook($value,'proc_res_status');
							
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the update
		sqlStatement("UPDATE procedure_result SET $query WHERE procedure_result_id = ?",$params);

		return;
	}

	/**
	 * Returns an array list of procedure order item objects associated with the
	 * given order.
	 *
	 * @static
	 * @param int $proc_report_id Procedure report identifier (parent result)
	 * @return array $objectList list of selected objects
	 */
	public static function fetchItemList($proc_report_id = false) {
		if (!$proc_report_id) return false;

		$query = "SELECT procedure_result_id FROM procedure_result ";
		$query .= "WHERE procedure_report_id = ? ORDER BY procedure_result_id ";

		$results = sqlStatement($query, array($proc_report_id));

		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtResultItem($data['procedure_result_id']);
		}

		return $objectList;
	}

	/**
	 * Returns an array of valid database fields for the object.
	 *
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields() {
		$fields = sqlListFields('procedure_result');
		return $fields;
	}
}


/**
 * Provides standardized processing for procedure specimen item data.
 *
 * @package WMT
 * @subpackage Forms
 */

class wmtSpecimenItem {
	public $procedure_specimen_id;
	public $procedure_report_id;
	public $specimen_number; 
	public $specimen_type;
	public $type_modifier;
	public $specimen_additive;
	public $collection_method;
	public $source_site;
	public $source_quantifier;
	public $specimen_volume;
	public $collected_datetime;
	public $received_datetime;
	
	/**
	 * Constructor for the 'form' class which retrieves the requested
	 * information from the database or creates an empty object.
	 *
	 * @param int $id record identifier
	 * @param boolean $update - DEPRECATED
	 * @return object instance of form class
	 */
	public function __construct($proc_specimen_id = false) {
		// create empty record with no id
		if (!$proc_specimen_id) return false;
		
		// retrieve data
		$query = "SELECT * FROM procedure_specimen WHERE procedure_specimen_id = ?";
		$results = sqlStatement($query, array($proc_specimen_id));

		if ($data = sqlFetchArray($results)) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtSpecimenItem::_construct - no procedure specimen item record with key ('.$proc_specimen_id.')');
		}

		return;
	}

	/**
	 * Inserts data from a form object into the database.
	 *
	 * @static
	 * @param wmtForm $object
	 * @return int $id identifier for new object
	 */
	public static function insert(wmtSpecimenItem $object) {
		// build sql insert from object
		$query = '';
		$params = array();
		$fields = wmtSpecimenItem::listFields();
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields)) continue;
			if ($value == 'YYYY-MM-DD') continue;
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? '' : $value;
		}

		// run the insert
		sqlInsert("INSERT INTO procedure_specimen SET $query",$params);

		return;
	}

	/**
	 * Updates database with information from the given object.
	 *
	 * @return null
	 */
	public function update() {
		// build sql update from object
		$query = '';
		$fields = wmtSpecimenItem::listFields();
		$params = array($this->procedure_specimen_id); // keys
		foreach ($this as $key => $value) {
			if (!in_array($key, $fields) || $key == 'procedure_specimen_id') continue;
			if ($value == 'YYYY-MM-DD') continue;
			$query .= ($query)? ", `$key` = ? " : "`$key` = ? ";
			$params[] = ($value == 'NULL')? '' : $value;
		}

		// run the update
		sqlInsert("UPDATE procedure_specimen SET $query WHERE procedure_specimen_id = ?",$params);

		return;
	}

	/**
	 * Returns an array list of procedure order item objects associated with the
	 * given order.
	 *
	 * @static
	 * @param int $proc_report_id Procedure report identifier (parent specimen)
	 * @return array $objectList list of selected objects
	 */
	public static function fetchItemList($proc_report_id = false) {
		if (!$proc_report_id) return false;

		$query = "SELECT procedure_specimen_id FROM procedure_specimen ";
		$query .= "WHERE procedure_report_id = ? ";

		$results = sqlStatement($query, array($proc_report_id));

		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtSpecimenItem($data['procedure_specimen_id']);
		}

		return $objectList;
	}

	/**
	 * Returns an array of valid database fields for the object.
	 *
	 * @static
	 * @return array list of database field names
	 */
	public static function listFields() {
		$fields = sqlListFields('procedure_specimen');
		return $fields;
	}
}




/**
 * Provides standardized processing for most forms.
 *
 * @package WMT
 * @subpackage Documents
 */
class wmtDocument {

	private $template_mod;
	private $documents;
	private $document_categories;
	private $tree;
	private $config;
	private $file_path;

	public function __construct($template_mod = "general") {
		$this->documents = array();
		$this->template_mod = $template_mod;

		//get global config options for this namespace
		$this->config = $GLOBALS['oer_config']['documents'];
		if ($GLOBALS['document_storage_method'] == 1) {
			$this->file_path = $GLOBALS['OE_SITE_DIR'].'/documents/temp/';
		}
		else {
			$this->file_path = $this->config['repository'] . preg_replace("/[^A-Za-z0-9]/","_",$_GET['patient_id']) . "/";
		}
		
		// validate the directory
		if (!file_exists($this->file_path)) {
			if (!mkdir($this->file_path,0700)) {
				$error .= "The system was unable to create the directory for this upload, '" . $this->file_path . "'.\n";
			}
		}

		$this->tree = new CategoryTree(1);
	}

	function upload($file, $patient_id, $category_id) {
		$couchDB = false;
		$harddisk = true;

		$fname = "--file name--";
		if (file_exists($this->file_path.$fname)) {
			unlink($this->file_path.$fname); // delete it
				
		}

		$d = new Document();
		$d->storagemethod = $GLOBALS['document_storage_method'];
		$d->url = "file://" .$this->file_path.$fname;
		$d->mimetype = $file['type'];
		$d->size = $file['size'];
		$d->owner = $_SESSION['authUserID'];
		$d->hash = sha1_file( $this->file_path.$fname );
		$d->type = $d->type_array['file_url'];
		$d->set_foreign_id($patient_id);
		$d->persist();
		$d->populate();
			
		if (is_numeric($d->get_id()) && is_numeric($category_id)){
			$sql = "REPLACE INTO categories_to_documents set category_id = '" . $category_id . "', document_id = '" . $d->get_id() . "'";
			$d->_db->Execute($sql);
		}
	}
}

/** 
 * Provides a base class for records stored in the 'lists' table which includes
 * a large collection of general information. This is a general class which may
 * be used as a standalone object but is typically extended to support specific
 * types of list content.
 *
 * @package WMT
 * @subpackage wmtList
 */
class wmtList {
	public $id;
	public $date;
	public $type;
	public $title;
	public $begdate;
	public $enddate;
	public $returndate;
	public $occurrence;
	public $classification;
	public $referredby;
	public $extrainfo;
	public $diagnosis;
	public $activity;
	public $comments;
	public $pid;
	public $user;
	public $groupname;
	public $outcome;
	public $reaction;
	
	/**
	 * Constructor for the class which retrieves the requested 
	 * 'lists' record from the database or creates an empty object.
	 * 
	 * @param int $id lists record identifier
	 * @param boolean - DEPRECATED
	 * @return object instance of lists class
	 */
	public function __construct($id = FALSE, $update = false) {
		if(!$id) return false;

		$query = "SELECT * FROM lists WHERE id = ?";
		$results = sqlStatement($query,array($id));
	
		if ($data = sqlFetchArray($results)) {
			// load everything returned into object
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			throw new Exception('wmtList::_construct - no list record with id ('.$id.').');
		}
	}	

	/**
	 * Inserts data from the provided object into the database.
	 * 
	 * @static
	 * @param wmtList $object
	 * @return null
	 */
	public static function insert(wmtList $object) {
		if($object->id) {
			throw new Exception("wmtList::insert - object already contains identifier");
		}

		// add generic record
		$object->date = date('Y-m-d H:i:s');
		$object->title = ($object->title)? $object->title : 'undefined';
		$object->user = $_SESSION['authUser'];
		$object->groupname = $_SESSION['authProvider'];
		$object->activity = 1;
		
		// build sql insert from object
		$query = '';
		$params = array();
		$fields = self::listFields();
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields) || $key == 'id') continue;
			if ($value == 'YYYY-MM-DD') continue;
			if ($key == 'units' && $value == '') $value = "0";
			if ($key == 'fee' && $value == '') $value = "0";
			
			$query .= ($query)? ", `$key` = ?" : "`$key` = ?";
			$params[] = ($value == 'NULL')? "" : $value;
		}

		// run the insert		
		$object->id = sqlInsert("INSERT INTO billing SET $query",$params);
		
		return $object->id;
	}

	/**
	 * Updates database with information from the given object.
	 * 
	 * @return null
	 */
	public function update() {
		if(! $this->id) 
			throw new Exception("wmtList::update - object does not contain identifier");
		
		// set appropriate date values
		$title = ($this->title)? $this->title : 'undefined';
		
		// build sql insert from object
		$query = '';
		$params = array($this->id);
		$fields = self::listFields();
		foreach ($object as $key => $value) {
			if (!in_array($key, $fields) || $key == 'id') continue;
			if ($value == 'YYYY-MM-DD') continue;
			if ($key == 'units' && $value == '') $value = "0";
			if ($key == 'fee' && $value == '') $value = "0";
			
			$query .= ($query)? ", `$key` = ?" : "`$key` = ?";
			$params[] = ($value == 'NULL')? "" : $value;
		}
		sqlStatement("UPDATE lists SET $query WHERE id = ?",$params);
		
		return;
	}

	/**
	 * Returns a list of record identifiers associated with the
	 * given PID and optionally a given TYPE. If no TYPE is given
	 * then all list record for the PID are returned.
	 * 
	 * @static
	 * @param int $pid patient identifier
	 * @param string $type type of issue to select
	 * @param bool $active restricts results to active items
	 * @return array $itemList list of selected identifiers
	 */
	public static function listPidItems($pid, $type=FALSE, $active=TRUE) {
		if (!$pid) return FALSE;

		$query = "SELECT * FROM lists ";
		$query .= "WHERE pid = ? ";
		if ($active) $query .= "AND enddate IS NULL AND returndate IS NULL ";
		if ($type) $query .= "AND type = ? ";
		$query .= "ORDER BY type, date, id";

		$results = sqlStatement($query,array($pid,$type));
	
		$itemList = array();
		while ($data = sqlFetchArray($results)) {
			$itemList[] = $data['id'];
		}
		
		return $itemList;
	}

	/**
	 * Returns an array list objects associated with the
	 * given PID and optionally a given TYPE. If no TYPE is given
	 * then all issues for the PID are returned.
	 * 
	 * @static
	 * @param int $pid patient identifier
	 * @param string $type type of list to select
	 * @param bool $active active items only flag
	 * @return array $objectList list of selected list objects
	 */
	public static function fetchPidItems($pid, $type=FALSE, $active=TRUE) {
		if (!$pid) return FALSE;

		$query = "SELECT * FROM lists ";
		$query .= "WHERE pid = ? ";
		if ($active) $query .= "AND enddate IS NULL AND returndate IS NULL ";
		if ($type) $query .= "AND type = ? ";
		$query .= "ORDER BY type, date, id";

		$results = sqlStatement($query,array($pid,$type));
	
		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtList($data['id']);
		}
		
		return $objectList;
	}

	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function linkEncounter($id, $pid, $encounter) {
		if (!$pid || !$encounter || !$id) {
			throw new Exception('wmtList::linkEncounter - missing required data elements');
		}

		// remove old links
		sqlStatement("DELETE FROM issue_encounter WHERE " .
  			"pid = ? AND encounter = ? AND list_id = ? ", array($pid,$encounter,$id));
		
		// add new link
		$query = "INSERT INTO issue_encounter SET ";
		$query .= "pid = ?, list_id = ?, encounter = ? ";
    	sqlStatement ($query,array($pid,$id,$encounter));
		
		return;
	}

	/**
	 * Returns an array list objects associated with the
	 * given ENCOUNTER and optionally a given TYPE. If no TYPE is given
	 * then all issues for the ENCOUNTER are returned.
	 *
	 * @static
	 * @param int $encounter encounter identifier
	 * @param string $type type of list to select
	 * @param bool $active active items only flag
	 * @return array $objectList list of selected list objects
	 */
	public static function fetchEncounterList($encounter, $type=FALSE, $active=TRUE) {
		if (!$encounter) return FALSE;
	
		$query = "SELECT lists.id FROM lists ";
		$query .= "LEFT JOIN issue_encounter ie ON ie.list_id = lists.id ";
		$query .= "WHERE ie.encounter = ? ";
		if ($active) $query .= "AND lists.enddate IS NULL AND lists.returndate IS NULL ";
		if ($type) $query .= "AND lists.type = ? ";
		$query .= "ORDER BY lists.type, lists.date, lists.id";
	
		$results = sqlStatement($query,array($encounter,$type));
	
		$objectList = array();
		while ($data = sqlFetchArray($results)) {
			$objectList[] = new wmtList($data['id']);
		}
	
		return $objectList;
	}
	
	/**
	 * Returns a single list object associated with the
	 * given ENCOUNTER and optionally a given TYPE. If no TYPE 
	 * is given then nothing is returned.
	 *
	 * @static
	 * @param int $encounter encounter identifier
	 * @param string $type type of list to select
	 * @param bool $active active items only flag
	 * @return $object list object
	 */
	public static function fetchEncounterItem($encounter, $type=FALSE, $active=TRUE) {
		if (!$encounter || !$type) return FALSE;
	
		$query = "SELECT lists.id FROM lists ";
		$query .= "LEFT JOIN issue_encounter ie ON ie.list_id = lists.id ";
		$query .= "WHERE ie.encounter = ? ";
		if ($active) $query .= "AND lists.enddate IS NULL AND lists.returndate IS NULL ";
		$query .= "AND lists.type = ? ";
		$query .= "ORDER BY lists.type, lists.date, lists.id ";
		$query .= "LIMIT 1";
	
		$data = sqlQuery($query,array($encounter,$type));

		return new wmtList($data['id']);
	}
	
}

/** 
 * Provides standardized error reporting helper functions for the 'errors'
 * database table.
 *
 * @package WMT
 * @subpackage wmtIssues
 */
class wmtIssue {
	public $id;
	public $date;
	public $type;
	public $title;
	public $begdate;
	public $enddate;
	public $returndate;
	public $occurrence;
	public $classification;
	public $referredby;
	public $extrainfo;
	public $diagnosis;
	public $reaction;
	public $activity;
	public $comments;
	public $pid;
	public $user;
	public $groupname;
	public $outcome;
	
	/**
	 * Constructor for the 'error' class which retrieves the requested 
	 * error record from the database of creates an empty object.
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public function __construct($id = FALSE) {
		if(!$id) return false;

		$query = "SELECT * FROM lists WHERE id = $id";

		$results = sqlStatement($query);
	
		if ($data = sqlFetchArray($results)) {
			$this->id = $data['id'];
			$this->date = $data['date'];
			$this->type = $data['type'];
			$this->title = $data['title'];
			$this->begdate = ($data['begdate'])? date('Y-m-d', strtotime($data['begdate'])) : '';
			$this->enddate = ($data['enddate'])? date('Y-m-d', strtotime($data['enddate'])) : '';
			$this->returndate = ($data['returndate'])? date('Y-m-d', strtotime($data['enddate'])) : '';
			$this->occurrence = $data['occurrence'];
			$this->classification = $data['classification'];
			$this->referredby = $data['referredby'];
			$this->extrainfo = $data['extrainfo'];
			$this->diagnosis = $data['diagnosis'];
			$this->reaction = $data['reaction'];
			$this->activity = $data['activity'];
			$this->comments = $data['comments'];
			$this->pid = $data['pid'];
			$this->user = $data['user'];
			$this->groupname = $data['groupname'];
			$this->outcome = $data['outcome'];
		}
		else {
			throw new Exception('wmtIssue::_construct - no issue record with id ('.$id.').');
		}
	}	

	/**
	 * Inserts data from an error object into the database.
	 * 
	 * @static
	 * @param Errors $iderror_object
	 * @return null
	 */
	public static function insert(wmtIssue $object) {
		if($object->id) {
			throw new Exception("wmtIssue::insert - object already contains identifier");
		}

		// add generic diagnosis record
		$begdate = ($object->begdate) ? "'$object->begdate'" : "NULL";
		$enddate = ($object->enddate) ? "'$object->enddate'" : "NULL";
		$returndate = ($object->returndate) ? "'$object->returndate'" : "NULL";
		
		$title = ($object->title)? $object->title : 'ICD9:'.$object->diagnosis;
		
		$object->id = sqlInsert("INSERT INTO lists SET " .
			"date = NOW(), " .
			"type = 'medical_problem', " .
			"title = '$title', " .
			"begdate = $begdate, " .
			"enddate = $enddate, " .
			"returndate = $returndate, " .
			"occurrence = '$object->occurrence', " .
			"classification = '$object->classification', " .
			"referredby = '$object->referredby', " .
			"extrainfo = '$object->extrainfo', " .
			"reaction = '$object->reaction', " .
			"diagnosis = '$object->diagnosis', " .
			"activity = '$object->activity', " . 
			"comments = '$object->comments', " .
			"pid = '$object->pid', " .
			"user = '".$_SESSION['authUser']."', " .
			"groupname = '".$_SESSION['authProvider']."', " .
			"outcome = '$object->outcome'");
		
		return $object->id;
	}

	/**
	 * Inserts data from an error object into the database.
	 * 
	 * @static
	 * @param Errors $iderror_object
	 * @return null
	 */
	public function update() {
		// update generic diagnosis record
		$begdate = ($this->begdate) ? "'$this->begdate'" : "NULL";
		$enddate = ($this->enddate) ? "'$this->enddate'" : "NULL";
		$returndate = ($this->returndate) ? "'$this->returndate'" : "NULL";
		
		$title = ($this->title)? $this->title : 'ICD9:'.$this->diagnosis;
		
		sqlInsert("UPDATE lists SET " .
			"title = '$title', " .
			"begdate = $begdate, " .
			"enddate = $enddate, " .
			"returndate = $returndate, " .
			"occurrence = '$this->occurrence', " .
			"classification = '$this->classification', " .
			"referredby = '$this->referredby', " .
			"extrainfo = '$this->extrainfo', " .
			"diagnosis = '$this->diagnosis', " .
			"reaction = '$this->reaction', " .
			"activity = '$this->activity', " . 
			"comments = '$this->comments', " .
			"pid = '$this->pid', " .
			"user = '".$_SESSION['authUser']."', " .
			"groupname = '".$_SESSION['authProvider']."', " .
			"outcome = '$this->outcome' " .
			"WHERE id = $this->id ");
		
		return;
	}

	/**
	 * Returns a list of issues identifiers associated with the
	 * given PID and optionally a given TYPE. If no TYPE is given
	 * then all issues for the PID are returned.
	 * 
	 * @static
	 * @param int $pid patient identifier
	 * @param string $type type of issue to select
	 * @return array $issList list of selected issue identifiers
	 */
	public static function listPidIssues($pid, $type=FALSE) {
		if (!$pid) return FALSE;

		$query = "SELECT * FROM lists ";
		$query .= "WHERE pid = $pid AND enddate IS NULL AND returndate IS NULL ";
		if ($type) $query = "AND type = '$type' ";
		$query .= "ORDER BY type, date, id";

		$results = sqlStatement($query);
	
		$isuList = array();
		while ($data = sqlFetchArray($results)) {
			$isuList[] = $data['id'];
		}
		
		return $isuList;
	}

	/**
	 * Returns an array issue objects associated with the
	 * given PID and optionally a given TYPE. If no TYPE is given
	 * then all issues for the PID are returned.
	 * 
	 * @static
	 * @param int $pid patient identifier
	 * @param string $type type of issue to select
	 * @return array $issList list of selected issue identifiers
	 */
	public static function fetchPidIssues($pid, $type=FALSE) {
		if (!$pid) return FALSE;

		$query = "SELECT id FROM lists ";
		$query .= "WHERE pid = $pid AND enddate IS NULL AND returndate IS NULL ";
		if ($type) $query .= "AND type = '$type' ";
		$query .= "ORDER BY type, date, id";

		$results = sqlStatement($query);
	
		$isuList = array();
		while ($data = sqlFetchArray($results)) {
			$isuList[] = new wmtIssue($data['id']);
		}
		
		return $isuList;
	}
	
	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function linkEncounter($pid, $encounter, $issue) {
		if (!$pid || !$encounter || !issue) {
			throw new Exception('wmtIssue::linkEncounter - missing required data elements');
		}

		// remove old links
		sqlStatement("DELETE FROM issue_encounter WHERE " .
  			"pid = '$pid' AND encounter = '$encounter' AND list_id = '$issue' ");
		
		// add new link
		$query = "INSERT INTO issue_encounter SET ";
		$query .= "pid = '$pid', list_id = '$issue', encounter = '$encounter' ";
	    sqlStatement ($query);
		
		return;
	}

	/**
	 *
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function unlinkEncounter($pid, $encounter, $issue) {
		if (!$pid || !$encounter || !issue) {
			throw new Exception('wmtIssue::unlinkEncounter - missing required data elements');
		}
	
		// remove old links
		sqlStatement("DELETE FROM issue_encounter WHERE " .
				"pid = '$pid' AND encounter = '$encounter' AND list_id = '$issue' ");
	
		return;
	}
	
	/**
	 * Returns an array issue objects associated with the
	 * given PID and optionally a given TYPE. If no TYPE is given
	 * then all issues for the PID are returned.
	 * 
	 * @static
	 * @param int $pid patient identifier
	 * @param string $type type of issue to select
	 * @return array $issList list of selected issue identifiers
	 */
	public static function fetchLinkedIssues($pid, $encounter, $type=FALSE) {
		if (!$pid) return FALSE;

		$query = "SELECT id, encounter FROM lists ";
		$query .= "LEFT JOIN issue_encounter ie ON encounter = '$encounter' AND list_id = lists.id ";
		$query .= "WHERE type = 'medical_problem' AND lists.pid = $pid AND enddate IS NULL AND returndate IS NULL ";
		if ($type) $query .= "AND type = '$type' ";
		$query .= "ORDER BY type, date, id";

		$results = sqlStatement($query);
	
		$isuList = array();
		while ($data = sqlFetchArray($results)) {
			$issue = new wmtIssue($data['id']);
			$isuList[] = array($issue,$data['encounter']); // encounter empty if not linked
		}
		
		return $isuList;
	}
	
	/**
	 * Build a table to display current issues.
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function issueTable($pid, $category, $active, $limit = false) {
		$issues = wmtIssue::fetchPidIssues($pid, $category, $active);
		$call_type = ($category == 'medical_problem')? 'issue' : $category;
		
		$output = "<table><tr>\n";
		if ($category == 'medical_problem') $output .= "<th class=\"wmtHeader\" style=\"width:20%\">Issue</th>\n";
		if ($category == 'allergy') $output .= "<th class=\"wmtHeader\" style=\"width:20%\">Allergy</th>\n";
		if ($category == 'medication') $output .= "<th class=\"wmtHeader\" style=\"width:20%\">Medication</th>\n";
		if ($category != 'medication') $output .= "<th class=\"wmtHeader\" style=\"width:100px;min-width:100px\">Began</th>\n";
		if ($category == 'medical_problem') $output .= "<th class=\"wmtHeader\" style=\"min-width:90px\">Diagnosis</th>\n";
		if ($category == 'medication') $output .= "<th class=\"wmtHeader\" style=\"min-width:100px\">Quantity</th>\n";
		if ($category == 'medication') $output .= "<th class=\"wmtHeader\" style=\"min-width:120px\">Dosage</th>\n";
		if ($category == 'allergy') $output .= "<th class=\"wmtHeader\" style=\"min-width:120px\">Reaction</th>\n";

		$output .= "<th class=\"wmtHeader\" style=\"width:50%\">Comments</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:30px\">Action</th>\n";
		$output .= "</tr>\n";
	
		if (count($issues) > 0) {
			foreach ($issues as $issue) {
				$output .= "<tr class=\"wmtLabel\" style=\"vertical-align:top\">";
				$output .= "<td>".$issue->title."</td>\n";
				if ($category != 'medication') $output .= "<td>".$issue->begdate."</td>\n";
				if ($category == 'medical_problem') $output .= "<td>".$issue->diagnosis."</td>\n";
				if ($category == 'medication') $output .= "<td>".$issue->quantity."</td>\n";
				if ($category == 'medication') $output .= "<td>".$issue->dosage."</td>\n";
				if ($category == "allergy") $output .= "<td>".$issue->reaction."</td>\n";
				if ($limit && $issue->comments) $issue->comments = substr_replace($issue->comments, '...', 60);

				if ($category == 'medical_problem') $output .= "<td style=\"padding-right:20px\">" . $issue->extrainfo."</td>\n";
				if ($category != 'medical_problem') $output .= "<td style=\"padding-right:20px\">" . $issue->comments."</td>\n";
				$output .= "<td><a class='iframe css_button_small' href='".$GLOBALS['webroot']."/library/wmt/edit_".$call_type.".php?issue=".$issue->id."'>";
				$output .= "<span>Edit</span></a></td>";
				$output .= "</tr>\n";
			}
		}
		else { // no issues
			$output .= "<tr class=\"wmtLabel\">\n";
			$output .= "<td colspan=\"4\">None on file</td>\n";
			$output .= "</tr>\n";
				
		}
		
		$output .= "</table>\n";
		return $output;
	}

	/**
 	* Build a table to display related issues.
	*
 	* @param int $id lists record identifier
	* @return object instance of lists class
 	*/
	public static function issuesRelated($pid, $encounter, $limit = false) {
		$issues = wmtIssue::fetchLinkedIssues($pid, $encounter, $active);

		$output = "<table><tr>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:20%\">Issue</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:100px;min-width:100px\">Began</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"min-width:90px\">Diagnosis</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:50%\">Comments</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:30px\">Linked</th>\n";
		$output .= "</tr>\n";

		if (count($issues) > 0) {
			foreach ($issues as $issue) {
				$output .= "<tr class=\"wmtLabel\" style=\"vertical-align:top\">";
				$output .= "<td>".$issue[0]->title."</td>\n";
				$output .= "<td>".$issue[0]->begdate."</td>\n";
				$output .= "<td>".$issue[0]->diagnosis."</td>\n";
				if ($limit && $issue[0]->comments) $issue[0]->comments = substr_replace($issue[0]->comments, '...', 60);

				$output .= "<td style=\"padding-right:20px\">".$issue[0]->extrainfo."</td>\n";
				$output .= "<td>&nbsp;&nbsp;<input type='checkbox' value='".$issue[0]->id."' name='issues[]' ";
				$output .= ($issue[1])? "checked " : " ";
				$output .= "/></td>";
				$output .= "</tr>\n";
			}
		}
		else { // no issues
			$output .= "<tr class=\"wmtLabel\">\n";
			$output .= "<td colspan=\"4\">None on file</td>\n";
			$output .= "</tr>\n";
		}

		$output .= "</table>\n";
		return $output;
	}
}


/** 
 * Provides standardized error reporting helper functions for the 'errors'
 * database table.
 *
 * @package Dermatology
 * @subpackage Diagnosis
 */
class wmtDiagnosis extends wmtIssue {
	public $dx_id;
	public $dx_date;
	public $dx_pid;
	public $dx_list_id;
	public $dx_form_name;
	public $dx_form_id;
	public $dx_form_title;
	
	/**
	 * Constructor for the 'error' class which retrieves the requested 
	 * error record from the database of creates an empty object.
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public function __construct($id = false) {
		if(!$id) return false;

		// get standard part of the issue
		try {
			parent::__construct($id);
		}
		catch (Exception $e) {
			// should log this as an error
			$this->id = NULL;
		}
				
		// try and retrieve extended issue (with diagnosis)
		$query = "SELECT * FROM form_derm_dx_issue WHERE list_id = $id";
		$results = sqlStatement($query);

		// check for child data
		if ($data = sqlFetchArray($results)) {
			if (!$this->id) {
				// found child with no parent, get rid of child
				sqlStatement("DELETE FROM form_derm_dx_issue WHERE id = ".$data['id']);
			}
			else {
				// add child data to object
				$this->dx_id = $data['id'];
				$this->dx_date = $data['date'];
				$this->dx_pid = $data['pid'];
				$this->dx_list_id = $data['list_id'];
				$this->dx_form_name = $data['form_name'];
				$this->dx_form_id = $data['form_id'];
				$this->dx_form_title = $data['form_title'];
			}
		}
	}
		
	/**
	 * Inserts data from an error object into the database.
	 * 
	 * @static
	 * @param Errors $iderror_object
	 * @return null
	 */
	public static function insert(wmtDiagnosis $object) {
		if($object->id) {
			throw new Exception("wmtDiagnosis::insert - object already contains identifier");
		}

		// insert parent record first
		$parent_id = parent::insert($object);
		
		// add generic diagnosis record
		$enc_date = ($object->date) ? "'$object->date'" : "NULL";

		$object->id = sqlInsert("INSERT INTO form_derm_dx_issue SET " .
			"date = '$enc_date', " .
			"pid = '$object->pid', " .
			"list_id = '$parent_id', " .
			"form_name = '$object->dx_form_name', " .
			"form_title = '$object->dx_form_title'");
		
		return $parent_id;
	}

	/**
	 * Delete diagnosis record from the database and unlink.
	 * 
	 * @static
	 * @param Errors $iderror_object
	 * @return null
	 */
	public static function delete($id) {
		if(!$id) {
			throw new Exception("wmtDiagnosis::delete - no identifier provided");
		}

		// insert parent record first
		$parent_id = parent::insert($object);
		
		// add generic diagnosis record
		$enc_date = ($object->date) ? "'$object->date'" : "NULL";

		$object->id = sqlInsert("INSERT INTO form_derm_dx_issue SET " .
			"date = '$enc_date', " .
			"pid = '$object->pid', " .
			"list_id = '$parent_id', " .
			"form_name = '$object->dx_form_name', " .
			"form_title = '$object->dx_form_title'");
		
		return $parent_id;
	}

	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function linkSingle($pid, $encounter, $issue) {
		if (!$pid || !$encounter || !issue) {
			throw new Exception('wmtDiagnosis::linkSingle - missing required data elements');
		}

		// remove old links
		sqlStatement("DELETE FROM issue_encounter WHERE " .
  			"pid = '$pid' AND encounter = '$encounter' AND list_id = '$issue' ");
		
		// add new link
		$query = "INSERT INTO issue_encounter SET ";
		$query .= "pid = '$pid', list_id = '$issue', encounter = '$encounter' ";
	    sqlStatement ($query);
		
		return;
	}

	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function linkDiagnosis($pid, $encounter, $issues) {
		if (!$pid || !$encounter || !is_array($issues)) {
			throw new Exception('wmtDiagnosis::linkDiagnosiss - missing required data elements');
		}

		// remove old links
		sqlStatement("DELETE FROM issue_encounter WHERE " .
  			"pid = '$pid' AND encounter = '$encounter'");
		
		// add new links
		foreach ($issues as $issue) {
			$query = "INSERT INTO issue_encounter SET ";
			$query .= "pid = '$pid', list_id = '$issue', encounter = '$encounter' ";
		    sqlStatement ($query);
		}
		
		return;
	}

	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function getDiagnosis($pid) {
		if (!$pid) return FALSE;

		$query = "SELECT l.id, ie.encounter FROM issue_encounter ie ";
		$query .= "LEFT JOIN lists l ON ie.list_id = l.id ";
		$query .= "WHERE ie.pid = $pid AND l.enddate IS NULL AND l.returndate IS NULL ";
		$query .= "ORDER BY l.date, l.id";

		$results = sqlStatement($query);
	
		$txList = array();
		while ($data = sqlFetchArray($results)) {
			$txList[] = array('id' => $data['id'], 'encounter' => $data['encounter']);
		}
		
		return $txList;
	}
	
	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public function getTxEncounter($encounter) {
		if (!$encounter) {
			throw new Exception('wmtDiagnosis::getTxEncounter - no encounter identifier provided');
		}

		$query = "SELECT l.id FROM issue_encounter ie ";
		$query .= "LEFT JOIN lists l ON ie.list_id = l.id ";
		$query .= "WHERE ie.pid = $pid AND l.enddate IS NULL AND l.returndate IS NULL ";
		$query .= "AND ie.encounter = '$this->encounter' ";
		$query .= "ORDER BY l.date, l.id";

		$results = sqlStatement($query);
	
		$txList = array();
		while ($data = sqlFetchArray($results)) {
			$txList[] = new wmtDiagnosis($data['id']);
		}
		
		return $txList;
	}

	/**
	 * 
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public function getTxForm($form_id) {
		if (!$form_id) {
			throw new Exception('wmtDiagnosis::getTxForm - no form identifier provided');
		}

		$query = "SELECT dx.list_id FROM form_derm_dx_issue dx ";
		$query .= "LEFT JOIN lists l ON dx.list_id = l.id ";
		$query .= "WHERE dx.form_id = $form_id AND l.enddate IS NULL AND l.returndate IS NULL ";
		$query .= "ORDER BY l.date, l.id";

		$results = sqlStatement($query);
	
		$txList = array();
		while ($data = sqlFetchArray($results)) {
			$txList[] = $data['id'];
		}
		
		return $txList;
	}

	/**
	 * Build a table to display encounter issues.
	 *
	 * @param int $id lists record identifier
	 * @return object instance of lists class
	 */
	public static function diagnosisTable($encounter) {
		$items = wmtList::fetchEncounterList($encounter, 'medical_problem', true);
	
		$output = "<center><table><tr>\n";
		$output .= "<th class=\"wmtHeader\" style=\"min-width:90px;width:90px\">Diagnosis</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:90px;min-width:90px;\">Start Date</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:90px;min-width:90px;\">End Date</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:25%\">Title</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:50%\">Description</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"padding-left:12px;width:40px\">Action</th>\n";
		$output .= "</tr>\n";

		$idx = 1;
		if (count($items) > 0) {
			foreach ($items as $issue) {
				$output .= "<tr class=\"wmtLabel\" style=\"vertical-align:top\">\n";
				$output .= "<td>".str_replace(';','<br/>',$issue->diagnosis)."</td>\n";
				$output .= "<td>".$issue->begdate."</td>\n";
				$output .= "<td>".$issue->enddate."</td>\n";
				$output .= "<td>".$issue->title."</td>\n";
				$output .= "<td>".$issue->extrainfo."</td>\n";
				$output .= "<td style=\"padding-left:12px;width:40px\"><a class='iframe css_button_small' href='".$GLOBALS['rootdir']."/../library/wmt/edit_issue.php?issue=".$issue->id."'>";
				$output .= "<span>Edit</span></a></td>";
				$output .= "</tr>\n";
				if ($issue->comments) {
					$output .= "<tr><td></td><td colspan=4 class=\"wmtOutput\" style=\"padding-bottom:10px;white-space:pre-wrap\"><span class=\"wmtHeader\">Plan Of Care:</span><br/><b>".$issue->comments."</b></td><td></td></tr>\n";
				}
				else {
					$output .= "<tr><td></td><td colspan=4 class=\"wmtOutput\" style=\"padding-bottom:10px;white-space:pre-wrap\"><span class=\"wmtHeader\">Plan Of Care:</span><br/><b>NO PLAN DEFINED</b></td><td></td></tr>\n";
				}
				
			}
		}
		else { // no issues
			$output .= "<tr class=\"wmtLabel\">\n";
			$output .= "<td colspan=\"7\">None on file</td>\n";
			$output .= "</tr>\n";
	
		}
	
		$output .= "</table>\n";
		return $output;
	}

	public static function diagnosisReport($encounter) {
		$items = wmtList::fetchEncounterList($encounter, 'medical_problem', true);
	
		$output = "<tr>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:120px\">Diagnosis</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:90px;min-width:90px;\">Start Date</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:90px;min-width:90px;\">End Date</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:25%\">Title</th>\n";
		$output .= "<th class=\"wmtHeader\" style=\"width:50%\">Description</th>\n";
		$output .= "</tr>\n";

		$idx = 1;
		if (count($items) > 0) {
			foreach ($items as $issue) {
				$output .= "<tr class=\"wmtDetail\" style=\"vertical-align:top\">\n";
				$output .= "<td>".str_replace(';','<br/>',$issue->diagnosis)."</td>\n";
				$output .= "<td>".$issue->begdate."</td>\n";
				$output .= "<td>".$issue->enddate."</td>\n";
				$output .= "<td>".$issue->title."</td>\n";
				$output .= "<td>".$issue->extrainfo."</td>\n";
				$output .= "</tr>\n";
				if ($issue->comments) {
					$output .= "<tr><td></td><td colspan=4 class=\"wmtDetail\" style=\"padding-bottom:10px;white-space:pre-wrap\"><span class=\"wmtHeader\">Plan Of Care:</span><br/>".$issue->comments."</td><td></td></tr>\n";
				}
				else {
					$output .= "<tr><td></td><td colspan=4 class=\"wmtDetail\" style=\"padding-bottom:10px;white-space:pre-wrap\"><span class=\"wmtHeader\">Plan Of Care:</span><br/>NO PLAN DEFINED</td><td></td></tr>\n";
				}
				
			}
		}
		else { // no issues
			$output .= "<tr class=\"wmtDetail\">\n";
			$output .= "<td colspan=\"7\">None on file</td>\n";
			$output .= "</tr>\n";
	
		}
	
		return $output;
	}

}
		
/**
 */
class wmtOption {
	public $list_id;
	public $option_id;
	public $title;
	public $seq;
	public $is_default;
	public $option_value;
	public $mapping;
	public $notes;

	/**
	 * Constructor for the 'option' class which retrieves the requested
	 * list_option record from the database or creates an empty object.
	 *
	 * @param int $id option record identifier
	 * @return object instance of category class
	 */
	public function __construct($type,$id) {
		if(!$id || !$type) return false;

		$query = "SELECT * FROM list_options ";
		$query .= "WHERE option_id = '$id' AND list_id = '$type' ";
		$results = sqlStatement($query);

		if ($data = sqlFetchArray($results)) {
			$this->list_id = $data['list_id'];
			$this->option_id = $data['option_id'];
			$this->title = $data['title'];
			$this->seq = $data['seq'];
			$this->is_default = $data['is_default'];
			$this->option_value = $data['option_value'];
			$this->mapping = $data['mapping'];
			$this->notes = $data['notes'];
		}
		else {
			throw new Exception('wmtOption::_construct - no list option record with id ('.$this->id.').');
		}
	}

	/**
	 * Returns an array of category data which may optionally be limited to
	 * only those records which are displayable.
	 *
	 * @static
	 * @param boolean $display include only displayable categories
	 * @return object instance of lists class
	 */
	public static function fetchOptions($type) {
		if (! $type) return false;

		$query = "SELECT option_id FROM list_options ";
		$query .= "WHERE list_id = '$type' ";
		$query .= "ORDER BY seq";

		$results = sqlStatement($query);

		$optList = array();
		while ($data = sqlFetchArray($results)) {
			$optList[] = new wmtOption($type,$data['option_id']);
		}

		return $optList;
	}
}


?>