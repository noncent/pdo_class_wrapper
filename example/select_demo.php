<?php
require_once('lib/FirePHPCore/FirePHP.class.php');
ob_start();
$firephp = FirePHP::getInstance(true);

// include pdo helper class to use common methods
include_once '../class/class.pdohelper.php';
// include pdo class wrapper
include_once '../class/class.pdowrapper.php';

// create new object of class wrapper
$pdo = new PdoWrapper();
// set error log mode
$pdo->setErrorLog(true);

echo "// select with fields and one where condition #1";
// set fields for table
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array('customerNumber'=>103);
// select with where and bind param use select method
$q = $pdo->select('customers',$selectFields,$whereConditions)->showQuery()->results();
// print array result
PDOHelper::PA($q);


$whereConditions = array('lastname ='=>'bow', 'or jobtitle ='=> 'Sales Rep', 'and isactive ='=>1, 'and officecode ='=> 1 );
$data = $pdo->select('employees',array('employeenumber','lastname','jobtitle'),$whereConditions)->showQuery()->results();



// set where condition
$whereConditions = array('lastname ='=>'bow', 'or jobtitle ='=> 'Sales Rep', 'and isactive ='=>1, 'and officecode ='=> 1 );
// select with where and bind param use select method
$q = $pdo->select('employees',array('employeeNumber','lastName','firstName'),$whereConditions)->showQuery()->results();
// print array result
PDOHelper::PA($q);


echo "// select with fields and two where condition #2";
// set fields for table
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
$q = $pdo->select('customers',$selectFields,$array_data);
// print array result
PDOHelper::PA($q);


echo "// select with fields and LIMIT clause condition #3";
// set fields for table
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array();
// select with where and bind param use select method
$q = $pdo->select('customers',$selectFields,$whereConditions, 'LIMIT 10')->showQuery()->results();
// print array result
PA($q);


echo "// select with fields and ORDER clause condition #4";
// set fields for table
$selectFields = array('customerNumber','customerName','contactLastName','contactFirstName','phone');
// set where condition
$whereConditions = array();
// select with where and bind param use select method
$q = $pdo->select('customers',$selectFields,$whereConditions, 'ORDER BY customerNumber DESC LIMIT 5')->showQuery()->results();
// print array result
PA($q);