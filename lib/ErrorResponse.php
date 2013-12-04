<?php

/**
 * Transforms an exception into XML, so we can send it as a response
 * to invalid request.
 */
class ErrorResponse
{
    private $exception;

    /**
     * Initializes with an exception.
     * @param {Exception} $exception
     */
    function __construct($exception)
    {
        $this->exception = $exception;
    }

    /**
     * Returns the exception data as XML string.
     * @return {string} XML
     */
    function toXml()
    {
        $error = new SimpleXMLElement("<Error></Error>");
        $error->addChild("Msg", $this->exception->getMessage());
        return $error->asXML();
    }
}
