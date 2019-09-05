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

use JeffersonSimaoGoncalves\Tools\Exception\NotDirectoryException;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * NotDirectoryExceptionTest class
 */
class NotDirectoryExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        $file = ROOT . 'dir' . DS . 'notDirectory';
        try {
            throw new NotDirectoryException(null, 0, null, $file);
        } catch (NotDirectoryException $e) {
            $this->assertSame('Filename `dir/notDirectory` is not a directory', $e->getMessage());
            $this->assertSame($file, $e->getFilePath());
        }

        try {
            throw new NotDirectoryException();
        } catch (NotDirectoryException $e) {
            $this->assertSame('Filename is not a directory', $e->getMessage());
            $this->assertNull($e->getFilePath());
        }
    }
}
