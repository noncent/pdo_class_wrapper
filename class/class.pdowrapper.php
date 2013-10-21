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
 * NON INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   PHP Class
 * @package    PdoWrapper (PDO)
 * @author     Neeraj Singh <neeraj.singh@lbi.co.in>
 * @copyright  The PHP Groups Of LBi India (2013-14)
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.01 Beta (modify - 13-August-2013)
 *
 * @example code
 *
 * $config = array("host"=>"localhost", "dbname"=>'sampledb', "username"=>'root', "password"=>'');
 * $db = new PdoWrapper($config);
 * $db->setErrorLog(true);
 */

/**
 * Include PDO Helper Class
 */
require_once 'class.pdohelper.php';

/** Class Start **/
class PdoWrapper extends PDO {
    /**
     * PHP Statement Handler
     *
     * @var object
     */
    private $_oSTH = null;
    /**
     * PDO SQL Statement
     *
     * @var string
     */
    public $sSql = '';
    /**
     * PDO SQL table name
     *
     * @var string
     */
    public $sTable = array();
    /**
     * PDO SQL Where Condition
     *
     * @var string
     */
    public $aWhere = array();
    /**
     * PDO SQL table column
     *
     * @var string
     */
    public $aColumn = array();
    /**
     * PDO SQL Other condition
     *
     * @var string
     */
    public $sOther = array();
    /**
     * PDO Results,Fetch All PDO Results array
     *
     * @var array
     */
    public $aResults = array();
    /**
     * PDO Result,Fetch One PDO Row
     *
     * @var array
     */
    public $aResult = array();
    /**
     * Get PDO Last Insert ID
     *
     * @var integer
     */
    public $iLastId = 0;
    /**
     * PDO last insert di in array
     * using with INSERT BATCH Query
     *
     * @var array
     */
    public $iAllLastId = array();
    /**
     * Get PDO Error
     *
     * @var string
     */
    public $sPdoError = '';
    /**
     * Get All PDO Affected Rows
     *
     * @var integer
     */
    public $iAffectedRows = 0;
    /**
     * Catch temp data
     * @var null
     */
    public $aData = null;
    /**
     * Enable/Disable class debug mode
     *
     * @var boolean
     */
    public $log = false;
    /**
     * Set flag for batch insert
     * @var bool
     */
    public $batch = false;
    /**
     * PDO Error File
     *
     * @var string
     */
    const ERROR_LOG_FILE = 'PDO_Errors.log';
    /**
     * PDO SQL log File
     *
     * @var string
     */
    const SQL_LOG_FILE = 'PDO_Sql.log';
    /**
     * PDO Config/Settings
     *
     * @var array
     */
    private $db_info = array();
    /**
     * Set PDO valid Query operation
     *
     * @var array
     */
    private $aValidOperation = array( 'SELECT', 'INSERT', 'UPDATE', 'DELETE' );
    /**
     * PDO Object
     *
     * @var object
     */
    protected static $oPDO = null;
    /**
     * Auto Start on Object init
     *
     * @param array $dsn
     *
     * @throws Exception
     */
    public function __construct( $dsn = array() ) {
        // if isset $dsn and it is array
        if ( is_array( $dsn ) && count($dsn) > 0 ) {
            // check valid array key name
            if(!isset($dsn['host']) || !isset($dsn['dbname']) || !isset($dsn['username']) || !isset($dsn['password'])){
                die("Dude!! You haven't pass valid db config array key.");
            }
            $this->db_info = $dsn;
        }else{
            if(count($this->db_info) > 0){
                $dsn = $this->db_info;
                // check valid array key name
                if(!isset($dsn['host']) || !isset($dsn['dbname']) || !isset($dsn['username']) || !isset($dsn['password'])){
                    die("Dude!! You haven't set valid db config array key.");
                }
            }else{
                die("Dude!! You haven't set valid db config array.");
            }
        }
        // Okay, everything is clear. now connect
        // spilt array key in php variable
        extract( $this->db_info );
        // try catch block start
        try {
            // use native pdo class and connect
            parent::__construct( "mysql:host=$host; dbname=$dbname", $username, $password, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ) );
            // set pdo error mode silent
            $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
            /** If you want to Show Class exceptions on Screen, Uncomment below code **/
            $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            /** Use this setting to force PDO to either always emulate prepared statements (if TRUE),
            or to try to use native prepared statements (if FALSE). **/
            $this->setAttribute( PDO::ATTR_EMULATE_PREPARES, true );
            // set default pdo fetch mode as fetch assoc
            $this->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
        }
        catch ( PDOException $e ) {
            // get pdo error and pass on error method
            die("ERROR in establish connection: ".$e->getMessage());
        }

    }
    /**
     * Unset The Class Object PDO
     */
    public function __destruct() {
        self::$oPDO = null;
    }
    /**
     * Get Instance of PDO Class as Singleton Pattern
     *
     * @param array $dsn
     *
     * @return object $oPDO
     */
    public static function getPDO( $dsn = array() ) {
        // if not set self pdo object property or pdo set as null
        if ( !isset( self::$oPDO ) || ( self::$oPDO !== null ) ) {
            // set class pdo property with new connection
            self::$oPDO = new self( $dsn );
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
     * Return PDO Query result by index value
     *
     * @param int $iRow
     *
     * @return array:boolean
     */
    public function result( $iRow = 0 ) {
        return isset($this->aResults[$iRow]) ? $this->aResults[$iRow] : false;
    }
    /**
     * Get Affected rows by PDO Statement
     *
     * @return number:boolean
     */
    public function affectedRows() {
        return is_numeric($this->iAffectedRows) ? $this->iAffectedRows : false;
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
     * Get all last insert id by insert batch query
     *
     * @return array
     */
    public function getAllLastInsertId() {
        return $this->iAllLastId;
    }
    /**
     * Get Helper Object
     *
     * @return PDOHelper
     */
    public function helper() {
        return new PDOHelper();
    }
    /**
     * Execute PDO Query
     *
     * @param string $sSql
     * @param array Bind Param Value
     *
     * @return PdoWrapper|multi type:|number
     */
    public function pdoQuery( $sSql = '', $aBindWhereParam = array() ) {
        // clean query from white space
        $sSql         = trim( $sSql );
        // get operation type
        $operation    = explode( ' ', $sSql );
        // make first word in uppercase
        $operation[0] = strtoupper( $operation[0] );
        // check valid sql operation statement
        if ( !in_array( $operation[0], $this->aValidOperation ) ) {
            self::error( 'invalid operation called in query. use only ' . implode( ', ', $this->aValidOperation ) );
        }
        // sql query pass with no bind param
        if ( !empty( $sSql ) && count( $aBindWhereParam ) <= 0 ) {
            // set class property with pass value
            $this->sSql  = $sSql;
            // set class statement handler
            $this->_oSTH = $this->prepare( $this->sSql );
            // try catch block start
            try {
                // execute pdo statement
                if ( $this->_oSTH->execute() ) {
                    // get affected rows and set it to class property
                    $this->iAffectedRows = $this->_oSTH->rowCount();
                    // set pdo result array with class property
                    $this->aResults      = $this->_oSTH->fetchAll();
                    // close pdo cursor
                    $this->_oSTH->closeCursor();
                    // return pdo result
                    return $this;
                } else {
                    // if not run pdo statement sed error
                    self::error( $this->_oSTH->errorInfo() );
                }
            }
            catch ( PDOException $e ) {
                self::error( $e->getMessage() . ': ' . __LINE__ );
            } // end try catch block
        } // if query pass with bind param 
        else if ( !empty( $sSql ) && count( $aBindWhereParam ) > 0 ) {
            // set class property with pass query
            $this->sSql   = $sSql;
            // set class where array
            $this->aData = $aBindWhereParam;
            // set class pdo statement handler
            $this->_oSTH  = $this->prepare( $this->sSql );
            // start binding fields
            // bind pdo param
            $this->_bindPdoParam( $aBindWhereParam );
            // use try catch block to get pdo error
            try {
                // run pdo statement with bind param
                if ( $this->_oSTH->execute() ) {
                    // check operation type
                    switch ( $operation[0] ):
                        case 'SELECT':
                            // get affected rows by select statement
                            $this->iAffectedRows = $this->_oSTH->rowCount();
                            // get pdo result array
                            $this->aResults      = $this->_oSTH->fetchAll();
                            // return PDO instance
                            return $this;
                            break;
                        case 'INSERT':
                            // return last insert id
                            $this->iLastId = $this->lastInsertId();
                            // return PDO instance
                            return $this;
                            break;
                        case 'UPDATE':
                            // get affected rows
                            $this->iAffectedRows = $this->_oSTH->rowCount();
                            // return PDO instance
                            return $this;
                            break;
                        case 'DELETE':
                            // get affected rows
                            $this->iAffectedRows = $this->_oSTH->rowCount();
                            // return PDO instance
                            return $this;
                            break;
                    endswitch;
                    // close pdo cursor
                    $this->_oSTH->closeCursor();
                } else {
                    self::error( $this->_oSTH->errorInfo() );
                }
            }
            catch ( PDOException $e ) {
                self::error( $e->getMessage() . ': ' . __LINE__ );
            } // end try catch block to get pdo error
        } else {
            self::error( 'Query is empty..' );
        }
    }
    /**
     * MySQL SELECT Query/Statement
     *
     * @param string $sTable
     * @param array $aColumn
     * @param array $aWhere
     * @param string $sOther
     *
     * @return multi type: array/error
     */
    public function select( $sTable = '', $aColumn = array(), $aWhere = array(), $sOther = '' ) {
        // handle column array data
        if(!is_array($aColumn))$aColumn = array();
        // get field if pass otherwise use *
        $sField = count( $aColumn ) > 0 ? implode( ', ', $aColumn ) : '*';
        // check if table name not empty
        if ( !empty( $sTable ) ) {
            // if more then 0 array found in where array
            if ( count( $aWhere ) > 0 && is_array( $aWhere ) ) {
                // set class where array
                $this->aData = $aWhere;
                // parse where array and get in temp var with key name and val
                if(strstr(key($aWhere), ' ')){
                    $tmp = $this->customWhere($this->aData);
                    // get where syntax with namespace
                    $sWhere = $tmp['where'];
                }else{
                    foreach ( $aWhere as $k => $v ) {
                        $tmp[] = "$k = :s_$k";
                    }
                    // join temp array with AND condition
                    $sWhere = implode( ' AND ', $tmp );
                }
                // unset temp var
                unset( $tmp );
                // set class sql property
                $this->sSql = "SELECT $sField FROM `$sTable` WHERE $sWhere $sOther;";
            } else {
                // if no where condition pass by user
                $this->sSql = "SELECT $sField FROM `$sTable` $sOther;";
            }
            // pdo prepare statement with sql query
            $this->_oSTH = $this->prepare( $this->sSql );
            // if where condition has valid array number
            if ( count( $aWhere ) > 0 && is_array( $aWhere ) ) {
               // bind pdo param
               $this->_bindPdoNameSpace( $aWhere );
            } // if end here
            // use try catch block to get pdo error
            try {
                // check if pdo execute
                if ( $this->_oSTH->execute() ) {
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
                    self::error( $this->_oSTH->errorInfo() );
                }
            }
            catch ( PDOException $e ) {
                // get pdo error and pass on error method
                self::error( $e->getMessage() . ': ' . __LINE__ );
            } // end try catch block to get pdo error
        } // if table name empty 
        else {
            self::error( 'Table name not found..' );
        }
    }
    /**
     * Execute PDO Insert
     *
     * @param string $sTable
     * @param array $aData
     *
     * @return number last insert ID
     */
    public function insert( $sTable, $aData = array() ) {
        // check if table name not empty
        if ( !empty( $sTable ) ) {
            // and array data not empty
            if ( count( $aData ) > 0 && is_array( $aData ) ) {
                // get array insert data in temp array
                foreach ( $aData as $f => $v ) {
                    $tmp[] = ":s_$f";
                }
                // make name space param for pdo insert statement
                $sNameSpaceParam = implode( ',', $tmp );
                // unset temp var
                unset( $tmp );
                // get insert fields name
                $sFields     = implode( ',', array_keys( $aData ) );
                // set pdo insert statement in class property
                $this->sSql  = "INSERT INTO `$sTable` ($sFields) VALUES ($sNameSpaceParam);";
                // pdo prepare statement
                $this->_oSTH = $this->prepare( $this->sSql );
                // set class where property with array data
                $this->aData = $aData;
                // bind pdo param
                $this->_bindPdoNameSpace( $aData );
                // use try catch block to get pdo error
                try {
                    // execute pdo statement
                    if ( $this->_oSTH->execute() ) {
                        // set class property with last insert id
                        $this->iLastId = $this->lastInsertId();
                        // close pdo
                        $this->_oSTH->closeCursor();
                        // return this object
                        return $this;
                    } else {
                        self::error( $this->_oSTH->errorInfo() );
                    }
                }
                catch ( PDOException $e ) {
                    // get pdo error and pass on error method
                    self::error( $e->getMessage() . ': ' . __LINE__ );
                }
            } else {
                self::error( 'Data not in valid format..' );
            }
        } else {
            self::error( 'Table name not found..' );
        }
    }
    /**
     * Execute PDO Insert as Batch Data
     *
     * @param string $sTable mysql table name
     * @param array $aData mysql insert array data
     * @param boolean $safeModeInsert set true if want to use pdo bind param
     *
     * @return number last insert ID
     */
    public function insertBatch( $sTable, $aData = array(), $safeModeInsert = true ) {
        // PDO transactions start
        $this->start();
        // check if table name not empty
        if ( !empty( $sTable ) ) {
            // and array data not empty
            if ( count( $aData ) > 0 && is_array( $aData ) ) {
                // get array insert data in temp array
                foreach ( $aData[0] as $f => $v ) {
                    $tmp[] = ":s_$f";
                }
                // make name space param for pdo insert statement
                $sNameSpaceParam = implode( ', ', $tmp );
                // unset temp var
                unset( $tmp );
                // get insert fields name
                $sFields = implode( ', ', array_keys( $aData[0] ) );
                // handle safe mode. If it is set as false means user not using bind param in pdo
                if ( !$safeModeInsert ) {
                    // set pdo insert statement in class property
                    $this->sSql = "INSERT INTO `$sTable` ($sFields) VALUES ";
                    foreach ( $aData as $key => $value ) {
                        $this->sSql .= '(' . "'" . implode( "', '", array_values( $value ) ) . "'" . '), ';
                    }
                    $this->sSql  = rtrim( $this->sSql, ', ' );
                    // return this object
                    // return $this;
                    // pdo prepare statement
                    $this->_oSTH = $this->prepare( $this->sSql );
                    // start try catch block
                    try {
                        // execute pdo statement
                        if ( $this->_oSTH->execute() ) {
                            // store all last insert id in array
                            $this->iAllLastId[] = $this->lastInsertId();
                        } else {
                            self::error( $this->_oSTH->errorInfo() );
                        }
                    }
                    catch ( PDOException $e ) {
                        // get pdo error and pass on error method
                        self::error( $e->getMessage() . ': ' . __LINE__ );
                        // PDO Rollback
                        $this->back();
                    } // end try catch block
                    // PDO Commit
                    $this->end();
                    // close pdo
                    $this->_oSTH->closeCursor();
                    // return this object
                    return $this;
                }
                // end here safe mode
                // set pdo insert statement in class property
                $this->sSql  = "INSERT INTO `$sTable` ($sFields) VALUES ($sNameSpaceParam);";
                // pdo prepare statement
                $this->_oSTH = $this->prepare( $this->sSql );
                // set class property with array
                $this->aData = $aData;
                // set batch insert flag true
                $this->batch = true;
                // parse batch array data
                foreach ( $aData as $key => $value ) {
                    // bind pdo param
                    $this->_bindPdoNameSpace( $value );
                    try {
                        // execute pdo statement
                        if ( $this->_oSTH->execute() ) {
                            // set class property with last insert id as array
                            $this->iAllLastId[] = $this->lastInsertId();
                        } else {
                            self::error( $this->_oSTH->errorInfo() );
                            // on error PDO Rollback
                            $this->back();
                        }
                    }
                    catch ( PDOException $e ) {
                        // get pdo error and pass on error method
                        self::error( $e->getMessage() . ': ' . __LINE__ );
                        // on error PDO Rollback
                        $this->back();
                    }
                }
                // fine now PDO Commit
                $this->end();
                // close pdo
                $this->_oSTH->closeCursor();
                // return this object
                return $this;
            } else {
                self::error( 'Data not in valid format..' );
            }
        } else {
            self::error( 'Table name not found..' );
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
    public function update( $sTable = '', $aData = array(), $aWhere = array(), $sOther = '' ) {
        // if table name is empty
        if ( !empty( $sTable ) ) {
            // check if array data and where array is more then 0
            if ( count( $aData ) > 0 && count( $aWhere ) > 0 ) {
                // parse array data and make a temp array
                foreach ( $aData as $k => $v ) {
                    $tmp[] = "$k = :s_$k";
                }
                // join temp array value with ,
                $sFields = implode( ', ', $tmp );
                // delete temp array from memory
                unset( $tmp );
                // parse where array and store in temp array
                foreach ( $aWhere as $k => $v ) {
                    $tmp[] = "$k = :s_$k";
                }
                $this->aData = $aData;
                $this->aWhere = $aWhere;
                // join where array value with AND operator and create where condition
                $sWhere = implode( ' AND ', $tmp );
                // unset temp array
                unset( $tmp );
                // make sql query to update 
                $this->sSql  = "UPDATE `$sTable` SET $sFields WHERE $sWhere $sOther;";
                // on PDO prepare statement
                $this->_oSTH = $this->prepare( $this->sSql );
                // bind pdo param for update statement
                $this->_bindPdoNameSpace( $aData );
                // bind pdo param for where clause
                $this->_bindPdoNameSpace( $aWhere );
                // try catch block start
                try {
                    // if PDO run
                    if ( $this->_oSTH->execute() ) {
                        // get affected rows
                        $this->iAffectedRows = $this->_oSTH->rowCount();
                        // close PDO
                        $this->_oSTH->closeCursor();
                        // return self object
                        return $this;
                    } else {
                        self::error( $this->_oSTH->errorInfo() );
                    }
                }
                catch ( PDOException $e ) {
                    // get pdo error and pass on error method
                    self::error( $e->getMessage() . ': ' . __LINE__ );
                } // try catch block end
            } else {
                self::error( 'update statement not in valid format..' );
            }
        } else {
            self::error( 'Table name not found..' );
        }
    }
    /**
     * Execute PDO Delete Query
     *
     * @param string $sTable
     * @param array $aWhere
     * @param string $sOther
     *
     * @return object PDO object
     */
    public function delete( $sTable, $aWhere = array(), $sOther = '' ) {
        // if table name not pass
        if ( !empty( $sTable ) ) {
            // check where condition array length
            if ( count( $aWhere ) > 0 && is_array( $aWhere ) ) {
                // make an temp array from where array data
                foreach ( $aWhere as $k => $v ) {
                    $tmp[] = "$k = :s_$k";
                }
                // join array values with AND Operator
                $sWhere = implode( ' AND ', $tmp );
                // delete temp array
                unset( $tmp );
                // set DELETE PDO Statement
                $this->sSql  = "DELETE FROM `$sTable` WHERE $sWhere $sOther;";
                // Call PDo Prepare Statement
                $this->_oSTH = $this->prepare( $this->sSql );
                // bind delete where param
                $this->_bindPdoNameSpace( $aWhere );
                // set array data
                $this->aData = $aWhere;
                // Use try Catch 
                try {
                    if ( $this->_oSTH->execute() ) {
                        // get affected rows
                        $this->iAffectedRows = $this->_oSTH->rowCount();
                        // close pdo
                        $this->_oSTH->closeCursor();
                        // return this object
                        return $this;
                    } else {
                        self::error( $this->_oSTH->errorInfo() );
                    }
                }
                catch ( PDOException $e ) {
                    // get pdo error and pass on error method
                    self::error( $e->getMessage() . ': ' . __LINE__ );
                } // end try catch here
            } else {
                self::error( 'Not a valid where condition..' );
            }
        } else {
            self::error( 'Table name not found..' );
        }
    }
    /**
     * Return PDO Query results array/json/xml type
     *
     * @param string $type
     *
     * @return mixed
     */
    public function results( $type = 'array' ) {
        switch ( $type ) {
            case 'array':
                // return array data
                return $this->aResults;
                break;
            case 'xml':
                //send the xml header to the browser
                header( "Content-Type:text/xml" );
                // return xml content
                return $this->helper()->arrayToXml( $this->aResults );
                break;
            case 'json':
                // set header as json
                header( 'Content-type: application/json; charset="utf-8"' );
                // return json encoded data
                return json_encode( $this->aResults );
                break;
        }
    }
    /**
     * Get Total Number Of Records in Requested Table
     *
     * @param string $sTable
     * @param string $where
     * @return number
     */
    public function count( $sTable = '', $sWhere = '' ) {
        // if table name not pass
        if ( !empty( $sTable ) ) {
            if(empty($sWhere)){
                // create count query
                $this->sSql  = "SELECT COUNT(*) AS NUMROWS FROM `$sTable`;";
            }else{
                // create count query
                $this->sSql  = "SELECT COUNT(*) AS NUMROWS FROM `$sTable` WHERE $sWhere;";
            }
            // pdo prepare statement
            $this->_oSTH = $this->prepare( $this->sSql );
            try {
                if ( $this->_oSTH->execute() ) {
                    // fetch array result
                    $this->aResults = $this->_oSTH->fetch();
                    // close pdo
                    $this->_oSTH->closeCursor();
                    // return number of count
                    return $this->aResults['NUMROWS'];
                } else {
                    self::error( $this->_oSTH->errorInfo() );
                }
            }
            catch ( PDOException $e ) {
                // get pdo error and pass on error method
                self::error( $e->getMessage() . ': ' . __LINE__ );
            }
        } else {
            self::error( 'Table name not found..' );
        }
    }
    /**
     * Truncate a MySQL table
     *
     * @param string $sTable
     * @return bool
     */
    public function truncate($sTable =''){
        // if table name not pass
        if ( !empty( $sTable ) ) {
            // create count query
            $this->sSql  = "TRUNCATE TABLE `$sTable`;";
            // pdo prepare statement
            $this->_oSTH = $this->prepare( $this->sSql );
            try {
                if ( $this->_oSTH->execute() ) {
                    // close pdo
                    $this->_oSTH->closeCursor();
                    // return number of count
                    return true;
                } else {
                    self::error( $this->_oSTH->errorInfo() );
                }
            }
            catch ( PDOException $e ) {
                // get pdo error and pass on error method
                self::error( $e->getMessage() . ': ' . __LINE__ );
            }
        } else {
            self::error( 'Table name not found..' );
        }
    }

    /**
     * Drop a MySQL table
     *
     * @param string $sTable
     * @return bool
     */
    public function drop($sTable =''){
        // if table name not pass
        if ( !empty( $sTable ) ) {
            // create count query
            $this->sSql  = "DROP TABLE `$sTable`;";
            // pdo prepare statement
            $this->_oSTH = $this->prepare( $this->sSql );
            try {
                if ( $this->_oSTH->execute() ) {
                    // close pdo
                    $this->_oSTH->closeCursor();
                    // return number of count
                    return true;
                } else {
                    self::error( $this->_oSTH->errorInfo() );
                }
            }
            catch ( PDOException $e ) {
                // get pdo error and pass on error method
                self::error( $e->getMessage() . ': ' . __LINE__ );
            }
        } else {
            self::error( 'Table name not found..' );
        }
    }
    /**
     * Return Table Fields of Requested Table
     *
     * @param string $sTable
     *
     * @return array Field Type and Field Name
     */
    public function describe( $sTable = '' ) {
        $this->sSql = $sSql  = "DESC $sTable;";
        $this->_oSTH = $this->prepare( $sSql );
        $this->_oSTH->execute();
        $aColList = $this->_oSTH->fetchAll();
        foreach ( $aColList as $key ) {
            $aField[] = $key['Field'];
            $aType[]  = $key['Type'];
        }
        return array_combine( $aField, $aType );
    }

    /**
     * @param array $array_data
     * @return array
     */
    public function customWhere ($array_data = array()){
        $syntax = '';
        foreach ($array_data as $key => $value) {
            $key = trim($key);
            if(strstr($key, ' ')){
                $array = explode(' ',$key);
                if(count($array)=='2'){
                    $random = '';//"_".rand(1,100);
                    $field = $array[0];
                    $operator  = $array[1];
                    $tmp[] = "$field $operator :s_$field"."$random";
                    $syntax .= " $field $operator :s_$field"."$random ";
                }elseif(count($array)=='3'){
                    $random = '';//"_".rand(1,100);
                    $condition = $array[0];
                    $field = $array[1];
                    $operator = $array[2];
                    $tmp[] = "$condition $field $operator :s_$field"."$random";
                    $syntax .= " $condition $field $operator :s_$field"."$random ";
                }
            }
        }
        return array(
            'where' => $syntax,
            'bind' => implode(' ',$tmp)
        );
    }
    /**
     * PDO Bind Param with :namespace
     * @param array $array
     */
    private function _bindPdoNameSpace( $array = array() ) {
        if(strstr(key($array), ' ')){
            // bind array data in pdo
            foreach ( $array as $f => $v ) {
                // get table column from array key
                $field = $this->getFieldFromArrayKey($f);
                // check pass data type for appropriate field
                switch ( gettype( $array[$f] ) ):
                    // is string found then pdo param as string
                    case 'string':
                        $this->_oSTH->bindParam( ":s" . "_" . "$field", $array[$f], PDO::PARAM_STR );
                        break;
                    // if int found then pdo param set as int
                    case 'integer':
                        $this->_oSTH->bindParam( ":s" . "_" . "$field", $array[$f], PDO::PARAM_INT );
                        break;
                    // if boolean found then set pdo param as boolean
                    case 'boolean':
                        $this->_oSTH->bindParam( ":s" . "_" . "$field", $array[$f], PDO::PARAM_BOOL );
                        break;
                endswitch;
            } // end for each here
        }else{
        // bind array data in pdo
        foreach ( $array as $f => $v ) {
            // check pass data type for appropriate field
            switch ( gettype( $array[$f] ) ):
                // is string found then pdo param as string
                case 'string':
                    $this->_oSTH->bindParam( ":s" . "_" . "$f", $array[$f], PDO::PARAM_STR );
                    break;
                // if int found then pdo param set as int
                case 'integer':
                    $this->_oSTH->bindParam( ":s" . "_" . "$f", $array[$f], PDO::PARAM_INT );
                    break;
                // if boolean found then set pdo param as boolean
                case 'boolean':
                    $this->_oSTH->bindParam( ":s" . "_" . "$f", $array[$f], PDO::PARAM_BOOL );
                    break;
            endswitch;
        } // end for each here
        }
    }
    /**
     * Bind PDO Param without :namespace
     * @param array $array
     */
    private function _bindPdoParam( $array = array() ) {
        // bind array data in pdo
        foreach ( $array as $f => $v ) {
            // check pass data type for appropriate field
            switch ( gettype( $array[$f] ) ):
                // is string found then pdo param as string
                case 'string':
                    $this->_oSTH->bindParam( $f + 1, $array[$f], PDO::PARAM_STR );
                    break;
                // if int found then pdo param set as int
                case 'integer':
                    $this->_oSTH->bindParam( $f + 1, $array[$f], PDO::PARAM_INT );
                    break;
                // if boolean found then set pdo param as boolean
                case 'boolean':
                    $this->_oSTH->bindParam( $f + 1, $array[$f], PDO::PARAM_BOOL );
                    break;
            endswitch;
        } // end for each here
    }
    /**
     * Catch Error in txt file
     *
     * @param mixed $msg
     */
    public function error( $msg ) {
        // log set as true
        if ( $this->log ) {
            // show executed query with error
            $this->showQuery();
            // die code
            $this->helper()->errorBox($msg);
        } else {
            // show error message in log file
            file_put_contents( self::ERROR_LOG_FILE, date( 'Y-m-d h:m:s' ) . ' :: ' . $msg . "\n", FILE_APPEND );
            // die with user message
            $this->helper()->error();
        }
    }
    /**
     * Show executed query on call
     * @param boolean $logfile set true if wanna log all query in file
     * @return PdoWrapper
     */
    public function showQuery($logfile=false) {
        if(!$logfile){
            echo "<div style='color:#990099; border:1px solid #777; padding:2px; background-color: #E5E5E5;'>";
            echo " Executed Query -> <span style='color:#008000;'> ";
            echo $this->helper()->formatSQL( $this->interpolateQuery() );
            echo "</span></div>";
            return $this;
        }else{
            // show error message in log file
            file_put_contents( self::SQL_LOG_FILE, date( 'Y-m-d h:m:s' ) . ' :: ' . $this->interpolateQuery() . "\n", FILE_APPEND );
            return $this;
        }
    }
    /**
     * Replaces any parameter placeholders in a query with the value of that
     * parameter. Useful for debugging. Assumes anonymous parameters from
     *
     * @return mixed
     */
    protected function interpolateQuery() {
        $sql = $this->_oSTH->queryString;
        // handle insert batch data
       if(!$this->batch){
        $params = ( ( is_array( $this->aData ) ) && ( count( $this->aData ) > 0 ) ) ? $this->aData : $this->sSql;
        if ( is_array( $params ) ) {
            # build a regular expression for each parameter
            foreach ( $params as $key => $value ) {
                if(strstr($key, ' ')){
                    $real_key = $this->getFieldFromArrayKey($key);
                    // update param value with quotes, if string value
                    $params[$key] = is_string( $value ) ? '"' . $value . '"' : $value;
                    // make replace array
                    $keys[]       = is_string( $real_key ) ? '/:s_' . $real_key . '/' : '/[?]/';
                }else{
                    // update param value with quotes, if string value
                    $params[$key] = is_string( $value ) ? '"' . $value . '"' : $value;
                    // make replace array
                    $keys[]       = is_string( $key ) ? '/:s_' . $key . '/' : '/[?]/';
                }
            }
            $sql = preg_replace( $keys, $params, $sql, 1, $count );

            if(strstr($sql,':s_')){
                foreach ( $this->aWhere as $key => $value ) {
                    if(strstr($key, ' ')){
                        $real_key = $this->getFieldFromArrayKey($key);
                        // update param value with quotes, if string value
                        $params[$key] = is_string( $value ) ? '"' . $value . '"' : $value;
                        // make replace array
                        $keys[]       = is_string( $real_key ) ? '/:s_' . $real_key . '/' : '/[?]/';
                    }else{
                        // update param value with quotes, if string value
                        $params[$key] = is_string( $value ) ? '"' . $value . '"' : $value;
                        // make replace array
                        $keys[]       = is_string( $key ) ? '/:s_' . $key . '/' : '/[?]/';
                    }
                }
                $sql = preg_replace( $keys, $params, $sql, 1, $count );
            }
            return $sql;
            #trigger_error('replaced '.$count.' keys');
        } else {
            return $params;
        }
       }else{
           $params_batch = ( ( is_array( $this->aData ) ) && ( count( $this->aData ) > 0 ) ) ? $this->aData : $this->sSql;
           $batch_query = '';
           if ( is_array( $params_batch ) ) {
               # build a regular expression for each parameter
               foreach ($params_batch as $keys => $params){
                   echo $params;
                   foreach ( $params as $key => $value ) {
                       if(strstr($key, ' ')){
                           $real_key = $this->getFieldFromArrayKey($key);
                           // update param value with quotes, if string value
                           $params[$key] = is_string( $value ) ? '"' . $value . '"' : $value;
                           // make replace array
                           $array_keys[]       = is_string( $real_key ) ? '/:s_' . $real_key . '/' : '/[?]/';
                       }else{
                           // update param value with quotes, if string value
                           $params[$key] = is_string( $value ) ? '"' . $value . '"' : $value;
                           // make replace array
                           $array_keys[]       = is_string( $key ) ? '/:s_' . $key . '/' : '/[?]/';
                       }
                   }
                   $batch_query .= "<br />".preg_replace( $array_keys, $params, $sql, 1, $count );
               }
               return $batch_query;
               #trigger_error('replaced '.$count.' keys');
           } else {
               return $params_batch;
           }
       }
    }
    /**
     * Return real table column from array key
     * @param array $array_key
     * @return mixed
     */
    public function getFieldFromArrayKey($array_key=array()){
        // get table column from array key
        $key_array = explode(' ',$array_key);
        // check no of chunk
        return (count($key_array)=='2') ? $key_array[0] : ((count($key_array)> 2) ? $key_array[1] : $key_array[0]);
    }
    /**
     * Set PDO Error Mode to get an error log file or true to show error on screen
     *
     * @param bool $mode
     */
    public function setErrorLog( $mode = false ) {
        $this->log = $mode;
    }
}
/** Class End **/
?>
