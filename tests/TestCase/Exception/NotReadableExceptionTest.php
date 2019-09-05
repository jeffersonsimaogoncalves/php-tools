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

use JeffersonSimaoGoncalves\Tools\Exception\NotReadableException;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * NotReadableExceptionTest class
 */
class NotReadableExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        $file = ROOT . 'dir' . DS . 'notReadableFile';
        try {
            throw new NotReadableException(null, 0, null, $file);
        } catch (NotReadableException $e) {
            $this->assertSame('File or directory `dir/notReadableFile` is not readable', $e->getMessage());
            $this->assertSame($file, $e->getFilePath());
        }

        try {
            throw new NotReadableException();
        } catch (NotReadableException $e) {
            $this->assertSame('File or directory is not readable', $e->getMessage());
            $this->assertNull($e->getFilePath());
        }
    }
}
