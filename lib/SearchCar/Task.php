<?php
require_once 'SearchCar/RequestValidator.php';
require_once 'SearchCar/RequestParser.php';
require_once 'SearchCar/Query.php';
require_once 'SearchCar/PriceQuery.php';
require_once 'SearchCar/Response.php';

/**
 * Performs the task of handling SearchCarRQ.
 *
 * A facade for all the SearchCar/* classes.
 */
class SearchCar_Task
{
    /**
     * Initialized with XmlValidator and database connection.
     *
     * @param {XmlValidator} $xmlValidator
     * @param {PDO} $db
     */
    function __construct($xmlValidator, $db)
    {
        $this->validator = new SearchCar_RequestValidator($xmlValidator);
        $this->parser = new SearchCar_RequestParser();
        $this->searchCarQuery = new SearchCar_Query(new SearchCar_PriceQuery($db));
    }

    /**
     * Given SimpleXML-parsed XML, performs the query and returns
     * result as XML string.
     *
     * Various exceptions can be thrown in here by the components
     * used.  Should be catched by caller.
     *
     * @param {SimpleXMLElement} $xmlElement
     * @return {string} XML response
     */
    function run($xmlElement)
    {
        $this->validator->validate($xmlElement);
        $query = $this->parser->parse($xmlElement);
        $response = $this->searchCarQuery->query($query);
        return (new SearchCar_Response($response))->toXml();
    }
}
