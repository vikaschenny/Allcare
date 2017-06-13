CREATE TABLE IF NOT EXISTS tbl_form_physical_exam (
 forms_id        bigint(20)   NOT NULL,
 line_id         char(8)      NOT NULL,
 wnl             tinyint(1)   NOT NULL DEFAULT 0,
 abn             tinyint(1)   NOT NULL DEFAULT 0,
 diagnosis       varchar(255) NOT NULL DEFAULT '',
 comments        varchar(255) NOT NULL DEFAULT '',
 PRIMARY KEY (forms_id, line_id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS tbl_form_physical_exam_diagnoses (
 line_id         char(8)      NOT NULL,
 ordering        int(11)      NOT NULL DEFAULT 0,
 diagnosis       varchar(255) NOT NULL DEFAULT '',
 KEY (line_id, ordering)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS tbl_allcare_formflag(
id int( 10 ) AUTO_INCREMENT NOT NULL ,
encounter_id  int( 100 ) ,
form_id int(100),
form_name varchar(500) ,
pending   char(1),
finalized char(1),
logdate  text,
KEY ( id )
) ENGINE = MYISAM ;

