<?php
/**
 * This file is part of php-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace JeffersonSimaoGoncalves\Tools\Test;

use App\ExampleClass;
use ReflectionProperty;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * Reflection\ReflectionTrait Test Case
 */
class ReflectionTraitTest extends TestCase
{
    /**
     * Tests for `getProperties()` method
     * @test
     */
    public function testGetProperties()
    {
        $expected = [
            'privateProperty' => 'this is a private property',
            'firstProperty' => null,
            'secondProperty' => 'a protected property',
            'publicProperty' => 'this is public',
            'staticProperty' => 'a static property',
        ];

        $example = new ExampleClass();
        $this->assertEquals($expected, $this->getProperties($example));
        $this->assertEquals($expected, $this->getProperties(ExampleClass::class));
        $this->assertEquals($expected, $this->getProperties(new ExampleClass()));

        $this->assertArrayKeysEqual(['publicProperty', 'staticProperty'], $this->getProperties($example, ReflectionProperty::IS_PUBLIC));
        $this->assertArrayKeysEqual(['firstProperty', 'secondProperty'], $this->getProperties($example, ReflectionProperty::IS_PROTECTED));
        $this->assertArrayKeysEqual(['privateProperty'], $this->getProperties($example, ReflectionProperty::IS_PRIVATE));
        $this->assertArrayKeysEqual(['staticProperty'], $this->getProperties($example, ReflectionProperty::IS_STATIC));

        unset($expected['privateProperty']);
        $example = $this->getMockBuilder(ExampleClass::class)->getMock();
        $this->assertEquals($expected, $this->getProperties($example));
    }

    /**
     * Tests for `getProperty()` method
     * @test
     */
    public function testGetProperty()
    {
        $example = new ExampleClass();
        $this->assertNull($this->getProperty($example, 'firstProperty'));
        $this->assertEquals('a protected property', $this->getProperty($example, 'secondProperty'));
        $this->assertEquals('a protected property', $this->getProperty(ExampleClass::class, 'secondProperty'));
        $this->assertEquals('a protected property', $this->getProperty(new ExampleClass(), 'secondProperty'));
    }

    /**
     * Tests for `invokeMethod()` method
     * @test
     */
    public function testInvokeMethod()
    {
        $example = new ExampleClass();
        $this->assertEquals('a protected method', $this->invokeMethod($example, 'protectedMethod'));
        $this->assertEquals('example string', $this->invokeMethod($example, 'protectedMethod', ['example string']));
        $this->assertEquals('a protected method', $this->invokeMethod(ExampleClass::class, 'protectedMethod'));
        $this->assertEquals('a protected method', $this->invokeMethod(new ExampleClass(), 'protectedMethod'));
    }

    /**
     * Tests for `setProperty()` method
     * @test
     */
    public function testSetProperty()
    {
        $example = new ExampleClass();
        $result = $this->setProperty($example, 'firstProperty', 'example string');
        $this->assertNull($result);
        $this->assertEquals('example string', $example->firstProperty);

        $expectedResult = $example->secondProperty;
        $result = $this->setProperty($example, 'secondProperty', null);
        $this->assertEquals($expectedResult, $result);
        $this->assertNull($example->secondProperty);
    }
}
