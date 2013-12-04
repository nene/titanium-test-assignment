<?php

/**
 * Transforms SearchCar response data structure into XML.
 */
class SearchCar_Response
{
    private $countries;

    /**
     * Initializes with the response data structure.
     * @param {array} $countries
     */
    function __construct($countries)
    {
        $this->countries = $countries;
    }

    /**
     * Returns the response data as XML string.
     * @return {string} XML
     */
    function toXml()
    {
        $root = new SimpleXMLElement("<SearchCarRS></SearchCarRS>");

        foreach ($this->countries as $country) {
            $countryEl = $root->addChild("Country");
            $countryEl->addAttribute("name", $country["name"]);

            foreach ($country["cities"] as $city) {
                $cityEl = $countryEl->addChild("City");
                $cityEl->addAttribute("name", $city["name"]);

                foreach ($city["cars"] as $car) {
                    $carEl = $cityEl->addChild("Car", $car["car_type"]);
                    $carEl->addAttribute("price", $car["price"]);
                }
            }
        }

        return $root->asXML();
    }
}
