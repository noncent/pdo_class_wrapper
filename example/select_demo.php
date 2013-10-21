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


// Example -1 
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array('customerNumber'=>103);
// select with where and bind param use select method
$q = $db->select('customers',$selectFields,$whereConditions)->showQuery()->results();
// print array result
PDOHelper::PA($q);

// Example -2 
$whereConditions = array('lastname ='=>'bow', 'or jobtitle ='=> 'Sales Rep', 'and isactive ='=>1, 'and officecode ='=> 1 );
$data = $db->select('employees',array('employeenumber','lastname','jobtitle'),$whereConditions)->showQuery()->results();
// print array result
PDOHelper::PA($q);


// Example -3 
$whereConditions = array('lastname ='=>'bow', 'or jobtitle ='=> 'Sales Rep', 'and isactive ='=>1, 'and officecode ='=> 1 );
// select with where and bind param use select method
$q = $db->select('employees',array('employeeNumber','lastName','firstName'),$whereConditions)->showQuery()->results();
// print array result
PDOHelper::PA($q);


// Example -4 
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array('customerNumber'=>103,'contactLastName'=> 'Schmitt');
$array_data = array(
    'customerNumber ='=>103,
    'and contactLastName ='=> 'Schmitt',
    'and age ='=>30,
    'or contactLastName ='=> 'Schmitt',
    'and age <' => 45,
    'or age >'=> 65
);
// select with where and bind param use select method
$q = $db->select('customers',$selectFields,$array_data);
// print array result
PDOHelper::PA($q);


// Example -5 
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array();
// select with where and bind param use select method
$q = $db->select('customers',$selectFields,$whereConditions, 'LIMIT 10')->showQuery()->results();
// print array result
PDOHelper::PA($q);


// Example -6 
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array();
// select with where and bind param use select method
$q = $db->select('customers',$selectFields,$whereConditions, 'ORDER BY customerNumber DESC LIMIT 5')->showQuery()->results();
// print array result
PA($q);