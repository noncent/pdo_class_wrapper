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

// simple delete #1

// where condition array
$aWhere = array('age'=>35);
// call update function
$q = $p->delete('test', $aWhere)->traceEnable()->affectedRows();
// print affected rows
PA($q);


// simple delete #2


// where condition array
$aWhere = array('age'=>45, 'first_name'=> 'Sonu');
// call update function
$q = $p->delete('test', $aWhere)->traceEnable()->affectedRows();
// print affected rows
PA($q);