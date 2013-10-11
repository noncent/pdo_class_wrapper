<?php
// include pdo helper class to use common methods
include_once '../class/class.pdohelper.php';
// include pdo class wrapper
include_once '../class/class.pdowrapper.php';

// create new object of class wrapper
$p = new PdoWrapper();
// set error log mode
$p->setErrorLog(true);


// simple update #1

// update array data
$dataArray = array('first_name'=>'Sangeeta','last_name'=>'Mishra','age'=>35);
// where condition array
$aWhere = array('id'=>23);
// call update function
$q = $p->update('test', $dataArray, $aWhere)->showQuery()->affectedRows();
// print affected rows
PDOHelper::PA($q);




// simple update #2

// update array data
$dataArray = array('first_name'=>'Sonia','last_name'=>'Shukla','age'=>23);
// two where condition array
$aWhere = array('age'=>35, 'last_name'=>'Mishra');
// call update function
$q = $p->update('test', $dataArray, $aWhere)->showQuery()->affectedRows();
// print affected rows
PDOHelper::PA($q);