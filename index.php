<?php
set_include_path(dirname(__FILE__) . '/lib');
require_once 'XmlValidator.php';
require_once 'SearchCar/RequestValidator.php';
require_once 'SearchCar/RequestParser.php';
require_once 'SearchCar/Query.php';
require_once 'SearchCar/PriceQuery.php';
require_once 'SearchCar/Response.php';
require_once 'ErrorResponse.php';
require_once 'DbFactory.php';

// XML is the only possibly response from this script, so set the
// correct heading for both success and error XML responses.
header("Content-type: text/xml; charset=utf8");

function handleSearchCarRq($xmlElement)
{
    // initialization
    $validator = new SearchCar_RequestValidator(new XmlValidator());
    $parser = new SearchCar_RequestParser();
    $searchCarQuery = new SearchCar_Query(new SearchCar_PriceQuery(DbFactory::create()));

    // actual work
    $validator->validate($xmlElement);
    $query = $parser->parse($xmlElement);
    $response = $searchCarQuery->query($query);
    return (new SearchCar_Response($response))->toXml();
}

try {
    if (!isset($_POST["query"])) {
        throw new Exception("HTTP POST is missing 'query' parameter");
    }

    // avoid PHP warnings - we're catching the exception anyway.
    $xmlElement = @(new SimpleXMLElement($_POST["query"]));

    // here we can possibly handle several different request types.
    if ($xmlElement->getName() == "SearchCarRQ") {
        echo handleSearchCarRq($xmlElement);
    }
    else {
        throw new Exception("Unknown XML request type: {$xmlElement->getName()}");
    }
}
catch (Exception $e) {
    echo (new ErrorResponse($e))->toXml();
}
