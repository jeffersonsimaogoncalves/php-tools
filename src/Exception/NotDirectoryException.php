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
 * @since       1.1.7
 */
namespace JeffersonSimaoGoncalves\Tools\Exception;

use Exception;

/**
 * "Filename is not a directory" exception
 */
class NotDirectoryException extends Exception
{
    /**
     * @var string|null
     */
    protected $path;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param string|null $path Path of filename that is not a directory
     * @uses $path
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $path = null)
    {
        if (!$message) {
            $message = 'Filename is not a directory';
            if ($path) {
                $message = sprintf('Filename `%s` is not a directory', rtr($path));
            }
        }
        parent::__construct($message, $code, $previous);
        $this->path = $path;
    }

    /**
     * Gets the path of filename that is not a directory
     * @return string|null
     * @since 1.2.11
     * @uses $path
     */
    public function getFilePath()
    {
        return $this->path;
    }
}
