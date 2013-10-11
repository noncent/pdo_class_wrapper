<?php

/**
 * PHP INI Run Time Settings
 */
ini_set('max_execution_time', 300); //300 seconds = 5 minutes execution time
ini_set('memory_limit', '-1'); // overrides the default PHP memory limit.


/**
 * Function to print array with pre tag
 *
 * @param array $array
 */
function PA($array){
	echo '<pre>',print_r($array, true),'</pre>';
}

// include pdo class wrapper
include_once 'class.pdowrapper.php';

// create new object of class wrapper
$p = new PdoWrapper();

//insert example #1
$dataArray = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
// use insert function
$q = $p->insert('test',$dataArray)->traceEnable()->getLastInsertId();
$dataArray = array('first_name'=>'Scott','last_name'=>'Dimon','age'=>55);
// use insert function
$q = $p->insert('test',$dataArray)->traceEnable()->getLastInsertId();
$dataArray = array('first_name'=>'Simran','last_name'=>'Singh','age'=>25);
// use insert function
$q = $p->insert('test',$dataArray)->traceEnable()->getLastInsertId();
// use insert function
$q = $p->insert('test',$dataArray)->traceEnable()->getLastInsertId();
// print array last insert id
PA($q); die;

//insert example (bulk insert) #2
/********************************************/
/* GET Execution Time To check Bulk Insert  */
/********************************************/
//place this before any script you want to calculate time
$time_start = microtime(true); 
// loop start to create insert data
for ($i = 0; $i < 1000000; $i++) {
	$dataArray[] = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
}
// use insertBatch function to insert multiple row at once
$q = $p->insertBatch('test',$dataArray, true)->traceEnable()->getLastInsertId();
// get end time
$time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start)/60;
//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
// print array last insert id
PA($q);