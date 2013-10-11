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

// select query #1
$q = $p->pdoQuery('select * from customers limit 5;')->traceEnable()->results();
PA($q);

// select query with where condition and bind param #2
$q = $p->pdoQuery('select * from customers where customernumber = ?;',array(103))->traceEnable()->results();
// print array result
PA($q);

// select query and get 2nd row of result array #3
$q = $p->pdoQuery('select * from customers;')->traceEnable()->result(2);
// print array result
PA($q);

// select query with multiple condition and bind param #4
$q = $p->pdoQuery('select * from customers where (customernumber = ? or contactLastName = ?) ;',array(112,'Schmitt'))->traceEnable()->results();
// print array result
PA($q);