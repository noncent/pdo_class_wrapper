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
    public  $sSql = '';
    /**
     * PDO Results,Fetch All PDO Results
     * @var array
     */
    public $aRows = array();
    /**
     * PDO Result,Fetch One PDO Row
     * @var array
     */
    public $aRow = array();
    /**
     * Get PDO Last Insert ID
     * @var integer
     */
    public $iLastId = 0;
    /**
     * Get All PDO Affetcted Rows
     * @var integer
     */
    public $iAffectedRows = 0;
    /**
     * PDO Config/Settings
     * @var array
     */
    private $db_info = array("host" => DB_HOST, "dbname" => DB_NAME, "username" => DB_USERNAME, "password" => DB_PASSWORD);	
    /**
     * PDO Object
     * @var object
     */
    protected static $oPDO = null;
    /**
     * Auto Start on Object init
     * 
     * @param unknown_type $dsn
     * @throws Exception
     */
    public function __construct($dsn = array()) {
        if (isset($dsn)) {
            foreach ($dsn as $key_name => $key_value) {
                if (!in_array($key_name, array(
                    "host",
                    "dbname",
                    "username",
                    "password"
                ))) {
                    self::error("Invalid key passed!");                    
                }
                $this->db_info = $dsn;
            }
        }else{
        	$this->db_info = $this->db_info;
        }
        if (is_array($this->db_info) && !empty($this->db_info)) {
            extract($this->db_info);
            try {
                parent::__construct("mysql:host=$host;dbname=$dbname", $username, $password, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                /** If you want to Show Class exceptions on Screen, Uncomment below code **/
                /** $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); **/
                $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
            catch (PDOException $e) {
                self::error($e->getMessage());                
            }
        }
    }
    /**
     * Get Instance of PDO Class
     * @return object $oPDO
     */
    public static function getPDO($dsn = array()) {
        if (!isset(self::$oPDO) || self::$oPDO!==null) {
            self::$oPDO = new self($dsn);
        }
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
     * @return multitype:array/error
     */
    public function query($sSql = '', $aBind = array()) {
        if (!empty($sSql) && count($aBind) <= 0) {
            $this->sSql = $sSql;
            $this->_oSTH = $this->prepare($this->sSql);
            if ($this->_oSTH->execute()) {
                $this->iAffectedRows = $this->_oSTH->rowCount();
                $this->aRows         = $this->_oSTH->fetchAll();
                $this->_oSTH->closeCursor();
                return $this->aRows;
            } else {
                $arr = $this->_oSTH->errorInfo();
                self::pdoError($arr);
            }
        } else if (!empty($sSql) && count($aBind) > 0) {
            $this->sSql = $sSql;
            $this->_oSTH = $this->prepare($this->sSql);
            foreach ($aBind as $f => $v) {
                $this->_oSTH->bindParam(($f + 1), $aBind[$f], PDO::PARAM_STR);
            }
            if ($this->_oSTH->execute()) {
                $this->iAffectedRows = $this->_oSTH->rowCount();
                $this->aRows         = $this->_oSTH->fetchAll();
                $this->_oSTH->closeCursor();
                return $this->aRows;
            } else {
                $arr = $this->_oSTH->errorInfo();
                self::pdoError($arr);
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
        $sField = count($aColumn) > 0 ? implode(', ', $aColumn) : '*';
        if (!empty($sTable)) {            
            if (count($aWhere) > 0) {
                foreach ($aWhere as $k => $v) {
                    $tmp[] = "$k = :s_$k";
                }
                $sWhere = implode(' AND ', $tmp);
                unset($tmp);
                $this->sSql = "SELECT $sField FROM $sTable WHERE $sWhere $sOther;";
            } else {
                $this->sSql = "SELECT $sField FROM $sTable $sOther;";
            }
            $this->_oSTH = $this->prepare($this->sSql);
            if (count($aWhere) > 0) {
                foreach ($aWhere as $f => $v) {
                    $this->_oSTH->bindParam(":s_$f", $aWhere[$f], PDO::PARAM_STR);
                }
            }
            if ($this->_oSTH->execute()) {
                $this->iAffectedRows = $this->_oSTH->rowCount();
                $this->aRows         = $this->_oSTH->fetchAll();
                $this->_oSTH->closeCursor();
                return $this->aRows;
            } else {
                $arr = $this->_oSTH->errorInfo();
                self::pdoError($arr);
            }
        } else {
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
        if (!empty($sTable)) {
            if (count($aData) > 0) {
                foreach ($aData as $f => $v) {
                    $tmp[] = ":i_$f";
                }
                $sNameSpaceParam = implode(',', $tmp);
                unset($tmp);
                $sFields     = implode(',', array_keys($aData));
                $this->sSql  = "INSERT INTO `$sTable` ($sFields) VALUES ($sNameSpaceParam);";
                $this->_oSTH = $this->prepare($this->sSql);
                foreach ($aData as $f => $v) {
                    $this->_oSTH->bindParam(":i_$f", $aData[$f], PDO::PARAM_STR);
                }
                if ($this->_oSTH->execute()) {
                    $this->iLastId = $this->lastInsertId();
                    $this->_oSTH->closeCursor();
                    return $this->iLastId;
                } else {
                    $arr = $this->_oSTH->errorInfo();
                    self::pdoError($arr);
                }
            } else {
                self::error('Data not in valid format..');
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * Execute PDO Update Statement
     * Get No OF Affected Rows
     * 
     * @param string $sTable
     * @param array $aData
     * @param array $aWhere
     * @param string $sOther
     * @return number
     */
    public function update($sTable = '', $aData = array(), $aWhere = array(), $sOther = '') {
        if (!empty($sTable)) {
            if (count($aData) > 0 && count($aWhere) > 0) {
                foreach ($aData as $k => $v) {
                    $tmp[] = "$k = :u_$k";
                }
                $sFields = implode(', ', $tmp);
                unset($tmp);
                foreach ($aWhere as $k => $v) {
                    $tmp[] = "$k = :w_$k";
                }
                $sWhere = implode(' AND ', $tmp);
                unset($tmp);
                $this->sSql  = "UPDATE $sTable SET $sFields WHERE $sWhere $sOther;";
                $this->_oSTH = $this->prepare($this->sSql);
                foreach ($aData as $f => $v) {
                    $this->_oSTH->bindParam(":u_$f", $aData[$f], PDO::PARAM_STR);
                }
                foreach ($aWhere as $f => $v) {
                    $this->_oSTH->bindParam(":w_$f", $aWhere[$f], PDO::PARAM_STR);
                }
                if ($this->_oSTH->execute()) {
                    $this->iAffectedRows = $this->_oSTH->rowCount();
                    $this->_oSTH->closeCursor();
                    return $this->iAffectedRows;
                } else {
                    $arr = $this->_oSTH->errorInfo();
                    self::pdoError($arr);
                }
            } else {
                self::error('update statement not in valid format..');
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * Execute PDO Delete
     * 
     * @param string $sTable
     * @param array $aWhere
     * @param string $sOther
     * @return number: total affected rows
     */
    public function delete($sTable, $aWhere = array(), $sOther = '') {
        if (!empty($sTable)) {
            if (count($aWhere) > 0) {
                foreach ($aWhere as $k => $v) {
                    $tmp[] = "$k = :d_$k";
                }
                $sWhere = implode(' AND ', $tmp);
                unset($tmp);
                $this->sSql  = "DELETE FROM $sTable WHERE $sWhere $sOther;";
                $this->_oSTH = $this->prepare($this->sSql);
                foreach ($aWhere as $f => $v) {
                    $this->_oSTH->bindParam(":d_$f", $aWhere[$f], PDO::PARAM_STR);
                }
                if ($this->_oSTH->execute()) {
                    $this->iAffectedRows = $this->_oSTH->rowCount();
                    $this->_oSTH->closeCursor();
                    return $this->iAffectedRows;
                } else {
                    $arr = $this->_oSTH->errorInfo();
                    self::pdoError($arr);
                }
            } else {
                self::error('Not a valid where condition..');
            }
        } else {
            self::error('Table name not found..');
        }
    }
    /**
     * Get Total Number Of Records in Requested Table
     * 
     * @param string $sTable
     * @return number
     */
    public function count($sTable = '') {
        if (!empty($sTable)) {
            $this->sSql  = "SELECT COUNT(*) AS NUMROWS FROM $sTable;";
            $this->_oSTH = $this->prepare($this->sSql);
            if ($this->_oSTH->execute()) {
                $this->aRows = $this->_oSTH->fetch();
                $this->_oSTH->closeCursor();
                return $this->aRows['NUMROWS'];
            } else {
                $arr = $this->_oSTH->errorInfo();
                self::pdoError($arr);
            }
        } else {
            self::error('Table name not found..');
        }
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
     * Get HTML Table with Data
     * 
     * @param string $sTable
     * @return string HTML Table with data
     */
    public function displayHtmlTable($sTable = '') {
        $sSql        = "SELECT * FROM $sTable;";
        $this->_oSTH = $this->prepare($sSql);
        $this->_oSTH->execute();
        $aColList = $this->_oSTH->fetchAll();
        $r        = '';
        $r .= '<div class="sqlCommand">' . $sSql . '</div>';
        if (count($aColList) > 0) {
            $r .= '<table border="1">';
            $r .= '<thead>';
            $r .= '<tr>';
            foreach ($aColList[0] as $k => $v) {
                $r .= '<td>' . $k . '</td>';
            }
            $r .= '</tr>';
            $r .= '</thead>';
            $r .= '<tbody>';
            foreach ($aColList as $record) {
                $r .= '<tr>';
                foreach ($record as $data) {
                    $r .= '<td>' . $data . '</td>';
                }
                $r .= '</tr>';
            }
            $r .= '</tbody>';
            $r .= '<table>';
        } else {
            $r .= '<div class="no-results">No results found for query.</div>';
        }
        return $r;
    }
    /**
     * Check That a Array is Associative or Not
     * 
     * @param array $arr
     * @return boolean
     */
    public function validArray($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    /**
     * Catch Error in txt file
     * 
     * @param string $msg
     */
    public function error($msg) {
        file_put_contents('./../error/Errors.txt', date('Y-m-d h:m:s') . ' :: ' . $msg . "\n", FILE_APPEND);
        die('An Error occuring. Please, Check you error log....');
    }
    /**
     * Catch PDO Errors
     * 
     * @return string
     */
    public function pdoError($e) {
        self::error(json_encode($e));
        die('An Error occuring. Please, Check you error log....');
    }
    /**
     * Unset The Class Object
     */
    public  function __destruct(){
    	self::$oPDO = null;
    }
}
/** Class End **/
?>
