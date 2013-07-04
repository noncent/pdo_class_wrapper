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
include_once 'class.pdowrapper_temp.php';

// create new object of class wrapper
$p = new PdoWrapper();