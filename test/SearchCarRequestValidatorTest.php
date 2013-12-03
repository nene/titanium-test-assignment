<?php
set_include_path(dirname(dirname(__FILE__)) . '/lib');
require_once 'SearchCarRequestValidator.php';
require_once 'XmlValidator.php';
require_once 'XmlValidatorException.php';

/**
 * Here we really only test the schema defined in
 * SearchCarRequestValidator, which we can't really do without using
 * the real XmlValidator instance.  So it's technically an integration
 * test.
 */
class SearchCarRequestValidatorTest extends PHPUnit_Framework_TestCase
{

    function validate($xml)
    {
        $xmlElement = new SimpleXMLElement($xml);
        $validator = new XmlValidator();

        return (new SearchCarRequestValidator($validator))->validate($xmlElement);
    }

    // one happy path

    function testSimpleExample()
    {
        $this->validate("
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
    }

    // a few failure conditions

    /**
     * @expectedException XmlValidatorException
     */
    function testEmptyRoot()
    {
        $this->validate("
            <SearchCarRQ>
            </SearchCarRQ>
        ");

        $this->assertEquals(0, count($countries));
    }

    /**
     * @expectedException XmlValidatorException
     */
    function testEmptyCountry()
    {
        $this->validate("
            <SearchCarRQ>
                <Country name='Austria'>
                </Country>
            </SearchCarRQ>
        ");
    }

    /**
     * @expectedException XmlValidatorException
     */
    function testCountriesWithEmptyName()
    {
        $this->validate("
            <SearchCarRQ>
                <Country name=''>
                </Country>
            </SearchCarRQ>
        ");
    }

    /**
     * @expectedException XmlValidatorException
     */
    function testEmptyCity()
    {
        $this->validate("
            <SearchCarRQ>
                <Country name='Austria'>
                    <City></City>
                </Country>
            </SearchCarRQ>
        ");
    }
}
