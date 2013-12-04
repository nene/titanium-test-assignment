<?php

/**
 * Given nested array of countries and cities:
 *
 *     [
 *         ["name" => "Austria", "cities" => ["Linz", "Salzburg"]],
 *         ["name" => "Portugal", "cities" => ["faro"]],
 *     ]
 *
 * Retrieves a list of top 5 cheapest cars in each city, and returns
 * them in the following format:
 *
 *     [
 *         ["name" => "Austria", "cities" => [
 *              ["name" => "Linz", "cars" => [
 *                  ["car_type" => "Opel Zafira", "price" => "25.12"],
 *                  ["car_type" => "BMW X2", "price" => "28.10"],
 *                  ...
 *              ],
 *              ["name" => "Salzburg", "cars" => [
 *                  ["car_type" => "Opel Zafira", "price" => "25.12"],
 *                  ["car_type" => "BMW X2", "price" => "28.10"],
 *                  ...
 *              ]
 *         ],
 *         ["name" => "Portugal", "cities" => [
 *              ["name" => "Faro", "cars" => [
 *                  ["car_type" => "Opel Zafira", "price" => "25.12"],
 *                  ["car_type" => "BMW X2", "price" => "28.10"],
 *                  ...
 *              ]
 *         ],
 *     ]
 */
class SearchCar_Query
{
    private $priceQuery;

    /**
     * Initialized with PriceQuery object.
     * @param {SearchCar_PriceQuery} $priceQuery
     */
    function __construct($priceQuery)
    {
        $this->priceQuery = $priceQuery;
    }

    function query($countries)
    {
        $result = [];
        foreach ($countries as $country) {
            $cities = [];
            foreach ($country['cities'] as $cityName) {
                $cars = $this->priceQuery->query($country['name'], $cityName);
                $cities[]= [
                    "name" => $cityName,
                    "cars" => $cars
                ];
            }
            $result[]= [
                "name" => $country["name"],
                "cities" => $cities
            ];
        }
        return $result;
    }

}
