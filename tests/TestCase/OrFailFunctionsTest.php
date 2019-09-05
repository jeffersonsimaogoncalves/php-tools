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
use ErrorException;
use Exception;
use RuntimeException;
use stdClass;
use JeffersonSimaoGoncalves\Tools\Exception\FileNotExistsException;
use JeffersonSimaoGoncalves\Tools\Exception\KeyNotExistsException;
use JeffersonSimaoGoncalves\Tools\Exception\NotDirectoryException;
use JeffersonSimaoGoncalves\Tools\Exception\NotInArrayException;
use JeffersonSimaoGoncalves\Tools\Exception\NotPositiveException;
use JeffersonSimaoGoncalves\Tools\Exception\NotReadableException;
use JeffersonSimaoGoncalves\Tools\Exception\NotWritableException;
use JeffersonSimaoGoncalves\Tools\Exception\PropertyNotExistsException;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * OrFailFunctionsTest class
 */
class OrFailFunctionsTest extends TestCase
{
    /**
     * Test for `file_exists_or_fail()` "or fail" function
     * @test
     */
    public function testFileExistsOrFail()
    {
        file_exists_or_fail(create_tmp_file());

        $this->assertException(FileNotExistsException::class, function () {
            file_exists_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` does not exist');
        $this->assertException(FileNotExistsException::class, function () {
            file_exists_or_fail(TMP . 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(ErrorException::class, function () {
            file_exists_or_fail(TMP . 'noExisting', new ErrorException('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `in_array_or_fail()` "or fail" function
     * @test
     */
    public function testInArrayOrFail()
    {
        in_array_or_fail('a', ['a', 'b']);

        $this->assertException(NotInArrayException::class, function () {
            in_array_or_fail('a', []);
        }, 'The value `a` is not in array');
        $this->assertException(NotInArrayException::class, function () {
            in_array_or_fail('a', [], 'an exception');
        }, 'an exception');
        $this->assertException(ErrorException::class, function () {
            in_array_or_fail('a', [], new ErrorException('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_dir_or_fail()` "or fail" function
     * @test
     */
    public function testIsDirOrFail()
    {
        is_dir_or_fail(TMP);

        $filename = create_tmp_file();

        $this->assertException(NotDirectoryException::class, function () use ($filename) {
            is_dir_or_fail($filename);
        }, 'Filename `' . $filename . '` is not a directory');
        $this->assertException(NotDirectoryException::class, function () use ($filename) {
            is_dir_or_fail($filename, 'an exception');
        }, 'an exception');
        $this->assertException(ErrorException::class, function () use ($filename) {
            is_dir_or_fail($filename, new ErrorException('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_positive_or_fail()` "or fail" function
     * @test
     */
    public function testIsPositiveOrFail()
    {
        is_positive_or_fail(1);
        is_positive_or_fail('1');

        $this->assertException(NotPositiveException::class, function () {
            is_positive_or_fail(-1);
        }, 'The value `-1` is not a positive');
        $this->assertException(NotPositiveException::class, function () {
            is_positive_or_fail(-1, 'an exception');
        }, 'an exception');
        $this->assertException(ErrorException::class, function () {
            is_positive_or_fail(-1, new ErrorException('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        is_readable_or_fail(create_tmp_file());

        $this->assertException(NotReadableException::class, function () {
            is_readable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` is not readable');
        $this->assertException(NotReadableException::class, function () {
            is_readable_or_fail(TMP . 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(ErrorException::class, function () {
            is_readable_or_fail(TMP . 'noExisting', new ErrorException('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_true_or_fail()` function
     * @test
     */
    public function testIsTrueOrFail()
    {
        foreach (['string', ['array'], new stdClass(), true, 1, 0.1] as $value) {
            is_true_or_fail($value);
        }

        foreach ([null, false, 0, '0', []] as $value) {
            $this->assertException(ErrorException::class, function () use ($value) {
                is_true_or_fail($value);
            }, 'The value is not equal to `true`');
        }

        //Failure with a `null` message
        try {
            is_true_or_fail(false, null);
        } catch (Exception $e) {
        } finally {
            $this->assertEmpty($e->getMessage());
        }

        //Failure with a custom message
        $this->assertException(ErrorException::class, function () {
            is_true_or_fail(false, '`false` is not `true`');
        }, '`false` is not `true`');

        //Failure with custom message and exception class string
        foreach ([RuntimeException::class, 'RuntimeException'] as $exceptionClass) {
            $this->assertException(RuntimeException::class, function () use ($exceptionClass) {
                is_true_or_fail(false, '`false` is not `true`', $exceptionClass);
            }, '`false` is not `true`');
        }

        //Failure with a custom exception class string as second argument
        $this->assertException(RuntimeException::class, function () {
            is_true_or_fail(false, RuntimeException::class);
        });

        //Failure with custom message and an instantiated exception
        $this->assertException(ErrorException::class, function () {
            is_true_or_fail(false, null, new ErrorException('an exception'));
        }, 'an exception');

        //Failures with bad exception classes
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, new stdClass());
        }, '`$exception` parameter must be a string');
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, stdClass::class);
        }, '`stdClass` is not and instance of `Exception`');
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, 'noExisting\Class');
        }, 'Class `noExisting\Class` does not exist');
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function
     * @test
     */
    public function testIsWritableOrFail()
    {
        is_writable_or_fail(create_tmp_file());

        $this->assertException(NotWritableException::class, function () {
            is_writable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` is not writable');
        $this->assertException(NotWritableException::class, function () {
            is_writable_or_fail(TMP . 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(ErrorException::class, function () {
            is_writable_or_fail(TMP . 'noExisting', new ErrorException('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `key_exists_or_fail()` "or fail" function
     * @test
     */
    public function testKeyExistsOrFail()
    {
        $array = ['a' => 'alfa', 'beta', 'gamma'];
        key_exists_or_fail('a', $array);
        key_exists_or_fail(['a', 1], $array);

        foreach ([
            'b',
            ['a', 'b'],
            ['b', 'c'],
        ] as $key) {
            $this->assertException(KeyNotExistsException::class, function () use ($array, $key) {
                key_exists_or_fail($key, $array);
            }, 'Key `b` does not exist');
            $this->assertException(KeyNotExistsException::class, function () use ($array, $key) {
                key_exists_or_fail($key, $array, 'an exception');
            }, 'an exception');
            $this->assertException(ErrorException::class, function () use ($array, $key) {
                key_exists_or_fail($key, $array, new ErrorException('an exception'));
            }, 'an exception');
        }
    }

    /**
     * Test for `property_exists_or_fail()` "or fail" function
     * @test
     */
    public function testPropertyExistsOrFail()
    {
        property_exists_or_fail(new ExampleClass(), 'publicProperty');

        $object = new stdClass();
        $object->name = 'My name';
        property_exists_or_fail($object, 'name');

        $object = $this->getMockBuilder(ExampleClass::class)
            ->setMethods(['has'])
            ->getMock();

        $object->expects($this->once())
            ->method('has')
            ->with('publicProperty')
            ->willReturn(true);

        property_exists_or_fail($object, 'publicProperty');

        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new stdClass(), 'noExisting');
        }, 'Object does not have `noExisting` property');

        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting');
        }, 'Object does not have `noExisting` property');
        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(ErrorException::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting', new ErrorException('an exception'));
        }, 'an exception');
    }
}
