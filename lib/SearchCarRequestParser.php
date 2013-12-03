<?php

class SearchCarRequestParser
{
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
