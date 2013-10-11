<?php
/**
 * PHP INI Run Time Settings
 */
 
//300 seconds = 5 minutes execution time
ini_set('max_execution_time', 300);
// overrides the default PHP memory limit.
ini_set('memory_limit', '-1');


// include pdo helper class to use common methods
include_once '../class/class.pdohelper.php';
// include pdo class wrapper
include_once '../class/class.pdowrapper.php';

// create new object of class wrapper
$pdodo = new PdoWrapper();
// set error log mode
$pdodo->setErrorLog(true);

//insert example #1
$dataArray = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
// use insert function
$q = $pdo->insert('test',$dataArray)->showQuery()->getLastInsertId();


$dataArray = array('first_name'=>'Scott','last_name'=>'Dimon','age'=>55);
// use insert function
$q = $pdo->insert('test',$dataArray)->showQuery()->getLastInsertId();


$dataArray = array('first_name'=>'Simran','last_name'=>'Singh','age'=>25);
// use insert function
$q = $pdo->insert('testt',$dataArray)->showQuery()->getLastInsertId();


// use insert function
$q = $pdo->insert('test',$dataArray)->showQuery()->getLastInsertId();
// print array last insert id
PA($q);


//insert example (bulk insert) #2
/********************************************/
/* GET Execution Time To check Bulk Insert  */
/********************************************/
//place this before any script you want to calculate time
$time_start = microtime(true);
// loop start to create insert data
for ($i = 0; $i < 1000000; $i++) {
    $dataArray[] = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
    $dataArray[] = array('first_name'=>'Scott','last_name'=>'Dimon','age'=>78);
    $dataArray[] = array('first_name'=>'Meena','last_name'=>'Verma','age'=>23);
}
// use insertBatch function to insert multiple row at once and get all last insert id in array
$q = $pdo->insertBatch('test',$dataArray, true)->showQuery()->getAllLastInsertId();
// get end time
$time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start)/60;
//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
// print array last insert id
PA($q);