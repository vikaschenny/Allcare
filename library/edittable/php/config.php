<?php if (!defined('DATATABLES')) exit(); // Ensure being used in DataTables env.

// Enable error reporting for debugging (remove for production)
error_reporting(E_ALL);
ini_set('display_errors', '1');


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Database user / pass
 */
$sql_details = array(
	"type" => "Mysql",  // Database type: "Mysql", "Postgres", "Sqlite" or "Sqlserver"
	"user" => "sql-ledger",       // Database user name
	"pass" => "secret",       // Database password
	"host" => "qa2allcare.texashousecalls.com",       // Database host
	"port" => "",       // Database connection port (can be left empty for default)
	"db"   => "sql-ledger",       // Database name
	"dsn"  => ""        // PHP DSN extra information. Set as `charset=utf8` if you are using MySQL
);


