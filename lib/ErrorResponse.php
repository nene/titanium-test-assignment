<?php

/**
 * Transforms an exception into XML, so we can send it as a response
 * to invalid request.
 */
class ErrorResponse
{
    /**
     * Converts exception into XML.
     * @param {Exception} $exception
     * @return {string} XML
     */
    function toXml($exception)
    {
        $error = new SimpleXMLElement("<Error></Error>");
        $error->addChild("Msg", $exception->getMessage());
        return $error->asXML();
    }
}
