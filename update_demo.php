<?php

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

// simple update #1

// update array data
$dataArray = array('first_name'=>'Sangeeta','last_name'=>'Mishra','age'=>35);
// where condition array
$aWhere = array('age'=>23);
// call update function
$q = $p->update('test', $dataArray, $aWhere)->traceEnable()->affectedRows();
// print affected rows
PA($q);


// simple update #2

// update array data
$dataArray = array('first_name'=>'Sonia','last_name'=>'Shukla','age'=>23);
// two where condition array
$aWhere = array('age'=>35, 'last_name'=>'Mishra');
// call update function
$q = $p->update('test', $dataArray, $aWhere)->traceEnable()->affectedRows();
// print affected rows
PA($q);