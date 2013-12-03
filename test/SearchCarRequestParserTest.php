<?php
set_include_path(dirname(dirname(__FILE__)) . '/lib');
require_once 'SearchCarRequestParser.php';


class SearchCarRequestParserTest extends PHPUnit_Framework_TestCase
{

    function parse($xml)
    {
        $xmlElement = new SimpleXMLElement($xml);

        return (new SearchCarRequestParser())->parse($xmlElement);
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
        $this->assertEquals('Austria', $countries[0]['name']);
        $this->assertEquals('Portugal', $countries[1]['name']);

        $this->assertEquals(2, count($countries[0]['cities']));
        $this->assertEquals('Linz', $countries[0]['cities'][0]);
        $this->assertEquals('Salzburg', $countries[0]['cities'][1]);

        $this->assertEquals(1, count($countries[1]['cities']));
        $this->assertEquals('Faro', $countries[1]['cities'][0]);
    }

    function testEmptyRoot()
    {
        $countries = $this->parse("
            <SearchCarRQ>
            </SearchCarRQ>
        ");

        $this->assertEquals(0, count($countries));
    }

    function testEmptyCountry()
    {
        $countries = $this->parse("
            <SearchCarRQ>
                <Country name='Austria'>
                </Country>
            </SearchCarRQ>
        ");

        $this->assertEquals(1, count($countries));
        $this->assertEquals(0, count($countries[0]['cities']));
    }

}
