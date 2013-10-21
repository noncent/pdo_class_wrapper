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


/**
 *  run simple mysql query
 *
 *  showQuery = display executed query
 *  results = get array results
 */
$q = $db->pdoQuery('select * from customers limit 5;')->showQuery()->results();
// print array result
$helper->PA($q);


/**
 *  run simple mysql query with where clause
 *  pass where value as an parametrised array
 *
 *  ? presenting place holder here for where clause values
 */
$q = $db->pdoQuery('select * from customers where (customernumber = ? OR customernumber = ?) ;',array(103,119))->showQuery()->results();
// print array result
$helper->PA($q);


/**
 *  run simple mysql query and get third row of array results
 *
 *  result(2) = will return 3rd row of array data
 */
$q = $db->pdoQuery('select * from customers;')->showQuery()->result(2);
// print array result
$helper->PA($q);


/**
 *  run mysql select query with where clause and or using parametrise array param
 */
$q = $db->pdoQuery('select * from customers where (customernumber = ? OR contactLastName = ?) ;',array(112,'Schmitt'))->showQuery()->results();
// print array result
$helper->PA($q);


/**
 *  run mysql select query with where clause and or using parametrise array param
 */
$innerJoinSql = "select p.checknumber, p.amount, p.paymentdate, c.customernumber, c.customerName, c.contactLastName, c.contactFirstName, c.phone, c.addressLine1, c.addressLine2, c.city, c.state, c.postalCode, c.country from payments as p inner join customers as c on p.customernumber = c.customernumber order by p.amount desc limit 2;";

$q = $db->pdoQuery($innerJoinSql)->showQuery()->results();
// print array result
$helper->PA($q);