<?php

/**
 * Reads a CSV file with a heading row, converting it into array of
 * records.
 */
class CsvReader
{
    const FIELD_SEPARATOR = ";";

    /**
     * Performs the reading.
     *
     * @param {string} $filename The CSV file to read.
     *
     * @returns {array} Rows where each row is an assoc-array mapping the
     * names of the columns to values of the particular row.  The names of
     * the columns are read from the first line in CSV file.
     */
    function read($filename)
    {
        return $this->withOpenFile($filename, [$this, 'readCsvFile']);
    }

    /**
     * Opens a file and passes its handle to given callback.  After the
     * latter finishes, closes the file and returns the value returned by
     * callback.
     *
     * Exits with an exception when file can't be read.
     */
    private function withOpenFile($filename, callable $callback)
    {
        $handle = fopen($filename, "r");
        if ($handle === false) {
            throw new Exception("CsvReader: Unable to read file: $filename");
        }

        $result = $callback($handle);

        fclose($handle);

        return $result;
    }

    /**
     * Reads CSV data from file handle.
     */
    private function readCsvFile($fileHandle)
    {
        $rows = [];
        $columnNames = false;

        while (($cols = fgetcsv($fileHandle, 1000, self::FIELD_SEPARATOR)) !== false) {
            if (!$columnNames) {
                // Remember column names from first line
                $columnNames = $cols;
            }
            else {
                // map column names to values in data-row
                $row = [];
                foreach ($columnNames as $i => $name) {
                    $row[$name] = $cols[$i];
                }
                $rows[]= $row;
            }
        }

        return $rows;
    }
}
