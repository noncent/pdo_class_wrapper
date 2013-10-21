<?php
// include pdo helper class to use common methods
include_once '../class/class.pdohelper.php';
// include pdo class wrapper
include_once '../class/class.pdowrapper.php';

// database connection setings
$dbConfig = array("host"=>"localhost", "dbname"=>'sampledb', "username"=>'root', "password"=>'');
// get instance of PDO Wrapper object
$db = new PdoWrapper($dbConfig);

// get instance of PDO Helper object
$helper = new PDOHelper();

// set error log mode true to show error on screen or false to log in log file
$db->setErrorLog(true);

// Example -1

// update array data
$dataArray = array('first_name'=>'Sangeeta','last_name'=>'Mishra','age'=>35);
// where condition array
$aWhere = array('id'=>23);
// call update function
$q = $p->update('test', $dataArray, $aWhere)->showQuery()->affectedRows();
// print affected rows
PDOHelper::PA($q);




// Example -2

// update array data
$dataArray = array('first_name'=>'Sonia','last_name'=>'Shukla','age'=>23);
// two where condition array
$aWhere = array('age'=>35, 'last_name'=>'Mishra');
// call update function
$q = $p->update('test', $dataArray, $aWhere)->showQuery()->affectedRows();
// print affected rows
PDOHelper::PA($q);
