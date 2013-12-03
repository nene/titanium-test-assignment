<?php

/**
 * Transforms SearchCarResponse data structure into XML.
 */
class SearchCarResponse
{
    /**
     * Converts nested array into XML string.
     * @param {array} $countries
     * @return {string} XML
     */
    function toXml($countries)
    {
        $root = new SimpleXMLElement("<SearchCarRS></SearchCarRS>");

        foreach ($countries as $country) {
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
