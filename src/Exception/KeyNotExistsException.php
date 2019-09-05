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
 * @since       1.1.10
 */
namespace JeffersonSimaoGoncalves\Tools\Exception;

use Exception;

/**
 * "Array key does not exist" exception
 */
class KeyNotExistsException extends Exception
{
    /**
     * @var string|null
     */
    protected $key;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param string|null $key Name of the key that do not exist
     * @uses $key
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $key = null)
    {
        if (!$message) {
            $message = 'Array key does not exist';
            if ($key) {
                $message = sprintf('Array key `%s` does not exist', $key);
            }
        }
        parent::__construct($message, $code, $previous);
        $this->key = $key;
    }

    /**
     * Gets the name of the key that do not exist
     * @return string|null
     * @since 1.2.11
     * @uses $key
     */
    public function getKeyName()
    {
        return $this->key;
    }
}
