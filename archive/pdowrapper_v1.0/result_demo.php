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

// select query with limit
$q = $p->pdoQuery('select * from customers;')->results();
// print array result
// PA($q); die;

// select query with limit
$q = $p->pdoQuery('select * from customers;')->results('xml');
// print xml result
// echo $q; die;

// select query with limit
$q = $p->pdoQuery('select * from customers;')->results('json');
// print json result
// echo $q; die;