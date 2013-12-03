<?php

/**
 * Transforms SearchCarRequest XML into simple array with structure:
 *
 *     [
 *         ["name" => "Austria", "cities" => ["Linz", "Salzburg"]],
 *         ["name" => "Portugal", "cities" => ["faro"]],
 *     ]
 *
 */
class SearchCarRequestParser
{
    /**
     * Does the parsing.
     * @param {SimpleXMLElement} $xmlElement
     * @return {array}
     */
    function parse($xmlElement)
    {
        $countries = [];

        foreach ($xmlElement->Country as $countryElement) {
            $country = [
                'name' => (string)$countryElement['name'],
                'cities' => [],
            ];

            foreach ($countryElement->City as $cityName) {
                $country['cities'][]= (string)$cityName;
            }

            $countries[]= $country;
        }

        return $countries;
    }
}
