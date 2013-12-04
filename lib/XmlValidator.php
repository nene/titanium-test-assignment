<?php
require_once 'XmlValidatorException.php';

/**
 * Validates a SimpleXMLElement tree.
 *
 * Accepts a schema definition like the following:
 *
 *      [
 *         "name" => "Root",
 *         "children" => [
 *             [
 *                 "name" => "Child",
 *                 "attributes" => [
 *                     ["name" => "myattr", "content" => true]
 *                 ],
 *                 "content" => true,
 *             ]
 *         ]
 *     ];
 *
 * Which will accept the following XML as valid:
 *
 *     <Root>
 *         <Child myattr="some attr content is required">
 *             some element content is required
 *         </Child>
 *         <!-- more Child elements -->
 *     </Root>
 *
 * Note: This should likely be replaced with a proper third-party
 * validator that uses some standard XML Schema definition format.
 * This simple implementation covers some basic cases, but it will
 * fall short when more complex definition is needed. e.g. one can't
 * define a recursive data structure.
 */
class XmlValidator
{
    /**
     * Validates an element against given schema definition.
     *
     * @param {SimpleXMLElement} $el
     * @param {array} $schema
     * @return {boolean} true when all OK.
     * @throws XmlValidatorException when XML is not valid.
     */
    function validate($el, $schema)
    {
        $this->validateNode($el, $schema);
        return true;
    }

    private function validateNode($el, $schema)
    {
        if (!$this->checkName($el, $schema)) {
            $this->err("<{$schema['name']}> expected but <{$el->getName()}> found");
        }

        if (!$this->checkContent($el, $schema)) {
            $this->err("<{$el->getName()}> must not be empty");
        }

        if (isset($schema['attributes'])) {
            $this->validateAttributes($el, $el->attributes(), $schema['attributes']);
        }

        if (isset($schema['children'])) {
            $this->validateChildren($el, $el->children(), $schema['children']);
        }
    }

    private function validateAttributes($el, $attributes, $schema)
    {
        $attributesFound = [];

        foreach ($attributes as $attr) {
            $attrSchema = $this->getSubSchema($schema, $attr->getName());

            if (!$attrSchema) {
                $this->err("unexpected attribute '{$attr->getName()}' found in <{$el->getName()}>");
            }

            $this->validateAttr($el->getName(), $attr, $attrSchema);

            $attributesFound[$attr->getName()] = true;
        }

        $this->validateRequired($attributesFound, $schema, function($attr) use ($el) {
            $this->err("required attribute '$attr' not found in <{$el->getName()}>");
        });
    }

    private function validateAttr($elName, $attr, $schema)
    {
        if (!$this->checkContent($attr, $schema)) {
            $this->err("attribute '{$attr->getName()}' must not be empty in <$elName>");
        }
    }

    private function validateChildren($el, $children, $schema)
    {
        $childrenFound = [];

        foreach ($children as $child) {
            $childSchema = $this->getSubSchema($schema, $child->getName());

            if (!$childSchema) {
                $this->err("unexpected <{$child->getName()}> found in <{$el->getName()}>");
            }

            $this->validateNode($child, $childSchema);

            $childrenFound[$child->getName()] = true;
        }

        $this->validateRequired($childrenFound, $schema, function($child) use ($el) {
            $this->err("required element <$child> not found in <{$el->getName()}>");
        });
    }

    // checks if all the elements required by schema are present.
    // When not, calls the $callback with name of the first missing element.
    private function validateRequired($foundElements, $schema, callable $callback)
    {
        foreach ($schema as $req) {
            if (!isset($foundElements[$req['name']])) {
                $callback($req['name']);
                return;
            }
        }
    }

    // retrieves sub-schema for given element/attribute name
    private function getSubSchema($schema, $name)
    {
        foreach ($schema as $child) {
            if ($child['name'] === $name) {
                return $child;
            }
        }
        return false;
    }

    // false when element/attribute name doesn't match with schema
    private function checkName($el, $schema)
    {
        return $el->getName() === $schema["name"];
    }

    // false when content is required but element or attribute is empty
    private function checkContent($el, $schema)
    {
        return !(isset($schema["content"]) && $schema["content"] === true && (string)$el === "");
    }

    private function err($msg)
    {
        throw new XmlValidatorException("Ivalid XML: " . $msg);
    }
}
