<?php
set_include_path(dirname(dirname(dirname(__FILE__))) . '/lib');
require_once 'SearchCar/Query.php';

class PriceQueryStub
{
    private $result;

    function __construct($result)
    {
        $this->result = $result;
    }

    function query($country, $city)
    {
        return $this->result;
    }
}


class SearchCar_QueryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->cars = [
            ["car_type" => "Opel Zafira", "price" => "25.12"],
            ["car_type" => "BMW X2", "price" => "28.10"],
            ["car_type" => "Skoda Octavia", "price" => "33.22"],
        ];

        $priceQuery = new PriceQueryStub($this->cars);

        $this->query = new SearchCar_Query($priceQuery);
    }

    function testSimpleExample()
    {
        $countries = $this->query->query([
            ["name" => "Austria", "cities" => ["Linz", "Salzburg"]],
        ]);

        $this->assertEquals(1, count($countries));
        $this->assertEquals('Austria', $countries[0]['name']);

        $this->assertEquals(2, count($countries[0]['cities']));

        $this->assertEquals('Linz', $countries[0]['cities'][0]["name"]);
        $this->assertEquals($this->cars, $countries[0]['cities'][0]["cars"]);

        $this->assertEquals('Salzburg', $countries[0]['cities'][1]["name"]);
        $this->assertEquals($this->cars, $countries[0]['cities'][1]["cars"]);
    }

}
