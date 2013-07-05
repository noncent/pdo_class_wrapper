<?php
/**
 * PdoWrapper
 *
 * PdoWrapper for using PDO methods
 *
 * PHP version 5.3.13
 *
 * MIT License
 *
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * 
 * @category   PHP Class
 * @package    PdoWrapper
 * @author     Neeraj Singh <neeraj.singh@lbi.co.in>
 * @author     Bhaskar Rabha <bhaskar.rabha@lbi.co.in>
 * @author     Priyadarshan Salkar <priyadarshan.salkar@lbi.co.in>
 * @copyright  2013-14 The PHP Group Of LBi India
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0 Beta
 */
/** Class Start **/
class PdoWrapper extends PDO {
    /**
     * PHP Statement Handler
     * @var object
     */
    private $_oSTH = null;
    /**
     * PDO SQL Statement
     * @var string
     */
    public $sSql = '';
    /**
     * PDO Results,Fetch All PDO Results array
     * @var array
     */
    public $aResults = array();
    /**
     * PDO Result,Fetch One PDO Row
     * @var array
     */
    public $aResult = array();
    /**
     * Get PDO Last Insert ID
     * @var integer
     */
    public $iLastId = 0;
    /**
     * Get PDO Error
     * @var string
     */
    public $sPdoError = '';
    /**
     * Get All PDO Affetcted Rows
     * @var integer
     */
    public $iAffectedRows = 0;
    /**
     * Enable/Disable class debug mode
     * @var boolean
     */
    const DEBUG = true;
    /**
     * PDO Error File
     * @var string
     */
    const ERROR_LOG_FILE = 'PDOErrors.log';
    /**
     * PDO Config/Settings
     * @var array
     */
    private $db_info = array("host" => '127.0.1.1', "dbname" => 'sampledb', "username" => 'root', "password" => '');
    /**
     * Set PDO valid Query opreation
     * 
     * @var array
     */
    private $aValidOpreation = array('SELECT', 'INSERT', 'UPDATE', 'DELETE');
    /**
     * PDO Object
     * @var object
     */
    protected static $oPDO = null;
    /**
     * Auto Start on Object init
     * 
     * @param string $dsn
     * @throws Exception
     */
    public function __construct($dsn = array()) {
        // if isset $dsn
        if (isset($dsn)) {
            // check $dsn array key for validity
            foreach ($dsn as $key_name => $key_value) {
                // if array key not found in $dsn array
                if (!in_array($key_name, array(
                    "host",
                    "dbname",
                    "username",
                    "password"
                ))) {
                    // show error msg
                    die("Invalid key passed in pdo config!");
                } else {
                    // set class property with pass $dsn setting
                    $this->db_info = $dsn;
                }
            }
        } else {
            // use class property to connect pdo 
            $this->db_info = $this->db_info;
        }
        // if db setting set and it is an array
        if (is_array($this->db_info) && !empty($this->db_info)) {
            // spilt array key as php var
            extract($this->db_info);
            try {
                // use native pdo class and connect
                parent::__construct("mysql:host=$host; dbname=$dbname", $username, $password, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));
                // set pdo error mode silent
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                /** If you want to Show Class exceptions on Screen, Uncomment below code **/
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                /** Use this setting to force PDO to either always emulate prepared statements (if TRUE), 
                or to try to use native prepared statements (if FALSE). **/
                $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                // set default pdo fetch mode as fecth assoc
                $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
            catch (PDOException $e) {
                // get pdo error and pass on error method
                self::error($e->getMessage() . ': ' . __LINE__);
            }
        }
    }
    /**
     * Get Instance of PDO Class as Singlton Pattern
     * 
     * @return object $oPDO
     */
    public static function getPDO($dsn = array()) {
        // if not set self pdo object property or pdo set as null
        if (!isset(self::$oPDO) || (self::$oPDO !== null)) {
            // set class pdo property with new connection
            self::$oPDO = new self($dsn);
        }
        // return class property object
        return self::$oPDO;
    }
    /**
     * Start PDO Transaction
     */
    public function start() {
        /*** begin the transaction ***/
        $this->beginTransaction();
    }
    /**
     * Start PDO Commit
     */
    public function end() {
        /*** commit the transaction ***/
        $this->commit();
    }
    /**
     * Start PDO Rollback
     */
    public function back() {
        /*** roll back the transaction if we fail ***/
        $this->rollback();
    }
    /**
     * Execute PDO Query
     * 
     * @param string $sSql
     * @param array Bind Param Value
     * @return PdoWrapper|multitype:|number 
     */
    public function pdoQuery($sSql = '', $aBind = array()) {
        // clen query from white space
        $sSql         = trim($sSql);
        // get opreation type
        $opreation    = explode(' ', $sSql);
        // make first word in uppercase
        $opreation[0] = strtoupper($opreation[0]);
        // check valid sql opreation
        if (!in_array($opreation[0], $this->aValidOpreation)) {
            self::error('invalid opreation called in query. use only ' . implode(', ', $this->aValidOpreation));
        }
        // sql query apss with no bind param
        if (!empty($sSql) && count($aBind) <= 0) {
            // set class property with pass value
            $this->sSql  = $sSql;
            // set class statment handler
            $this->_oSTH = $this->prepare($this->sSql);
            try {
                // execute pdo statment
                if ($this->_oSTH->execute()) {
                    // get affetcted rows and set it to class property
                    $this->iAffectedRows = $this->_oSTH->rowCount();
                    // set pdo result array with class property
                    $this->aResults      = $this->_oSTH->fetchAll();
                    // close pdo cursor
                    $this->_oSTH->closeCursor();
                    // return pdo result
                    return $this;
                } else {
                    // if not run pdo statement sed error
                    self::error($this->_oSTH->errorInfo());
                }
            }
            catch (PDOException $e) {
                self::error($e->getMessage() . ': ' . __LINE__);
            }
        } // if query pass with bind param 
        else if (!empty($sSql) && count($aBind) > 0) {
            // set class property with pass query
            $this->sSql  = $sSql;
            // set class pdo statment handler
            $this->_oSTH = $this->prepare($this->sSql);
            // start binding fields
            // bind pdo param
            $this->bindPdoParam('q', $aBind);
            // use try catch block to get pdo error
            try {
                // run pdo statement with bindparam
                if ($this->_oSTH->execute()) {
                    // check opreation type
                    switch ($opreation[0]):
                        case 'SELECT':
                            // get pdo result array
                            $this->aResults = $this->_oSTH->fetchAll();
                            // return result array
                            return $this;
                            break;
                        case 'INSERT':
                            $this->iLastId = $this->lastInsertId();
                            // return last insert id
                            return $this;
                            break;
                        case 'UPDATE':
                            // get affected rows
                            $this->iAffectedRows = $this->_oSTH->rowCount();
                            // return affected rows
                            return $this;
                            break;
                        case 'DELETE':
                            // get affected rows
                            $this->iAffectedRows = $this->_oSTH->rowCount();
                            // return affected rows
                            return $this;
                            break;
                    endswitch;
                    // close pdo cursor
                    $this->_oSTH->closeCursor();
                } else {
                    self::error($this->_oSTH->errorInfo());
                }
            }
            catch (PDOException $e) {
                self::error($e->getMessage() . ': ' . __LINE__);
            }
        } else {
            self::error('Query is empty..');
        }
    }
    /**
     * MySQL SELECT Query/Statement
     * 
     * @param string $sTable
     * @param array $aColumn
     * @param array $aWhere
     * @param string $sOther
     * @return multitype: array/error
     */
    public function select($sTable = '', $aColumn = array(), $aWhere = array(), $sOther = '') {
        // get field if pass otherwise use *
        $sField = count($aColumn) > 0 ? implode(', ', $aColumn) : '*';
        // check if table name not empty
        if (!empty($sTable)) {
            // if more then 0 array found in where array
            if (count($aWhere) > 0) {
                // parse where array and get in tem var with key name and val
                foreach ($aWhere as $k => $v) {
                    $tmp[] = "$k = :s_$k";
                }
                // join temp array with AND condition
                $sWhere = implode(' AND ', $tmp);
                // unset temp var
                unset($tmp);
                // set class sql property
                $this->sSql = "SELECT $sField FROM $sTable WHERE $sWhere $sOther;";
            } else {
                // if no where condition pass by user
                $this->sSql = "SELECT $sField FROM $sTable $sOther;";
            }
            // pdo prepare statement with sql query
            $this->_oSTH = $this->prepare($this->sSql);
            // if where condition has valid array number
            if (count($aWhere) > 0) {
                // bind pdo param
                $this->bindPdoNameSpace('s', $aWhere);
            } // if end here
            // use try catch block to get pdo error
            try {
                // check if pdo execute
                if ($this->_oSTH->execute()) {
                    // set class property with affected rows
                    $this->iAffectedRows = $this->_oSTH->rowCount();
                    // set class property with sql result
                    $this->aResults      = $this->_oSTH->fetchAll();
                    // close pdo
                    $this->_oSTH->closeCursor();
                    // return self object
                    return $this;
                } else {
                    // catch pdo error
                    self::error($this->_oSTH->errorInfo());
                }
            }
            catch (PDOException $e) {
                // get pdo error and pass on error method
                self::error($e->getMessage() . ': ' . __LINE__);
            }
        } // if table name empty 
        else {
            self::error('Table name not found..');
        }
    }
    /**
     * Execute PDO Insert
     * 
     * @param string $sTable
     * @param array $aData
     * @return number last insert ID
     */
    public function insert($sTable, $aData = array()) {
        // check if table name not empty
        if (!empty($sTable)) {
            // and array data not empty
            if (count($aData) > 0) {
                // get array insert data in temp array
                foreach ($aData as $f => $v) {
                    $tmp[] = ":i_$f";
                }
                // make name space param for pdo insert statement
                $sNameSpaceParam = implode(',', $tmp);
                // unset tem var
                unset($tmp);
                // get insert fields name
                $sFields     = implode(',', array_keys($aData));
                // set pdo insert statment in class property
                $this->sSql  = "INSERT INTO `$sTable` ($sFields) VALUES ($sNameSpaceParam);";
                // pdo prepare statment
                $this->_oSTH = $this->prepare($this->sSql);
                // bind pdo param
                $this->bindPdoNameSpace('i', $aData);
                try {
                    // execute pdo statement
                    if ($this->_oSTH->execute()) {
                        // set class properry with last insert id
                        $this->iLastId = $this->lastInsertId();
                        // close pdo
                        $this->_oSTH->closeCursor();
                        // return this object
                        return $this;
                    } else {
                        self::error($this->_oSTH->errorInfo());
                    }
                }
                catch (PDOException $e) {
                    // get pdo error and pass on error method
                    self::error($e->getMessage() . ': ' . __LINE__);
                }
            } else {
                self::error('Data not in valid format..');
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * Execute PDO Insert as Batch Data
     *
     * @param string $sTable mysql table name
     * @param array $aData mysql insert array data
     * @param boolean $safeMode set true if want to use pdo bind param
     * 
     * @return number last insert ID
     */
    public function insertBatch($sTable, $aData = array(), $safeMode = true) {
        $this->start();
        // check if table name not empty
        if (!empty($sTable)) {
            // and array data not empty
            if (count($aData) > 0) {
                // get array insert data in temp array
                foreach ($aData[0] as $f => $v) {
                    $tmp[] = ":bi_$f";
                }
                // make name space param for pdo insert statement
                $sNameSpaceParam = implode(', ', $tmp);
                // unset tem var
                unset($tmp);
                // get insert fields name
                $sFields = implode(', ', array_keys($aData[0]));
                // handle safe mode. If it is set as false means user not using bind param in pdo
                if (!$safeMode) {
                    // set pdo insert statment in class property
                    $this->sSql = "INSERT INTO `$sTable` ($sFields) VALUES ";
                    foreach ($aData as $key => $value) {
                        $this->sSql .= '(' . "'" . implode("', '", array_values($value)) . "'" . '), ';
                    }
                    $this->sSql  = rtrim($this->sSql, ', ');
                    // return this object
                    // return $this;
                    // pdo prepare statment
                    $this->_oSTH = $this->prepare($this->sSql);
                    try {
                        // execute pdo statement
                        if ($this->_oSTH->execute()) {
                            // set class properry with last insert id
                            $this->iLastId = $this->lastInsertId();
                        } else {
                            self::error($this->_oSTH->errorInfo());
                        }
                    }
                    catch (PDOException $e) {
                        // get pdo error and pass on error method
                        self::error($e->getMessage() . ': ' . __LINE__);
                        $this->back();
                    }
                    $this->end();
                    // close pdo
                    $this->_oSTH->closeCursor();
                    // return this object
                    return $this;
                }
                // end here safe mode
                // set pdo insert statment in class property
                $this->sSql  = "INSERT INTO `$sTable` ($sFields) VALUES ($sNameSpaceParam);";
                // pdo prepare statment
                $this->_oSTH = $this->prepare($this->sSql);
                // parse batch array data
                foreach ($aData as $key => $value) {
                    // bind pdo param
                    $this->bindPdoNameSpace('bi', $value);
                    try {
                        // execute pdo statement
                        if ($this->_oSTH->execute()) {
                            // set class properry with last insert id
                            $this->iLastId = $this->lastInsertId();
                        } else {
                            self::error($this->_oSTH->errorInfo());
                            $this->back();
                        }
                    }
                    catch (PDOException $e) {
                        // get pdo error and pass on error method
                        self::error($e->getMessage() . ': ' . __LINE__);
                        $this->back();
                    }
                }
                $this->end();
                // close pdo
                $this->_oSTH->closeCursor();
                // return this object
                return $this;
            } else {
                self::error('Data not in valid format..');
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * Execute PDO Update Statement
     * Get No OF Affected Rows updated
     * 
     * @param string $sTable
     * @param array $aData
     * @param array $aWhere
     * @param string $sOther
     * 
     * @return number
     */
    public function update($sTable = '', $aData = array(), $aWhere = array(), $sOther = '') {
        // if table name is empty
        if (!empty($sTable)) {
            // check if array data and where array is more then 0
            if (count($aData) > 0 && count($aWhere) > 0) {
                // parse array data and make a temp array
                foreach ($aData as $k => $v) {
                    $tmp[] = "$k = :u_$k";
                }
                // join temp array value with ,
                $sFields = implode(', ', $tmp);
                // delete temp array from memory
                unset($tmp);
                // parse where array and store in temp array
                foreach ($aWhere as $k => $v) {
                    $tmp[] = "$k = :w_$k";
                }
                // join where array value with AND opreator and create where condition
                $sWhere = implode(' AND ', $tmp);
                // unset temp array
                unset($tmp);
                // make sql query to update 
                $this->sSql  = "UPDATE $sTable SET $sFields WHERE $sWhere $sOther;";
                // on PDO prepare statement
                $this->_oSTH = $this->prepare($this->sSql);
                // bindpdo param for update statement
                $this->bindPdoNameSpace('u', $aData);
                // bindpdo param for where clause
                $this->bindPdoNameSpace('w', $aWhere);
                try {
                    // if PDO run
                    if ($this->_oSTH->execute()) {
                        // get affected rowa
                        $this->iAffectedRows = $this->_oSTH->rowCount();
                        // close PDO
                        $this->_oSTH->closeCursor();
                        // return self object
                        return $this;
                    } else {
                        self::error($this->_oSTH->errorInfo());
                    }
                }
                catch (PDOException $e) {
                    // get pdo error and pass on error method
                    self::error($e->getMessage() . ': ' . __LINE__);
                }
            } else {
                self::error('update statement not in valid format..');
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * Execute PDO Delete Query
     * 
     * @param string $sTable
     * @param array $aWhere
     * @param string $sOther
     * 
     * @return number: total affected rows
     */
    public function delete($sTable, $aWhere = array(), $sOther = '') {
        // if table name not pass
        if (!empty($sTable)) {
            // check where condition array length
            if (count($aWhere) > 0) {
                // make an temp array from where array data
                foreach ($aWhere as $k => $v) {
                    $tmp[] = "$k = :d_$k";
                }
                // join array values with AND Opreator
                $sWhere = implode(' AND ', $tmp);
                // delete temp array
                unset($tmp);
                // set DELETE PDO Statement
                $this->sSql  = "DELETE FROM $sTable WHERE $sWhere $sOther;";
                // Call PDo Preapare Statement
                $this->_oSTH = $this->prepare($this->sSql);
                // bind delete where param
                $this->bindPdoNameSpace('d', $aWhere);
                // Use try Catch 
                try {
                    if ($this->_oSTH->execute()) {
                        // get affected rows
                        $this->iAffectedRows = $this->_oSTH->rowCount();
                        // close pdo
                        $this->_oSTH->closeCursor();
                        // return this object
                        return $this;
                    } else {
                        self::error($this->_oSTH->errorInfo());
                    }
                }
                catch (PDOException $e) {
                    // get pdo error and pass on error method
                    self::error($e->getMessage() . ': ' . __LINE__);
                }
            } else {
                self::error('Not a valid where condition..');
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * Return PDo Query result by index value 
     * 
     * @param int $iRow
     * @return array
     */
    public function result($iRow = 0) {
        return $this->aResults[$iRow];
    }
    /**
     * Return PDO Query results array/json/xml
     *
     * @return array:xml:json
     */
    public function results($type = 'array') {
        switch ($type) {
            case 'array':
                // return array data
                return $this->aResults;
                break;
            case 'xml':
                //send the xml header to the browser
                header("Content-Type:text/xml");
                // return xml content
                return $this->arrayToXml($this->aResults);
                break;
            case 'json':
                // set header as json
                header('Content-type: application/json; charset="utf-8"');
                // return json encoded data
                return json_encode($this->aResults);
                break;
        }
    }
    /**
     * Get Affected rows by Update and Delete Query
     * 
     * @return number
     */
    public function affectedRows() {
        return $this->iAffectedRows;
    }
    /**
     * Get Last Insert id by Insert query
     * 
     * @return number
     */
    public function getLastInsertId() {
        return $this->iLastId;
    }
    /**
     * Get Total Number Of Records in Requested Table
     * 
     * @param string $sTable
     * @return number
     */
    public function count($sTable = '') {
        // if table name not pass
        if (!empty($sTable)) {
            // create count query
            $this->sSql  = "SELECT COUNT(*) AS NUMROWS FROM $sTable;";
            // pdo prepare statement
            $this->_oSTH = $this->prepare($this->sSql);
            try {
                if ($this->_oSTH->execute()) {
                    // fetch array result
                    $this->aResults = $this->_oSTH->fetch();
                    // close pdo
                    $this->_oSTH->closeCursor();
                    // return number of count
                    return $this->aResults['NUMROWS'];
                } else {
                    self::error($this->_oSTH->errorInfo());
                }
            }
            catch (PDOException $e) {
                // get pdo error and pass on error method
                self::error($e->getMessage() . ': ' . __LINE__);
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * PDO Bind Param with :namespace
     * 
     * @param string $sufix
     * @param array $array
     */
    public function bindPdoNameSpace($sufix = 's', $array = array()) {
        // bind array data in pdo
        foreach ($array as $f => $v) {
            // check pass data type for appropriate field
            switch (gettype($array[$f])):
                // is string found then pdo param as string
                case 'string':
                    $this->_oSTH->bindParam(":$sufix" . "_" . "$f", $array[$f], PDO::PARAM_STR);
                    break;
                // if int found then pdo param set as int
                case 'integer':
                    $this->_oSTH->bindParam(":$sufix" . "_" . "$f", $array[$f], PDO::PARAM_INT);
                    break;
                // if boolean found then set pdo param as boolean
                case 'boolean':
                    $this->_oSTH->bindParam(":$sufix" . "_" . "$f", $array[$f], PDO::PARAM_BOOL);
                    break;
            endswitch;
        } // end for each here
    }
    /**
     * Bind PDO Param without :namespace
     *
     * @param array $array
     */
    public function bindPdoParam($sufix = 's', $array = array()) {
        // bind array data in pdo
        foreach ($array as $f => $v) {
            // check pass data type for appropriate field
            switch (gettype($array[$f])):
                // is string found then pdo param as string
                case 'string':
                    $this->_oSTH->bindParam($f + 1, $array[$f], PDO::PARAM_STR);
                    break;
                // if int found then pdo param set as int
                case 'integer':
                    $this->_oSTH->bindParam($f + 1, $array[$f], PDO::PARAM_INT);
                    break;
                // if boolean found then set pdo param as boolean
                case 'boolean':
                    $this->_oSTH->bindParam($f + 1, $array[$f], PDO::PARAM_BOOL);
                    break;
            endswitch;
        } // end for each here
    }
    /**
     * Return Table Fields of Requested Table
     * 
     * @param string $sTable
     * @return array Field Type and Field Name
     */
    public function getFields($sTable = '') {
        $sSql        = "DESC $sTable;";
        $this->_oSTH = $this->prepare($sSql);
        $this->_oSTH->execute();
        $aColList = $this->_oSTH->fetchAll();
        foreach ($aColList as $key) {
            $aField[] = $key['Field'];
            $aType[]  = $key['Type'];
        }
        return array_combine($aField, $aType);
    }
    /**
     * function defination to convert array to xml
     * 
     * @param array $arrayData
     * @param object $xmlObject
     */
    public function arrayToXml($arrayData) {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .= "<root>";
        foreach ($arrayData as $key => $value) {
            $xml .= "<xml_data>";
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    //$k holds the table column name
                    $xml .= "<$k>";
                    //embed the SQL data in a CDATA element to avoid XML entity issues
                    $xml .= "<![CDATA[$v]]>";
                    //and close the element
                    $xml .= "</$k>";
                }
            } else {
                //$key holds the table column name
                $xml .= "<$key>";
                //embed the SQL data in a CDATA element to avoid XML entity issues
                $xml .= "<![CDATA[$value]]>";
                //and close the element
                $xml .= "</$key>";
            }
            $xml .= "</xml_data>";
        }
        $xml .= "</root>";
        return $xml;
    }
    /**
     * Catch Error in txt file
     * 
     * @param mixvar $msg
     */
    public function error($msg) {
        if (self::DEBUG) {
            die("<p style='color:#333846; border:1px solid #777; padding:2px; background-color: #FFC0CB;'>ERROR:" . json_encode($msg) . "</p>");
        } else {
            file_put_contents(self::ERROR_LOG_FILE, date('Y-m-d h:m:s') . ' :: ' . $msg . "n", FILE_APPEND);
            die("<p style='color:#333846; border:1px solid #777; padding:2px; background-color: #FFC0CB;'>ERROR: An error occuring. Please, Check you error log.</p>");
        }
    }
    /**
     * Show executed query on call
     * * 
     * @return PdoWrapper
     */
    public function traceEnable() {
        echo <<<TRACE
<p style='color:#A60099; border:1px solid #E5E5E5; padding:2px; background-color: #E5E5E5;'> Query Fired: $this->sSql</p>       
TRACE;
        return $this;
    }
    /**
     * Unset The Class Object
     */
    public function __destruct() {
        self::$oPDO = null;
    }
}
/** Class End **/
?>
