<h4>PHP PDO Class Wrapper</h4>
<h5>[A Wrapper Class of PDO]</h5>
*Version 1.2 (Beta)*

<h5>Introduction:</h5>

PDO Class Wrapper is a wrapper class of PDO (PHP Data Object) library.  As we know that in any web application, database makes an important role for developer to create a good dynamic web application. We can use different database drivers to make web more and more interactive and dynamic. But in any web project we also know that ‘Security’ is a big part and concern for developers. Every developer wants to keep user’s data very safe. Hence, we use much built-in functionality in PHP to prevent unauthorized access for database e.g.  mysql_real_escape_string (),  addslashes () etc. But some time it’s very difficult to manage big application with big chunk of code. So PHP improves MySQL to MySQLi (MySQL Improved). According to php.net The MySQLi extension has a number of benefits, the key enhancements over the MySQL extension being:Object-oriented interface
<ol>
<li>Support for Prepared Statements</li>
<li>Support for Multiple Statements</li>
<li>Support for Transactions</li>
<li>Enhanced debugging capabilities</li>
<li>Embedded server support</li>
</ol>

[source: php.net]
<br />

<h5>What is the PDO MYSQL driver?</h5>

The PDO MYSQL driver is not an API as such, at least from the PHP programmer's perspective. In fact the PDO MYSQL driver sits in the layer below PDO itself and provides MySQL-specific functionality. The programmer still calls the PDO API, but PDO uses the PDO MYSQL driver to carry out communication with the MySQL server.
The PDO MYSQL driver is one of several available PDO drivers. Other PDO drivers available include those for the Firebird and PostgreSQL database servers.
The PDO MYSQL driver is implemented using the PHP extension framework. Its source code is located in the directory ext/pdo_mysql. It does not expose an API to the PHP programmer.

[source: php.net]
<br />

<h5>Comparison of MySQL API options for PHP</h5>

![PDO Comparison](https://raw.github.com/neerajsinghsonu/PDO_Class_Wrapper/master/comparison.jpg "PDO Comparison")

<h5>About PDO Class Wrapper:</h5>

PDO Class Wrapper is a wrapper class of PDO (PHP Data Object) library. It has many useful built in functions to manage your web application database code very shorter. Also you will find some helpful method to fix your bug with very ease.

[source: Net Tut+]
<br />

<h5>Advantage of using PDO:</h5>

Many PHP programmers learned how to access databases by using either the MySQL or MySQLi extensions. As of PHP 5.1, there’s a better way. PHP Data Objects (PDO) provides methods for prepared statements and working with objects that will make you far more productive!

<h5>PDO Class Wrapper Features:</h5>

PDO Class Wrapper has very classic methods like any database class library:

<pre>
<ol>
<li>MySQL query									pdoQuery()</li>
<li>MySQL select query							select ()</li>
<li>MySQL insert query 							insert ()</li>
<li>MySQL insert batch							insertBatch()</li>
<li>MySQL update query							update()</li>
<li>MySQL delete query 							delete()</li>
<li>MySQL truncate table						truncate()</li>
<li>MySQL drop table							drop()</li>
<li>MySQL describe table						describe()</li>
<li>MySQL count records							count()</li>
<li>Show/debug executed query					showQuery()</li>
<li>Get last insert id							getLastInsertId()</li>
<li>Get all last insert id						getAllLastInsertId()</li>
<li>Get MySQL results							results()</li>
<li>Get MySQL result							result()</li>
<li>Get status of executed query				affectedRows()</li>
<li>MySQL begin transactions					start()</li>
<li>MySQL commit the transaction				end()</li>
<li>MySQL rollback the transaction				back()</li>
<li>Debugger PDO Error 							setErrorLog()</li>
</ol>
</pre>

<h5>How to Connect PDO Class:</h5>

<h6>Example: [A]</h6>

<pre>
$dbConfig = array
(
"host"=>"localhost", "dbname"=>'mydb', "username"=>'root', "password"=>''
);
$db = new PdoWrapper($dbConfig);
</pre>

<h6>Example: [B]</h6>

<pre>
$dbConfig = array
(
"host"=>"localhost", "dbname"=>"mydb", "username"=>'root', "password"=>''
);
$db = PdoWrapper::getPDO($dbConfig);
</pre>

<h4>PDO Class Wrapper Methods <b>Explanations:</b></h4>

<h5>pdoQuery():</h5>

Method name and parameter
<pre>pdoQuery ( string $sSql, array $aBindWhereParam)</pre>

<b>Explanations:</b>
This method is use for simple MySQL query; you can execute your MySQL query with parameterized parameter or as simple query.

<b>Example:</b>
```php
$sql = 'select * from customers limit 5;';
$data = $pdo->pdoQuery($sql)->results();

Raw Query:
SELECT * FROM customers LIMIT 5;

$sql = "select * from customers where (customernumber = '0000' OR customernumber = '45121') ;";
$data = $pdo->pdoQuery($sql)->results();

Raw Query:
SELECT * FROM customers WHERE (customernumber = 103 OR customernumber = 119) ;

$sql = "select * from customers where (customernumber = '0000' OR customernumber = '45121') ;";
$data = $db->pdoQuery($sql)->results();

Raw Query:
SELECT * FROM customers WHERE (customernumber = '0000' OR customernumber = '45121') ;

$sql = "select p.checknumber, p.amount, p.paymentdate, c.customernumber,
c.customerName, c.contactLastName, c.contactFirstName, c.phone, c.addressLine1,
c.addressLine2, c.city, c.state, c.postalCode, c.country from payments as p
inner join customers as c on p.customernumber = c.customernumber
order by p.amount desc limit 2;";
$data = $pdo->pdoQuery($sql)->results();

Raw Query:
SELECT p.checknumber, p.amount, p.paymentdate, c.customernumber, c.customername,
c.contactlastname, c.contactfirstname, c.phone, c.addressline1, c.addressline2,
c.city, c.state, c.postalcode, c.country FROM payments AS p INNER JOIN
customers AS c ON p.customernumber = c.customernumber ORDER BY p.amount DESC LIMIT 2;
```

<h4>select():</h4>

Method name and parameter
<pre>select (string $sTable , array $aColumn, array $aWhere, string $sOther)</pre>

<b>Explanations:</b>
The select method is made for get table data from just pass table name in method, if you omit column then you will get all fields of requested table else you can pass table field by an array. If you want to pass a where clause then you can use third parameter of select method and by pass fourth parameter you can send other filters.

<b>Example:</b>
```php
Get all table fields from table without passing 2nd parameter.

$select = $pdo->select('customers');
$data = $select->results();

Raw Query:
SELECT * FROM `customers` ;

Or

You can use one line code to get a result array

$data = $pdo->select('employees')->results();

Raw Query:
SELECT * FROM `employees` ;

Get only selected fields from table

$data = $db->select('employees', array('employeeNumber','lastName','firstName'))->results();

Raw Query:
SELECT employeenumber, lastname, firstname FROM `employees` ;

Or

$fieldsArray = array('employeeNumber','lastName','firstName');
$data = $db->select('employees', $fieldsArray)->results();

Raw Query:
SELECT employeenumber, lastname, firstname FROM `employees` ;

$selectFields = array('employeeNumber','lastName','firstName');
$whereConditions = array('lastname'=>'bow');
$data = $db->select('employees', $selectFields, $whereConditions, 'ORDER BY employeeNumber DESC LIMIT 5')->results();

Raw Query:
SELECT employeenumber, lastname, firstname FROM `employees` WHERE lastname = "bow" ORDER BY employeenumber DESC LIMIT 5;

Custom Where Clause with Select Method:

You can set your own custom where clause

$whereConditions = array('lastname ='=>'bow', 'or jobtitle ='=> 'Sales Rep', 'and isactive ='=>1, 'and officecode ='=> 1 );
$data = $db->select('employees','',$whereConditions)->results();

Raw Query:
SELECT * FROM `employees` WHERE lastname = "bow" OR jobtitle = "sales rep" AND isactive = 1 AND officecode = 1 ;

OR

$whereConditions = array('lastname ='=>'bow', 'or jobtitle ='=> 'Sales Rep', 'and isactive ='=>1, 'and officecode ='=> 1 );
$data = $db->select('employees',array('employeenumber','lastname','jobtitle'),$whereConditions)->results();

Raw Query:
SELECT employeenumber, lastname, jobtitle FROM 'employees' WHERE lastname = "bow" OR jobtitle = "sales rep" AND isactive = 1 AND officecode = 1 ;
```

<h4>insert():</h4>

Method name and parameter
<pre>insert( string $sTable, array $aData )</pre>

<b>Explanations:</b>
By insert method you can insert record into selected table. Just pass data as an array with fields as array key and the array data will insert in to table. Insert method automatically convert your array data in to SQL injection safe data.

<b>Example:</b>
```php
$dataArray = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
$data = $db->insert('test',$dataArray)->getLastInsertId();

Raw Query:
INSERT INTO `test` (first_name,last_name,age) VALUES ("sid","mike",45);
```

<h4>insertBatch():</h4>

Method name and parameter
<pre>insertBatch(string $sTable, array $aData, boolean $safeModeInsert )</pre>

<b>Explanations:</b>
You can use this method for inserting multiple array data in same table. You have to just send full array data and rest of thing insertBatch will handle. You can send third parameter as false if you don’t want to insert parameterize insert or send true if want to secure insertions.
insertBatch works with MySQL transactions so you don’t need to worry about failure data. It will be rollback if anything goes wrong.

<b>Example:</b>
```php
$dataArray[] = array('first_name'=>'Sid','last_name'=>'Mike','age'=>45);
$dataArray[] = array('first_name'=>'Scott','last_name'=>'Dimon','age'=>78);
$dataArray[] = array('first_name'=>'Meena','last_name'=>'Verma','age'=>23);
$data = $db->insertBatch('test',$dataArray, true)->getAllLastInsertId();

Raw Query:
INSERT INTO `test` (first_name, last_name, age) VALUES ("sid", "mike", 45);
INSERT INTO `test` (first_name, last_name, age) VALUES ("scott", "dimon", 78);
INSERT INTO `test` (first_name, last_name, age) VALUES ("meena", "verma", 23);
```

<h4>update():</h4>

Method name and parameter
<pre>update( string $sTable, array $aData, array $aWhere, string  $sOther)</pre>

<b>Explanations:</b>
Update method is use for update a table with array data. You can send array data as update data in table.

<b>Example:</b>
```php
$dataArray = array('first_name'=>'Sangeeta','last_name'=>'Mishra','age'=>35);
$aWhere = array('id'=>23);
$data = $db->update('test', $dataArray, $aWhere->affectedRows();

Raw Query:
UPDATE `test` SET first_name = "sangeeta", last_name = "mishra", age = 35 WHERE id = 23 ;

Or

$dataArray = array('first_name'=>'Sonia','last_name'=>'Shukla','age'=>23);
$aWhere = array('age'=>35, 'last_name'=>'Mishra');
$data = $db->update('test', $dataArray, $aWhere)->affectedRows();

Raw Query:
UPDATE `test` SET first_name = "sonia", last_name = "shukla", age = 23 WHERE
age = 35 AND last_name = "mishra" ;
```

<h4>delete():</h4>

Method name and parameter
<pre>delete( string $sTable, array $aWhere, string  $sOther )</pre>

<b>Explanations:</b>
You can delete records from table by send table name and your where clause array.

<b>Example:</b>
```php
$aWhere = array('age'=>35);
$data = $db->delete('test', $aWhere)->affectedRows();

Raw Query:
DELETE FROM `test` WHERE age = 35 ;

$aWhere = array('age'=>45, 'first_name'=> 'Sonu');
$data = $db->delete('test', $aWhere)->affectedRows();

Raw Query:
DELETE FROM `test` WHERE age = 45 AND first_name = "sonu" ;
```

<h4>truncate():</h4>

Method name and parameter
<pre>truncate( string $sTable )</pre>

<b>Explanations:</b>
You can truncate table by just pass table name.

<b>Example:</b>
<pre>
$data = $db->truncate('test');

Raw Query:
TRUNCATE TABLE `test`;
</pre>

<h4>drop():</h4>

Method name and parameter
<pre>drop( string $sTable )</pre>

<b>Explanations:</b>
You can drop table by just pass table name.

<b>Example:</b>
<pre>
$data = $db->drop('test');

Raw Query:
DROP TABLE `test`;
</pre>

<h4>describe():</h4>

Method name and parameter
<pre>describe( string $sTable )</pre>

<b>Explanations:</b>
You can get a table field name and data type.

<b>Example:</b>
<pre>
$data = $db->describe('test');

Raw Query:
DESC  `test`;
</pre>

<h4>count():</h4>

Method name and parameter
<pre>count( string $sTable, string $sWhere )</pre>

<b>Explanations:</b>
This function will return the number of total rows in a table.

<b>Example:</b>
```php
echo $q = $p->count('employees');
$p->showQuery();

Raw Query:
23
SELECT COUNT(*) AS numrows FROM `employees`;

echo $q = $p->count('employees','firstname = "mary"');
$p->showQuery();

Raw Query:
1
SELECT COUNT(*) AS numrows FROM `employees` WHERE firstname = "mary";
echo $q = $p->count('employees','jobtitle="Sales Rep"');
$p->showQuery();

Raw Query:
17
SELECT COUNT(*) AS numrows FROM `employees` WHERE jobtitle="sales rep";
```

<h4>showQuery():</h4>

Method name and parameter
<pre>showQuery( Boolean $logfile )</pre>

<b>Explanations:</b>
By this function you can get executed query. It will show raw query on your screen. If you want to logfile to save query then you can send 2nd param as true. By default it’s false.

<b>Example:</b>
<pre>
$db->showQuery();

Raw Query:
SELECT COUNT(*) AS numrows FROM `test`;
</pre>

<h4>getLastInsertId():</h4>

Method name and parameter
<pre>getLastInsertId()</pre>

<b>Explanations:</b>
Get a newly inserted id by insert function.

<b>Example:</b>
<pre>$lid = $db->getLastInsertId();</pre>

Return:
Number/Integer

<h4>getAllLastInsertId():</h4>

Method name and parameter
<pre>getAllLastInsertId ()</pre>

<b>Explanations:</b>
Get all newly inserted id by insertBatch function.

<b>Example:</b>
<pre>
$lid = $db->getAllLastInsertId();
</pre>

Return:
Array

<h4>results():</h4>

Method name and parameter
<pre>results (string $type )</pre>

<b>Explanations:</b>
Get array result data by executed SELECT or Select Query. You can get result in three formats
Array, XML and JSON. Just pass 1st param as ‘array’ or ‘xml’ or ‘json’. By default it will return array.

<b>Example:</b>
```php
$data = $db->results();
$data = $db->results('xml');
$data = $db->results('json');
```

Return:
Array | XML | JSON

<h4>result():</h4>

Method name and parameter
<pre>results (integer $iRow)</pre>

<b>Explanations:</b>
Get result from an array data by request index or false.

<b>Example:</b>
<pre>$data = $db->result(1);</pre>

Return:
Array | false

<h4>affectedRows():</h4>

Method name and parameter
<pre>affectedRows()</pre>

<b>Explanations:</b>
Get number of affected rows by update, delete and select etc. statement or false.

<b>Example:</b>
<pre>$data = $db->affectedRows();</pre>

Return:
integer | false

<h4>start():</h4>

Method name and parameter
<pre>start()</pre>

<b>Explanations:</b>
Start the MySQL transaction.

<b>Example:</b>
<pre>$db->start();</pre>

<h4>end():</h4>

Method name and parameter
<pre>end()</pre>

<b>Explanations:</b>
Commit the MySQL transaction.

<b>Example:</b>
<pre>$db->end();</pre>

<h4>back():</h4>

Method name and parameter
<pre>back()</pre>

<b>Explanations:</b>
Rollback the MySQL transaction.

<b>Example:</b>
<pre>$db->back();</pre>

<h4>setErrorLog():</h4>

Method name and parameter
<pre>setErrorLog (boolean $mode)</pre>

<b>Explanations:</b>
setErrorLog, method works for show/hide PDO error. If you send true then all errors will show on screen or if you send false then all errors will store in log file in same location.

<b>Example:</b>
<pre>$db->setErrorLog(true);</pre>

<h4>Example Connection Page:</h4>

```php
// include PDO Class Wrapper
include_once 'class/class.pdowrapper.php';

// set connection data
$dbConfig = array
(
"host"=>"localhost", "dbname"=>'sampledb', "username"=>'root', "password"=>''
);

// get instance of PDO Class Wrapper
$db = PdoWrapper::getPDO($dbConfig);

// set error log mode true to show all error on screen
$db->setErrorLog(true);

/* simple update example */
// update array data
$dataArray = array('first_name'=>'Sangeeta','last_name'=>'Mishra','age'=>35);

// where condition array
$aWhere = array('id'=>23);

// call update function
$q = $p->update('test', $dataArray, $aWhere)->showQuery()->affectedRows();

Output:
UPDATE `test` SET first_name = "sangeeta", last_name = "mishra", age = 35 WHERE id = 23 ;
1
```


Cheers!!
<br />
Priyadarshan Salkar | priyadarshan[dot]salkar[at]lbi[dot]co[dot]in
<br />
Bhaskar Rabha  |bhaskar[dot]rabha[at]lbi[dot]co[dot]in
<br />
Neeraj Singh | neeraj[dot]singh[at]lbi[dot]co[dot]in
<h6>[Document End]</h6>
