<?php

/**
 * Given country and city name, retrieves 5 cheapest cars in this
 * city.  The return value is an array with the following structure:
 *
 *    [
 *         ["car_type" => "BMW X3", "price" => "34.33"],
 *         ["car_type" => "Opel Zafira", "price" => "34.34"],
 *         ["car_type" => "Volvo S40", "price" => "35.00"],
 *         ...
 *    ]
 *
 */
class SearchCar_PriceQuery
{
    const LIMIT = 5;

    private $db;

    /**
     * Initialized with a database connection object.
     * @param {PDO} $db
     */
    function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Performs the query.
     * @param {string} $country Name of the country
     * @param {string} $city Name of the city within that country
     * @return {array} of car type and price pairs.
     */
    function query($country, $city)
    {
        $sql = "
            SELECT
              car_types.name as car_type,
              car_prices.price
            FROM
              car_prices
              LEFT JOIN car_types ON (car_prices.car_type_id = car_types.id)
              LEFT JOIN cities ON (car_prices.city_id = cities.id)
              LEFT JOIN countries ON (cities.country_id = countries.id)
            WHERE
              countries.name = :country AND
              cities.name = :city
            ORDER BY
              price
            LIMIT :limit
        ";

        $statement = $this->db->prepare($sql);
        $statement->bindParam(":country", $country, PDO::PARAM_STR, 255);
        $statement->bindParam(":city", $city, PDO::PARAM_STR, 255);
        $statement->bindValue(":limit", self::LIMIT, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
