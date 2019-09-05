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
namespace App;

use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

class SkipTestCase extends TestCase
{

    /**
     * test that a test is marked as skipped using skipIf and its first parameter evaluates to true
     *
     * @return void
     */
    public function testSkipIfTrue()
    {
        $this->skipIf(true);
    }

    /**
     * test that a test is not marked as skipped using skipIf and its first parameter evaluates to false
     *
     * @return void
     */
    public function testSkipIfFalse()
    {
        $this->skipIf(false);
    }
}
