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
 * @since       1.0.2
 */
namespace JeffersonSimaoGoncalves\Tools\TestSuite;

use BadMethodCallException;
use Exception;
use PHPUnit\Framework\Constraint\IsType;

/**
 * A trait that provides some assertion methods
 * @method void assertIsArray($var, $message = '') Asserts that `$var` is an array
 * @method void assertIsBool($var, $message = '') Asserts that `$var` is a boolean
 * @method void assertIsCallable($var, $message = '') Asserts that `$var` is a callable
 * @method void assertIsFloat($var, $message = '') Asserts that `$var` is a float
 * @method void assertIsHtml($var, $message = '') Asserts that `$var` is an html string
 * @method void assertIsInt($var, $message = '') Asserts that `$var` is an int
 * @method void assertIsIterable($var, $message = '') Asserts that `$var` is iterable, i.e. that it is an array or an object implementing `Traversable`
 * @method void assertIsJson($var, $message = '') Asserts that `$var` is a json string
 * @method void assertIsObject($var, $message = '') Asserts that `$var` is an object
 * @method void assertIsPositive($var, $message = '') Asserts that `$var` is a positive number
 * @method void assertIsResource($var, $message = '') Asserts that `$var` is a resource
 * @method void assertIsString($var, $message = '') Asserts that `$var` is a string
 * @method void assertIsUrl($var, $message = '') Asserts that `$var` is an url
 */
trait TestTrait
{
    /**
     * Magic `__call()` method.
     *
     * Provides some "assertIs" methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @see __callStatic()
     * @since 1.1.12
     */
    public function __call($name, array $arguments)
    {
        self::__callStatic($name, $arguments);
    }

    /**
     * Magic `__callStatic()` method.
     *
     * Provides some "assertIs" methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @since 1.1.12
     * @throws \JeffersonSimaoGoncalves\Tools\Exception\BadMethodCallException
     */
    public static function __callStatic($name, array $arguments)
    {
        if (string_starts_with($name, 'assertIs')) {
            $count = count($arguments);
            if (!$count || $count > 2) {
                throw new BadMethodCallException(sprintf(
                    'Method %s::%s() expects at least 1 argument, maximum 2, %d passed',
                    __CLASS__,
                    $name,
                    $count
                ));
            }

            $type = substr($name, 8);

            if (defined(sprintf('%s::TYPE_%s', IsType::class, strtoupper($type)))) {
                $arguments = array_merge([lcfirst($type)], $arguments);
                call_user_func_array([__CLASS__, 'assertInternalType'], $arguments);

                return;
            }

            $function = sprintf('is_%s', strtolower($type));
            if (function_exists($function)) {
                $var = array_shift($arguments);
                $arguments = array_merge([$function($var)], $arguments);
                call_user_func_array([__CLASS__, 'assertTrue'], $arguments);

                return;
            }
        }

        throw new BadMethodCallException(sprintf('Method %s::%s() does not exist', __CLASS__, $name));
    }

    /**
     * Asserts that the array keys are equal to `$expectedKeys`
     * @param array $expectedKeys Expected keys
     * @param array $array Array to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertArrayKeysEqual(array $expectedKeys, array $array, $message = '')
    {
        $keys = array_keys($array);
        sort($keys);
        sort($expectedKeys);
        self::assertEquals($expectedKeys, $keys, $message);
    }

    /**
     * Asserts that a callable throws an exception
     * @param string $expectedException Expected exception
     * @param callable $function A callable you want to test and that should
     *  raise the expected exception
     * @param string|null $expectedMessage The expected message or `null`
     * @return void
     * @since 1.1.7
     */
    protected static function assertException($expectedException, callable $function, $expectedMessage = null)
    {
        if ($expectedException !== Exception::class && !is_subclass_of($expectedException, Exception::class)) {
            self::fail(sprintf('Class `%s` does not exist or is not an exception', $expectedException));
        }

        $e = false;
        try {
            call_user_func($function);
        } catch (Exception $e) {
            parent::assertInstanceof(
                $expectedException,
                $e,
                sprintf('Expected exception `%s`, unexpected type `%s`', $expectedException, get_class($e))
            );

            if ($expectedMessage) {
                parent::assertNotEmpty(
                    $e->getMessage(),
                    sprintf('Expected message exception `%s`, but no message for the exception', $expectedMessage)
                );
                parent::assertEquals($expectedMessage, $e->getMessage(), sprintf(
                    'Expected message exception `%s`, unexpected message `%s`',
                    $expectedMessage,
                    $e->getMessage()
                ));
            }
        } finally {
            parent::assertNotFalse($e, sprintf('Expected exception `%s`, but no exception throw', $expectedException));
        }
    }

    /**
     * Asserts that a filename has the `$expectedExtension`.
     *
     * If `$expectedExtension` is an array, asserts that the filename has at
     *  least one of those values.
     *
     * It is not necessary it actually exists.
     * The assertion is case-insensitive (eg, for `PIC.JPG`, the expected
     *  extension is `jpg`).
     * @param string|array $expectedExtension Expected extension
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertFileExtension($expectedExtension, $filename, $message = '')
    {
        self::assertContains(get_extension($filename), (array)$expectedExtension, $message);
    }

    /**
     * Asserts that a filename have a MIME content type.
     *
     * If `$expectedMime` is an array, asserts that the filename has at
     *  least one of those values.
     * @param string|array $expectedMime MIME content type
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertFileMime($expectedMime, $filename, $message = '')
    {
        self::assertFileExists($filename);
        self::assertContains(mime_content_type($filename), (array)$expectedMime, $message);
    }

    /**
     * Asserts that a filename has some file permissions.
     *
     * If `$expectedPerms` is an array, asserts that the filename has at
     *  least one of those values
     * @param string|int|array $expectedPerms Expected permission values as a
     *  four-chars string or octal value
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.9
     */
    protected static function assertFilePerms($expectedPerms, $filename, $message = '')
    {
        parent::assertFileExists($filename);

        $expectedPerms = array_map('fileperms_to_string', (array)$expectedPerms);
        self::assertContains(fileperms_as_octal($filename), $expectedPerms, $message);
    }

    /**
     * Asserts that an image file has `$expectedWidth` and `$expectedHeight`
     * @param int|string $expectedWidth Expected image width
     * @param int|string $expectedHeight Expected mage height
     * @param string $filename Path to the tested file
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertImageSize($expectedWidth, $expectedHeight, $filename, $message = '')
    {
        self::assertFileExists($filename);
        list($actualWidth, $actualHeight) = getimagesize($filename);
        self::assertEquals($actualWidth, $expectedWidth, $message);
        self::assertEquals($actualHeight, $expectedHeight, $message);
    }

    /**
     * Asserts that `$var` is an array and is not empty
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.6
     */
    protected static function assertIsArrayNotEmpty($var, $message = '')
    {
        self::assertIsArray($var, $message);
        self::assertNotEmpty(array_filter($var), $message);
    }

    /**
     * Asserts that the object properties are equal to `$expectedProperties`
     * @param array $expectedProperties Expected properties
     * @param object $object Object you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected function assertObjectPropertiesEqual(array $expectedProperties, $object, $message = '')
    {
        self::assertIsObject($object);
        self::assertArrayKeysEqual($expectedProperties, (array)$object, $message);
    }

    /**
     * Asserts that `$firstClass` and `$secondClass` have the same methods
     * @param mixed $firstClass First class as string or object
     * @param mixed $secondClass Second class as string or object
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertSameMethods($firstClass, $secondClass, $message = '')
    {
        list($firstClassMethods, $secondClassMethods) = [get_class_methods($firstClass), get_class_methods($secondClass)];
        sort($firstClassMethods);
        sort($secondClassMethods);
        self::assertEquals($firstClassMethods, $secondClassMethods, $message);
    }

    /**
     * Skips the test if the condition is `true`
     * @param bool $shouldSkip Whether or not the test should be skipped
     * @param string $message The message to display
     * @return bool
     */
    public function skipIf($shouldSkip, $message = '')
    {
        if ($shouldSkip) {
            $this->markTestSkipped($message);
        }

        return $shouldSkip;
    }
}
