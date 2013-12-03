<?php
set_include_path(dirname(__FILE__) . '/lib');
require_once 'XmlValidator.php';
require_once 'SearchCarRequestValidator.php';
require_once 'SearchCarRequestParser.php';
require_once 'SearchCarQuery.php';
require_once 'PriceQuery.php';
require_once 'SearchCarResponse.php';

$xml = "
<SearchCarRQ>
    <Country name='Austria'>
        <City>Linz</City>
        <City>Salzburg</City>
    </Country>
    <Country name='Portugal'>
        <City>Faro</City>
    </Country>
</SearchCarRQ>
";

$xmlElement = new SimpleXMLElement($xml);

$validator = new SearchCarRequestValidator(new XmlValidator());
$validator->validate($xmlElement);

$parser = new SearchCarRequestParser();
$query = $parser->parse($xmlElement);

print_r($query);

$db = new PDO("mysql:host=localhost;dbname=car_prices", "nene", "");
$searchCarQuery = new SearchCarQuery(new PriceQuery($db));
$response = $searchCarQuery->query($query);

print_r($response);

echo (new SearchCarResponse())->toXml($response);
