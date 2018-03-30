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
namespace Tools\Test;

use App\ExampleClass;
use PHPUnit\Framework\TestCase;
use Tools\ReflectionTrait;

/**
 * Reflection\ReflectionTrait Test Case
 */
class ReflectionTraitTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests for `getProperty()` method
     * @test
     */
    public function testGetProperty()
    {
        $example = new ExampleClass;

        $this->assertNull($this->getProperty($example, 'firstProperty'));
        $this->assertEquals('a protected property', $this->getProperty($example, 'secondProperty'));
    }

    /**
     * Tests for `invokeMethod()` method
     * @test
     */
    public function testInvokeMethod()
    {
        $example = new ExampleClass;

        $this->assertEquals('a protected method', $this->invokeMethod($example, 'protectedMethod'));
        $this->assertEquals('example string', $this->invokeMethod($example, 'protectedMethod', ['example string']));
    }

    /**
     * Tests for `getProperty()` method
     * @test
     */
    public function testSetProperty()
    {
        $example = new ExampleClass;

        $this->setProperty($example, 'firstProperty', 'example string');
        $this->assertEquals('example string', $example->firstProperty);

        $this->setProperty($example, 'secondProperty', null);
        $this->assertNull($example->secondProperty);
    }
}
