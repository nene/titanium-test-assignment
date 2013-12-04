<?php
/**
 * Imports car prices database data from CSV files.
 *
 * Run me like this:
 *
 *    $ php import.php a.csv b.csv
 */

set_include_path(dirname(dirname(__FILE__)) . '/lib');
require_once 'CsvReader.php';
require_once 'CarPricesImporter.php';
require_once 'DbFactory.php';


$filenames = array_slice($argv, 1);
$data = [];

foreach ($filenames as $fname) {
    $data = array_merge($data, (new CsvReader())->read($fname));
}

$db = DbFactory::createFrom(dirname(dirname(__FILE__)) . "/db-config.ini");

(new CarPricesImporter($db))->import($data);



?>