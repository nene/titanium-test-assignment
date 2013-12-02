<?php
/**
 * Imports car prices database data from CSV files.
 *
 * Run me like this:
 *
 *    $ php import.php a.csv b.csv
 */


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
     */
    private function withOpenFile($filename, callable $callback)
    {
        $handle = fopen($filename, "r");
        if ($handle === false) {
            echo "Unable to read file: $filename\n";
            exit(1);
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
                array_push($rows, $row);
            }
        }

        return $rows;
    }
}

/**
 * Imports car prices data to database.
 */
class Importer
{
    private $db;
    private $rows;

    // For memorizing the primary key values
    private $countries = [];
    private $carTypes = [];
    private $cities = [];

    function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Performs the import.
     *
     * @param {array} $rows Assoc-records to import, as generated by
     * CsvReader.
     */
    function import($rows)
    {
        $this->rows = $rows;

        // Doing this all inside a transaction will eliminate the
        // foreign key checks, speeding up the import process
        // considerably.
        $this->db->beginTransaction();

        $this->insertCountries();
        $this->insertCities();
        $this->insertCarTypes();
        $this->insertCarPrices();

        $this->db->commit();
    }

    private function insertCountries()
    {
        $statement = $this->db->prepare("INSERT INTO countries SET name = :name");

        $name = null;
        $statement->bindParam(':name', $name, PDO::PARAM_STR, 255);

        foreach ($this->rows as $row) {
            $name = $row['Country'];
            if (!isset($this->countries[$name])) {
                $statement->execute();
                $this->countries[$name] = $this->db->lastInsertId();
            }
        }
    }

    private function insertCarTypes()
    {
        $statement = $this->db->prepare("INSERT INTO car_types SET name = :name");

        $name = null;
        $statement->bindParam(':name', $name, PDO::PARAM_STR, 255);

        foreach ($this->rows as $row) {
            $name = $row['Car type'];
            if (!isset($this->carTypes[$name])) {
                $statement->execute();
                $this->carTypes[$name] = $this->db->lastInsertId();
            }
        }
    }

    private function insertCities()
    {
        $statement = $this->db->prepare("
            INSERT INTO cities SET
                country_id = :country_id,
                name = :name");

        $countryId = null;
        $name = null;
        $statement->bindParam(':country_id', $countryId, PDO::PARAM_INT);
        $statement->bindParam(':name', $name, PDO::PARAM_STR, 255);

        foreach ($this->rows as $row) {
            $countryId = $this->countries[$row['Country']];
            $name = $row['City'];
            $fullName = $countryId . "/" . $row['City'];
            if (!isset($this->cities[$fullName])) {
                $statement->execute();
                $this->cities[$fullName] = $this->db->lastInsertId();
            }
        }
    }

    private function insertCarPrices()
    {
        $statement = $this->db->prepare("
            INSERT INTO car_prices SET
                city_id = :city_id,
                car_type_id = :car_type_id,
                price = :price");

        $countryId = null;
        $cityId = null;
        $carTypeId = null;
        $price = null;
        $statement->bindParam(':city_id', $cityId, PDO::PARAM_INT);
        $statement->bindParam(':car_type_id', $carTypeId, PDO::PARAM_INT);
        $statement->bindParam(':price', $price, PDO::PARAM_STR, 255);

        foreach ($this->rows as $row) {
            $countryId = $this->countries[$row["Country"]];
            $cityId = $this->cities[$countryId . "/" . $row["City"]];
            $carTypeId = $this->carTypes[$row['Car type']];
            $price = $row['Price'];
            $statement->execute();
        }
    }

}


$filenames = array_slice($argv, 1);
$data = [];

foreach ($filenames as $fname) {
    $data = array_merge($data, (new CsvReader($fname))->read());
}

$db = new PDO("mysql:host=localhost;dbname=car_prices", "nene", "");

(new Importer($db))->import($data);



?>