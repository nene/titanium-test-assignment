<?php

/**
 * Validates the XML schema of SearchCarRQ XML.
 */
class SearchCar_RequestValidator
{
    private $validator;
    private $schema;

    /**
     * Initialized with XmlValidator instance.
     * @param {XmlValidator} $validator
     */
    function __construct($validator)
    {
        $this->schema = [
            "name" => "SearchCarRQ",
            "children" => [
                [
                    "name" => "Country",
                    "attributes" => [
                        ["name" => "name", "content" => true]
                    ],
                    "children" => [
                        [
                            "name" => "City",
                            "content" => true,
                        ]
                    ]
                ]
            ]
        ];

        $this->validator = $validator;
    }

    /**
     * Validates the XML.
     * @param {SimplXMLElement} $xmlElement
     */
    function validate($xmlElement)
    {
        return $this->validator->validate($xmlElement, $this->schema);
    }
}
