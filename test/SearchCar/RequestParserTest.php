<?php
set_include_path(dirname(dirname(dirname(__FILE__))) . '/lib');
require_once 'SearchCar/RequestParser.php';


class SearchCar_RequestParserTest extends PHPUnit_Framework_TestCase
{

    function parse($xml)
    {
        $xmlElement = new SimpleXMLElement($xml);

        return (new SearchCar_RequestParser())->parse($xmlElement);
    }

    // here we're only testing happy paths, because the input to the
    // parser must be already validated XmlValidator.

    function testSimpleExample()
    {
        $countries = $this->parse("
            <SearchCarRQ>
                <Country name='Austria'>
                    <City>Linz</City>
                    <City>Salzburg</City>
                </Country>
                <Country name='Portugal'>
                    <City>Faro</City>
                </Country>
            </SearchCarRQ>
        ");

        $this->assertEquals(2, count($countries));
        $this->assertSame('Austria', $countries[0]['name']);
        $this->assertSame('Portugal', $countries[1]['name']);

        $this->assertEquals(2, count($countries[0]['cities']));
        $this->assertSame('Linz', $countries[0]['cities'][0]);
        $this->assertSame('Salzburg', $countries[0]['cities'][1]);

        $this->assertEquals(1, count($countries[1]['cities']));
        $this->assertSame('Faro', $countries[1]['cities'][0]);
    }

}
