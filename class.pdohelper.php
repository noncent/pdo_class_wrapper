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
 * @category   PHP Helper Class
 * @package    PdoHelper
 * @author     Neeraj Singh <neeraj.singh@lbi.co.in>
 * @author     Bhaskar Rabha <bhaskar.rabha@lbi.co.in>
 * @author     Priyadarshan Salkar <priyadarshan.salkar@lbi.co.in>
 * @copyright  2013-14 The PHP Group Of LBi India
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0 Beta
 */
/** Class Start **/
class PdoHelper {
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
}
/** Class End **/
?>
