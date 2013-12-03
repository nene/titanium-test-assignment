<?php

class SearchCarRequestValidator
{
    private $validator;
    private $schema;

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

    function validate($xmlElement)
    {
        return $this->validator->validate($xmlElement, $this->schema);
    }
}
