<?php
/**
 * This file is part of cakephp-thumber.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-thumber
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace JeffersonSimaoGoncalves\Tools\Test\TestSuite;

use App\AnotherExampleChildClass;
use App\ExampleChildClass;
use App\ExampleClass;
use App\SkipTestCase;
use BadMethodCallException;
use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Error\Deprecated;
use stdClass;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * TestTraitTest class
 */
class TestTraitTest extends TestCase
{
    /**
     * Test for `__call()` and `__callStatic()` magic methods
     * @ŧest
     */
    public function testMagicCallAndCallStatic()
    {
        //Methods that use the `assertInternalType()` method
        foreach ([
            'assertIsArray' => ['array'],
            'assertIsBool' => true,
            'assertIsFloat' => 1.1,
            'assertIsInt' => 1,
            'assertIsJson' => '{"a":1,"b":2,"c":3,"d":4,"e":5}',
            'assertIsObject' => new stdClass(),
            'assertIsString' => 'string',
        ] as $assertMethod => $value) {
            $this->{$assertMethod}($value);
            self::{$assertMethod}($value);
        }

        //Missing argument
        $this->assertException(BadMethodCallException::class, function () {
            $this->assertIsJson();
        }, 'Method ' . get_parent_class($this) . '::assertIsJson() expects at least 1 argument, maximum 2, 0 passed');

        //Calling a no existing method or a no existing "assertIs" method
        foreach (['assertIsNoExistingType', 'noExistingMethod'] as $method) {
            $this->assertException(BadMethodCallException::class, function () use ($method) {
                $this->$method('string');
            }, 'Method ' . get_parent_class($this) . '::' . $method . '() does not exist');
        }
    }

    /**
     * Tests for `assertArrayKeysEqual()` method
     * @test
     */
    public function testAssertArrayKeysEqual()
    {
        $this->assertArrayKeysEqual([], []);

        foreach ([
            ['key1' => 'value1', 'key2' => 'value2'],
            ['key2' => 'value2', 'key1' => 'value1'],
        ] as $array) {
            $this->assertArrayKeysEqual(['key1', 'key2'], $array);
        }

        $this->expectException(AssertionFailedError::class);
        $this->assertArrayKeysEqual(['key2'], $array);
    }

    /**
     * Tests for `assertException()` method
     * @test
     */
    public function testAssertException()
    {
        $this->assertException(Exception::class, function () {
            throw new Exception();
        });
        $this->assertException(Exception::class, function () {
            throw new Exception('right exception message');
        });
        $this->assertException(Exception::class, function () {
            throw new Exception('right exception message');
        }, 'right exception message');

        //No exception throw
        try {
            $this->assertException(Exception::class, 'time');
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith('Expected exception `Exception`, but no exception throw', $e->getMessage());
            unset($e);
        }

        //No existing exception or invalid exception class
        foreach (['noExistingException', stdClass::class] as $class) {
            try {
                $this->assertException($class, function () {
                    throw new Exception();
                });
            } catch (AssertionFailedError $e) {
            } finally {
                $this->assertStringStartsWith('Class `' . $class . '` does not exist or is not an exception', $e->getMessage());
                unset($e);
            }
        }

        //Unexpected exception type
        try {
            $this->assertException(Deprecated::class, function () {
                throw new Exception();
            });
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith('Expected exception `' . Deprecated::class . '`, unexpected type `Exception`', $e->getMessage());
            unset($e);
        }

        //Wrong exception message
        try {
            $this->assertException(Exception::class, function () {
                throw new Exception('Wrong');
            }, 'Right');
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith('Expected message exception `Right`, unexpected message `Wrong`', $e->getMessage());
            unset($e);
        }

        //Expected exception message, but no message
        try {
            $this->assertException(Exception::class, function () {
                throw new Exception();
            }, 'Right');
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith('Expected message exception `Right`, but no message for the exception', $e->getMessage());
        }
    }

    /**
     * Test for `assertFileExtension()` method
     * @ŧest
     */
    public function testAssertFileExtension()
    {
        $this->assertFileExtension('jpg', 'file.jpg');
        $this->assertFileExtension('jpeg', 'FILE.JPEG');
        $this->assertFileExtension(['jpg', 'jpeg'], 'file.jpg');
    }

    /**
     * Test for `assertFileMime()` method
     * @ŧest
     */
    public function testAssertFileMime()
    {
        $file = create_tmp_file('string');
        $this->assertFileMime('text/plain', $file);
        $this->assertFileMime(['text/plain', 'inode/x-empty'], $file);
    }

    /**
     * Tests for `assertFilePerms()` method
     * @requires OS Linux
     * @test
     */
    public function testAssertFilePerms()
    {
        $file = create_tmp_file();
        $this->assertFilePerms('0600', $file);
        $this->assertFilePerms(0600, $file);
        $this->assertFilePerms(['0600', '0666'], $file);
        $this->assertFilePerms([0600, 0666], $file);
        $this->assertFilePerms(['0600', 0666], $file);
    }

    /**
     * Test for `assertImageSize()` method
     * @ŧest
     */
    public function testAssertImageSize()
    {
        $filename = TMP . 'pic.jpg';
        imagejpeg(imagecreatetruecolor(120, 20), $filename);
        $this->assertImageSize(120, 20, $filename);
        $this->assertImageSize('120', '20', $filename);
    }

    /**
     * Tests for `assertIsArrayNotEmpty()` method
     * @test
     */
    public function testAssertIsArrayNotEmpty()
    {
        $this->assertIsArrayNotEmpty(['value']);

        foreach ([
            [],
            [[]],
            [false],
            [null],
            [''],
        ] as $array) {
            $this->assertException(AssertionFailedError::class, function () use ($array) {
                $this->assertIsArrayNotEmpty($array);
            });
        }
    }

    /**
     * Tests for `assertObjectPropertiesEqual()` method
     * @test
     */
    public function testAssertObjectPropertiesEqual()
    {
        $object = new stdClass();
        $object->first = 'first value';
        $object->second = 'second value';
        $this->assertObjectPropertiesEqual(['first', 'second'], $object);
        $this->assertObjectPropertiesEqual(['second', 'first'], $object);

        $this->expectException(AssertionFailedError::class);
        $this->assertObjectPropertiesEqual(['first'], $object);
    }

    /**
     * Test for `assertSameMethods()` method
     * @ŧest
     */
    public function testAssertSameMethods()
    {
        $exampleClass = new ExampleClass();
        $this->assertSameMethods($exampleClass, ExampleClass::class);
        $this->assertSameMethods($exampleClass, get_class($exampleClass));

        $copyExampleClass = &$exampleClass;
        $this->assertSameMethods($exampleClass, $copyExampleClass);

        $this->assertSameMethods(ExampleChildClass::class, AnotherExampleChildClass::class);

        $this->expectException(AssertionFailedError::class);
        $this->assertSameMethods(ExampleClass::class, AnotherExampleChildClass::class);
    }

    /**
     * Test for `skipIf()` method
     * @ŧest
     */
    public function testSkipIf()
    {
        $result = (new SkipTestCase('testSkipIfTrue'))->run();
        $this->assertSame(1, $result->skippedCount());

        $result = (new SkipTestCase('testSkipIfFalse'))->run();
        $this->assertSame(0, $result->skippedCount());
    }
}
