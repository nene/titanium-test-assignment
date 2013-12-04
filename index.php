<?php
set_include_path(dirname(__FILE__) . '/lib');
require_once 'XmlValidator.php';
require_once 'SearchCarRequestValidator.php';
require_once 'SearchCarRequestParser.php';
require_once 'SearchCarQuery.php';
require_once 'PriceQuery.php';
require_once 'SearchCarResponse.php';
require_once 'ErrorResponse.php';
require_once 'DbFactory.php';

// XML is the only possibly response from this script, so set the
// correct heading for both success and error XML responses.
header("Content-type: text/xml; charset=utf8");

function handleSearchCarRq($xmlElement)
{
    // initialization
    $validator = new SearchCarRequestValidator(new XmlValidator());
    $parser = new SearchCarRequestParser();
    $searchCarQuery = new SearchCarQuery(new PriceQuery(DbFactory::create()));

    // actual work
    $validator->validate($xmlElement);
    $query = $parser->parse($xmlElement);
    $response = $searchCarQuery->query($query);
    return (new SearchCarResponse($response))->toXml();
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
