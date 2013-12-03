<?php
set_include_path(dirname(dirname(__FILE__)) . '/lib');
require_once 'XmlValidator.php';
require_once 'XmlValidatorException.php';


class XmlValidatorTest extends PHPUnit_Framework_TestCase
{
    function validate($xml, $schema)
    {
        $xmlElement = new SimpleXMLElement($xml);

        return (new XmlValidator())->validate($xmlElement, $schema);
    }

    function testValidRoot()
    {
        $ok = $this->validate("<Foo></Foo>", [
            "name" => "Foo"
        ]);
        $this->assertTrue($ok);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage <Foo> expected but <Bar> found
     */
    function testInvalidRoot()
    {
        $this->validate("<Bar></Bar>", [
            "name" => "Foo"
        ]);
    }

    function testValidChild()
    {
        $ok = $this->validate("<Foo><Bar></Bar></Foo>", [
            "name" => "Foo",
            "children" => [
                ["name" => "Bar"]
            ]
        ]);
        $this->assertTrue($ok);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage required element <Bar> not found in <Foo>
     */
    function testMissingChild()
    {
        $this->validate("<Foo></Foo>", [
            "name" => "Foo",
            "children" => [
                ["name" => "Bar"]
            ]
        ]);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage unexpected <Zap> found in <Foo>
     */
    function testInvalidChild()
    {
        $this->validate("<Foo><Zap></Zap></Foo>", [
            "name" => "Foo",
            "children" => [
                ["name" => "Bar"]
            ]
        ]);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage unexpected <Zap> found in <Foo>
     */
    function testInvalidChildInAdditionToValidOne()
    {
        $this->validate("<Foo><Bar></Bar><Zap></Zap></Foo>", [
            "name" => "Foo",
            "children" => [
                ["name" => "Bar"]
            ]
        ]);
    }

    function testMultipleChildren()
    {
        $ok = $this->validate("<Foo><Bar></Bar><Zap></Zap></Foo>", [
            "name" => "Foo",
            "children" => [
                ["name" => "Bar"],
                ["name" => "Zap"]
            ]
        ]);
        $this->assertTrue($ok);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage required element <Zap> not found in <Foo>
     */
    function testMultipleChildrenMissing()
    {
        $this->validate("<Foo><Bar></Bar></Foo>", [
            "name" => "Foo",
            "children" => [
                ["name" => "Bar"],
                ["name" => "Zap"]
            ]
        ]);
    }

    function testEmptyElementOk()
    {
        $ok = $this->validate("<Foo></Foo>", [
            "name" => "Foo",
        ]);
        $this->assertTrue($ok);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage <Foo> must not be empty
     */
    function testEmptyElementNotOk()
    {
        $this->validate("<Foo></Foo>", [
            "name" => "Foo",
            "content" => true,
        ]);
    }

    function testValidAttribute()
    {
        $ok = $this->validate("<Foo bar='hello'></Foo>", [
            "name" => "Foo",
            "attributes" => [
                ["name" => "bar", "content" => true]
            ]
        ]);
        $this->assertTrue($ok);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage required attribute 'bar' not found in <Foo>
     */
    function testMissingAttribute()
    {
        $this->validate("<Foo></Foo>", [
            "name" => "Foo",
            "attributes" => [
                ["name" => "bar", "content" => true]
            ]
        ]);
    }

    function testMultipleAttributes()
    {
        $ok = $this->validate("<Foo bar='hello' zap='xyz'></Foo>", [
            "name" => "Foo",
            "attributes" => [
                ["name" => "bar"],
                ["name" => "zap"]
            ]
        ]);
        $this->assertTrue($ok);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage required attribute 'zap' not found in <Foo>
     */
    function testMultipleAttributesMissing()
    {
        $this->validate("<Foo bar='hello'></Foo>", [
            "name" => "Foo",
            "attributes" => [
                ["name" => "bar"],
                ["name" => "zap"]
            ]
        ]);
    }

    function testEmptyAttributeOk()
    {
        $ok = $this->validate("<Foo bar=''></Foo>", [
            "name" => "Foo",
            "attributes" => [
                ["name" => "bar"]
            ]
        ]);
        $this->assertTrue($ok);
    }

    /**
     * @expectedException XmlValidatorException
     * @expectedExceptionMessage attribute 'bar' must not be empty in <Foo>
     */
    function testEmptyAttributeNotOk()
    {
        $this->validate("<Foo bar=''></Foo>", [
            "name" => "Foo",
            "attributes" => [
                ["name" => "bar", "content" => true]
            ]
        ]);
    }
}
