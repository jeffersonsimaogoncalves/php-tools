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

use JeffersonSimaoGoncalves\Tools\Exception\NotInArrayException;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * NotInArrayExceptionTest class
 */
class NotInArrayExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        try {
            throw new NotInArrayException(null, 0, null, 'bad-value');
        } catch (NotInArrayException $e) {
            $this->assertSame('Value `bad-value` is not in the array', $e->getMessage());
            $this->assertSame('bad-value', $e->getValue());
        }

        try {
            throw new NotInArrayException();
        } catch (NotInArrayException $e) {
            $this->assertSame('Value is not in the array', $e->getMessage());
            $this->assertNull($e->getValue());
        }

        try {
            throw new NotInArrayException(null, 0, null, ['no-stringable']);
        } catch (NotInArrayException $e) {
            $this->assertSame('Value is not in the array', $e->getMessage());
            $this->assertSame(['no-stringable'], $e->getValue());
        }
    }
}
