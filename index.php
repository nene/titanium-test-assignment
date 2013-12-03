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

// initialization
$validator = new SearchCarRequestValidator(new XmlValidator());
$parser = new SearchCarRequestParser();
$searchCarQuery = new SearchCarQuery(new PriceQuery(DbFactory::create()));

try {
    $xml = $_POST["query"];

    // avoid PHP warnings - we're catching the exception anyway.
    $xmlElement = @(new SimpleXMLElement($xml));

    $validator->validate($xmlElement);

    $query = $parser->parse($xmlElement);

    $response = $searchCarQuery->query($query);

    echo (new SearchCarResponse())->toXml($response);
}
catch (Exception $e) {
    echo (new ErrorResponse())->toXml($e);
}
