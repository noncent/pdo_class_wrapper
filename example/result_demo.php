<?php
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

// select query with limit
$q = $db->pdoQuery('select * from customers;')->results();
// print array result
$helper->PA($q);

// select query with limit
$q = $db->pdoQuery('select * from customers;')->results('xml');
// print xml result
echo $q;

// select query with limit
$q = $db->pdoQuery('select * from customers;')->results('json');
// print json result
echo $q;