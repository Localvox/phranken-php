<?php
namespace MarketMeSuite\Phranken\Util;

/**
 * Class CSVUtils
 * @package MarketMeSuite\Phranken\Util
 */
class CSVUtils
{
    /**
     * @var int The maximum characters to read per line
     */
    public static $MAX_LINE_LENGTH = 8000;

    /**
     * @var int The max size in bytes. default is 5MB
     */
    public static $MAX_CSV_EXPORT_FILE_SIZE = 5242880;

    /**
     * @param string   $path
     * @param array    $map          An associative array where
     *                               key    = index of row data and
     *                               $value = key used in parsed array
     * @param callback $dataFunction A function to use to parse each row of data
     *                               function ($row, $data, &$parsedData) {
     *                                   $parsedData[] = $data;
     *                               }
     *                               If defined this will override the $map parameter for all parsing
     * @param array    $skipRows     An array of row numbers to skip parsing
     *
     * @return array|bool            The final parsed data
     */
    public static function readCSV($path, array $map, $dataFunction = null, array $skipRows = null)
    {
        if (!file_exists($path)) {
            return false;
        }

        // the final parsed data
        $parsedData = array();

        $row = 1;
        if (($handle = fopen($path, "r")) !== false) {
            while (($data = fgetcsv($handle, static::$MAX_LINE_LENGTH, ",")) !== false) {

                // skip rows that have been provided in $skipRows
                if ($skipRows !== null) {

                    // if only one skip row exists then don't use in_array
                    if (count($skipRows) === 1 && $skipRows[0] === $row) {
                        $row++;
                        continue;
                    }

                    if (in_array($row, $skipRows)) {
                        $row++;
                        continue;
                    }
                }

                // determine whether to use the $map
                // or the provided dataFunction
                if (is_callable($dataFunction)) {
                    $dataFunction($row, $data, $parsedData);
                } else {

                    $parsedRow = array();

                    // convert row data to key => value
                    // based on the provided $map
                    foreach ($map as $key => $value) {
                        if (array_key_exists($key, $data)) {
                            $parsedRow[$value] = $data[$key];
                        }
                    }

                    $parsedData[] = $parsedRow;
                }

                $row++;
            }

            fclose($handle);
        }

        return $parsedData;
    }

    /**
     * Converts an array of rows
     *
     * @param array $list
     *
     * @return string A constructed CSV
     */
    public static function createCSVFromRows(array $list)
    {
        // create a temporary file handle
        $maxSize = static::$MAX_CSV_EXPORT_FILE_SIZE;
        $fp = fopen("php://temp/maxmemory:$maxSize", 'w+');

        // Put all the rows into the CSV
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        // back to the beginning of the file
        rewind($fp);

        // final storage for $csv
        $csv = '';

        // make sure we read back the entire csv
        $readString = fread($fp, 1024);

        while (!empty($readString)) {
            $csv .= $readString;
            $readString = fread($fp, 1024);
        }

        // close file
        fclose($fp);

        return $csv;
    }
}
