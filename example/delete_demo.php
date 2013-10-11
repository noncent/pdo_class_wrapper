<?php
// include pdo helper class to use common methods
include_once '../class/class.pdohelper.php';
// include pdo class wrapper
include_once '../class/class.pdowrapper.php';

// create new object of class wrapper
$p = new PdoWrapper();
// set error log mode
$p->setErrorLog(true);

// simple delete #1

// where condition array
$aWhere = array('age'=>35);
// call update function
$q = $p->delete('test', $aWhere)->showQuery()->affectedRows();
// print affected rows
PDOHelper::PA($q);


// simple delete #2


// where condition array
$aWhere = array('age'=>45, 'first_name'=> 'Sonu');
// call update function
$q = $p->delete('test', $aWhere)->showQuery()->affectedRows();
// print affected rows
PDOHelper::PA($q);