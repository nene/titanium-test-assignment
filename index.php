<?php
set_include_path(dirname(__FILE__) . '/lib');
require_once 'XmlValidator.php';
require_once 'SearchCarRequestValidator.php';
require_once 'SearchCarRequestParser.php';

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
