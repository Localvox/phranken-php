<?php
namespace MarketMeSuite\Phranken\Commandline;


/**
 * manages rendering an associative array to a text-base table
 */
class ConsoleTable
{
    
    /**
     * An associative array where $key = colum heading and
     * $value = an array of row values for the associated column
     * @var array
     */
    private $tableData = null;

    /**
     * @deprecated
     * @var function
     */
    private $valueRenderer = null;
    
    public function __construct(array $data = null, $valueRenderer = null)
    {
        $this->tableData = $data;
        $this->valueRenderer = $valueRenderer;
    }

    /**
     * @return array An associative array where $key = colum heading and
     *               $value = an array of row values for the associated column
     */
    public function getTableData()
    {
        return $this->tableData;
    }
    
    /**
     * draws the table into a string
     * @param  integer $padding The amount of padding for row values
     * @return string           The rendered table, ready to be printed
     */
    public function drawTable($padding = 3)
    {
        $pad = str_pad('', $padding, ' ');
        
        $out = '';
        $colWidths = array();
        $longestRow = $this->getLongestRow($this->tableData);
        
        foreach ($this->tableData as $colKey => $rowsData) {

            $heading = str_pad($colKey, $this->getColMaxWidth($colKey, $rowsData), ' ');
            $count = strlen($heading);
            $colWidths[$colKey] = $count;
            $out .= $heading . $pad;
        }
        
        $out .= PHP_EOL;
        
        for ($i = 0; $i < $longestRow; $i++) {

            foreach ($this->tableData as $colKey => $rowsData) {

                if (isset($rowsData[$i])) {
                    $out .= str_pad($rowsData[$i], $colWidths[$colKey], ' ') . $pad;
                } else {
                    $out .= str_pad('', $colWidths[$colKey], ' ') . $pad;
                }
            }
            
            $out .= PHP_EOL;
        }
        
        return $out;
    }

    /**
     * @deprecated This was planned but never fully implemented
     */
    public function applyValueRenderer(&$data)
    {
        if ($this->valueRenderer === null) {
            return false;
        }

        foreach ($data as $key => $value) {
            $data[$key] = $this->valueRenderer($value);
        }

        return $data;
    }
    
    /**
     * Gets the maximum column width in the provided row data
     * @param  string $heading The heading for this column. This is used for the minimum
     * @param  array  $data    An array of row values in this column
     * @return integer         The maximum width that the column needs to be to fix
     *                         all row values
     */
    public function getColMaxWidth($heading, array $data)
    {
        return ( strlen($heading) > $this->arrayGetMaxWidth($data) ) ? strlen($heading) : $this->arrayGetMaxWidth($data);
    }
    
    /**
     * gets the maximum length of the values in $arr
     * @param  array  $arr An array of values. Values will be coarsed to strings
     * @return integer     The length of the longest value in $arr
     */
    public function arrayGetMaxWidth(array $arr)
    {
        $val = 0;
        foreach ($arr as $value) {

            if (strlen($value) > $val) {
                $val = strlen($value);
            }
        }
        return $val;
    }
    
    /**
     * Finds the longest row and returns the number of columns in that row
     * @param  array  $data The table data. An associative array where $key = colum heading and
     *                      $value = an array of row values for the associated column
     * @return integer      The longest row width by number of rows
     */
    public function getLongestRow(array $data)
    {
        $longest = 0;

        foreach ($data as $colKey => $rowsData) {

            if (count($rowsData) > $longest) {
                $longest = count($rowsData);
            }
        }
        
        return $longest;
    }
}
