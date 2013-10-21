<?php
//300 seconds = 5 minutes execution time
ini_set('max_execution_time', 300);
// overrides the default PHP memory limit.
ini_set('memory_limit', '-1');

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
$dataArray = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
// use insert function
$q = $db->insert('test',$dataArray)->showQuery()->getLastInsertId();
PDOHelper::PA($q);


// Example -2 
$dataArray = array('first_name'=>'Scott','last_name'=>'Dimon','age'=>55);
// use insert function
$q = $db->insert('test',$dataArray)->showQuery()->getLastInsertId();
PDOHelper::PA($q);


// Example -3 
$dataArray = array('first_name'=>'Simran','last_name'=>'Singh','age'=>25);
// use insert function
$q = $db->insert('testt',$dataArray)->showQuery()->getLastInsertId();
PDOHelper::PA($q);


// Example -4 
// use insert function
$q = $db->insert('test',$dataArray)->showQuery()->getLastInsertId();
// print array last insert id
PDOHelper::PA($q);


// Example -5 (Bulk Insert)
// loop start to create insert data
for ($i = 0; $i < 1000000; $i++) {
    $dataArray[] = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
    $dataArray[] = array('first_name'=>'Scott','last_name'=>'Dimon','age'=>78);
    $dataArray[] = array('first_name'=>'Meena','last_name'=>'Verma','age'=>23);
}
// use insertBatch function to insert multiple row at once and get all last insert id in array
$q = $db->insertBatch('test',$dataArray, true)->showQuery()->getAllLastInsertId();
// print array last insert id
PDOHelper::PA($q);