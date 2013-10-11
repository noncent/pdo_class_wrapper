<?php
/**
 * PdoHelper
 *
 * Pdo Helper for using PDO methods with Helper functions
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
 * @category   PHP PDO Helper Class
 * @package    PdoHelper
 * @author     Neeraj Singh <neeraj.singh@lbi.co.in>
 * @author     Bhaskar Rabha <bhaskar.rabha@lbi.co.in>
 * @author     Priyadarshan Salkar <priyadarshan.salkar@lbi.co.in>
 * @copyright  2013-14 The PHP Group Of LBi India
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0 Beta
 */
/** Class Start **/
class PDOHelper {
    /**
     * function definition to convert array to xml
     * send an array and get xml
     *
     * @param array $arrayData
     *
     * @return string
     */
    public function arrayToXml( $arrayData = array() ) {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .= "<root>";
        foreach ( $arrayData as $key => $value ) {
            $xml .= "<xml_data>";
            if ( is_array( $value ) ) {
                foreach ( $value as $k => $v ) {
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
     * Format the SQL Query
     *
     * @param $sql string
     * @return mixed
     */
    public function formatSQL( $sql = '' ) {
        // Reserved SQL Keywords Data
        $reserveSqlKey = "select|insert|update|delete|truncate|drop|create|add|except|percent|all|exec|plan|alter|execute|precision|and|exists|primary|any|exit|print|as|fetch|proc|asc|file|procedure|authorization|fillfactor|public|backup|for|raiserror|begin|foreign|read|between|freetext|readtext|break|freetexttable|reconfigure|browse|from|references|bulk|full|replication|by|function|restore|cascade|goto|restrict|case|grant|return|check|group|revoke|checkpoint|having|right|close|holdlock|rollback|clustered|identity|rowcount|coalesce|identity_insert|rowguidcol|collate|identitycol|rule|column|if|save|commit|in|schema|compute|index|select|constraint|inner|session_user|contains|insert|set|containstable|intersect|setuser|continue|into|shutdown|convert|is|some|create|join|statistics|cross|key|system_user|current|kill|table|current_date|left|textsize|current_time|like|then|current_timestamp|lineno|to|current_user|load|top|cursor|national|tran|database|nocheck|transaction|dbcc|nonclustered|trigger|deallocate|not|truncate|declare|null|tsequal|default|nullif|union|delete|of|unique|deny|off|update|desc|offsets|updatetext|disk|on|use|distinct|open|user|distributed|opendatasource|values|double|openquery|varying|drop|openrowset|view|dummy|openxml|waitfor|dump|option|when|else|or|where|end|order|while|errlvl|outer|with|escape|over|writetext|absolute|overlaps|action|pad|ada|partial|external|pascal|extract|position|allocate|false|prepare|first|preserve|float|are|prior|privileges|fortran|assertion|found|at|real|avg|get|global|relative|go|bit|bit_length|both|rows|hour|cascaded|scroll|immediate|second|cast|section|catalog|include|char|session|char_length|indicator|character|initially|character_length|size|input|smallint|insensitive|space|int|sql|collation|integer|sqlca|sqlcode|interval|sqlerror|connect|sqlstate|connection|sqlwarning|isolation|substring|constraints|sum|language|corresponding|last|temporary|count|leading|time|level|timestamp|timezone_hour|local|timezone_minute|lower|match|trailing|max|min|translate|date|minute|translation|day|module|trim|month|true|dec|names|decimal|natural|unknown|nchar|deferrable|next|upper|deferred|no|usage|none|using|describe|value|descriptor|diagnostics|numeric|varchar|disconnect|octet_length|domain|only|whenever|work|end-exec|write|year|output|zone|exception|free|admin|general|after|reads|aggregate|alias|recursive|grouping|ref|host|referencing|array|ignore|result|returns|before|role|binary|initialize|rollup|routine|blob|inout|row|boolean|savepoint|breadth|call|scope|search|iterate|large|sequence|class|lateral|sets|clob|less|completion|limit|specific|specifictype|localtime|constructor|localtimestamp|sqlexception|locator|cube|map|current_path|start|current_role|state|cycle|modifies|statement|data|modify|static|structure|terminate|than|nclob|depth|new|deref|destroy|treat|destructor|object|deterministic|old|under|dictionary|operation|unnest|ordinality|out|dynamic|each|parameter|variable|equals|parameters|every|without|path|postfix|prefix|preorder";
        // convert in array
        $list = explode('|',$reserveSqlKey);
        foreach ($list as &$verb) {
            $verb = '/\b' . preg_quote($verb, '/') . '\b/';
        }
        $regex_sign = array('/\b','\b/');
        // replace matching words
        return str_replace($regex_sign,'',preg_replace( $list, array_map( array(
            $this,
            'highlight_sql'
        ), $list ), strtolower( $sql ) ));
    }
    /**
     * Coloring for MySQL reserved keywords
     *
     * @param $param
     * @return string
     */
    public function highlight_sql( $param ) {
        return "<span style='color:#990099; font-weight:bold; text-transform: uppercase;'>$param</span>";
    }
    /**
     * Get HTML Table with Data
     * Send complete array data and get an HTML table with mysql data
     *
     * @param array $aColList Result Array data
     * @return string HTML Table with data
     */
    public function displayHtmlTable( $aColList = array() ) {
        $r        = '';
        if ( count( $aColList ) > 0 ) {
            $r .= '<table border="1">';
            $r .= '<thead>';
            $r .= '<tr>';
            foreach ( $aColList[0] as $k => $v ) {
                $r .= '<td>' . $k . '</td>';
            }
            $r .= '</tr>';
            $r .= '</thead>';
            $r .= '<tbody>';
            foreach ( $aColList as $record ) {
                $r .= '<tr>';
                foreach ( $record as $data ) {
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
     * @param array $array
     * @return bool true/false
     */
    public function isAssocArray( $array = array() ) {
        return array_keys( $array ) !== range( 0, count( $array ) - 1 );
    }
    /**
     * Function to print array with pre tag
     *
     * @param array $array
     */
    public function PA( $array ) {
        echo '<pre>', print_r( $array, true ), '</pre>';
    }
    /**
     * Show Error to user
     */
    public function error(){
        $style = "style='color:#333846; border:1px solid #777; padding:2px; background-color: #FFC0CB;'";
        die( "<div $style >ERROR: error occurred. Please, Check you error log file.</div>" );
    }
    /**
     * Show Error Array Data and stop code execution
     * @param array $data
     */
    public function errorBox( $data = array() ) {
        $style = "style='color:#333846; border:1px solid #777; padding:2px; background-color: #FFC0CB;'";
        die( "<div $style >ERROR:" . json_encode( $data ) . "</div>" );
    }

}
/** Class End **/
?>
