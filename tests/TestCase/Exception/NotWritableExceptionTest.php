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

use JeffersonSimaoGoncalves\Tools\Exception\NotWritableException;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * NotWritableExceptionTest class
 */
class NotWritableExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        $file = ROOT . 'dir' . DS . 'notWritableFile';
        try {
            throw new NotWritableException(null, 0, null, $file);
        } catch (NotWritableException $e) {
            $this->assertSame('File or directory `dir/notWritableFile` is not writable', $e->getMessage());
            $this->assertSame($file, $e->getFilePath());
        }

        try {
            throw new NotWritableException();
        } catch (NotWritableException $e) {
            $this->assertSame('File or directory is not writable', $e->getMessage());
            $this->assertNull($e->getFilePath());
        }
    }
}
