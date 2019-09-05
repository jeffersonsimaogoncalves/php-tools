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

use JeffersonSimaoGoncalves\Tools\Exception\NotPositiveException;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * NotPositiveExceptionTest class
 */
class NotPositiveExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        try {
            throw new NotPositiveException(null, 0, null, -1);
        } catch (NotPositiveException $e) {
            $this->assertSame('Value `-1` is not a positive', $e->getMessage());
            $this->assertSame(-1, $e->getValue());
        }

        try {
            throw new NotPositiveException();
        } catch (NotPositiveException $e) {
            $this->assertSame('Value is not a positive', $e->getMessage());
            $this->assertNull($e->getValue());
        }

        try {
            throw new NotPositiveException(null, 0, null, ['no-stringable']);
        } catch (NotPositiveException $e) {
            $this->assertSame('Value is not a positive', $e->getMessage());
            $this->assertSame(['no-stringable'], $e->getValue());
        }
    }
}
