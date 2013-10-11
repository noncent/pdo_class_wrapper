<?php
// include pdo helper class to use common methods
include_once '../class/class.pdohelper.php';
// include pdo class wrapper
include_once '../class/class.pdowrapper.php';

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