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
namespace JeffersonSimaoGoncalves\Tools\Test\Exception;

use JeffersonSimaoGoncalves\Tools\Exception\PropertyNotExistsException;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * PropertyNotExistsExceptionTest class
 */
class PropertyNotExistsExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        try {
            throw new PropertyNotExistsException(null, 0, null, 'a-key');
        } catch (PropertyNotExistsException $e) {
            $this->assertSame('Property `a-key` does not exist', $e->getMessage());
            $this->assertSame('a-key', $e->getPropertyName());
        }

        try {
            throw new PropertyNotExistsException();
        } catch (PropertyNotExistsException $e) {
            $this->assertSame('Property does not exist', $e->getMessage());
            $this->assertNull($e->getPropertyName());
        }
    }
}
