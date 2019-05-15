<?php
declare(strict_types=1);
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
 * @since       1.1.7
 */
namespace Tools\Exception;

use Exception;

/**
 * "File or directory does not exist" exception
 */
class FileNotExistsException extends Exception
{
    /**
     * Constructor
     * @param string $message The string of the error message
     * @param int $code The code of the error
     * @param \Exception|null $previous the previous exception
     */
    public function __construct($message = 'File or directory does not exist', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
