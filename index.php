<?php
set_include_path(dirname(__FILE__) . '/lib');
require_once 'XmlValidator.php';
require_once 'SearchCar/Task.php';
require_once 'ErrorResponse.php';
require_once 'DbFactory.php';

// XML is the only possibly response from this script, so set the
// correct heading for both success and error XML responses.
header("Content-type: text/xml; charset=utf8");

try {
    if (!isset($_POST["query"])) {
        throw new Exception("HTTP POST is missing 'query' parameter");
    }

    // avoid PHP warnings - we're catching the exception anyway.
    $xmlElement = @(new SimpleXMLElement($_POST["query"]));

    // set up objects possibly used in all queries
    $xmlValidator = new XmlValidator();
    $db = DbFactory::createFrom(dirname(__FILE__) . "/db-config.ini");

    // switch by different request types
    if ($xmlElement->getName() == "SearchCarRQ") {
        $task = new SearchCar_Task($xmlValidator, $db);
    }
    else {
        throw new Exception("Unknown XML request type: {$xmlElement->getName()}");
    }

    // perform the actual work
    echo $task->run($xmlElement);
}
catch (Exception $e) {
    echo (new ErrorResponse($e))->toXml();
}
