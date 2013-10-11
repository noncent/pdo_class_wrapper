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


echo "// select with fields and one where condition #1";
// set fields for table
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array('customerNumber'=>103);
// select with where and bind param use select method
$q = $p->select('customers',$selectFields,$whereConditions)->traceEnable()->results();
// print array result
PA($q);


echo "// select with fields and two where condition #2";
// set fields for table
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array('customerNumber'=>103,'contactLastName'=> 'Schmitt');
// select with where and bind param use select method
$q = $p->select('customers',$selectFields,$whereConditions)->traceEnable()->results();
// print array result
PA($q);


echo "// select with fields and LIMIT clause condition #3";
// set fields for table
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array();
// select with where and bind param use select method
$q = $p->select('customers',$selectFields,$whereConditions, 'LIMIT 10')->traceEnable()->results();
// print array result
PA($q);


echo "// select with fields and ORDER clause condition #4";
// set fields for table
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array();
// select with where and bind param use select method
$q = $p->select('customers',$selectFields,$whereConditions, 'ORDER BY customerNumber DESC LIMIT 5')->traceEnable()->results();
// print array result
PA($q);