<?php

class SearchCarRequestParser
{
    function parse($xmlElement)
    {
        $countries = [];

        foreach ($xmlElement->Country as $countryElement) {
            $country = [
                'name' => $countryElement['name'],
                'cities' => [],
            ];

            foreach ($countryElement->City as $cityName) {
                $country['cities'][]= $cityName;
            }

            $countries[]= $country;
        }

        return $countries;
    }
}
