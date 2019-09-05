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
 * @since       1.2.6
 */
namespace JeffersonSimaoGoncalves\Tools\Exception;

use Exception;

/**
 * "Not in array" exception
 */
class NotInArrayException extends Exception
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param mixed $value Value that is not in array
     * @uses $value
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $value = null)
    {
        if (!$message) {
            $message = 'Value is not in the array';
            if ($value && is_stringable($value)) {
                $message = sprintf('Value `%s` is not in the array', (string)$value);
            }
        }
        parent::__construct($message, $code, $previous);
        $this->value = $value;
    }

    /**
     * Gets the value that is not in array
     * @return mixed
     * @since 1.2.11
     * @uses $value
     */
    public function getValue()
    {
        return $this->value;
    }
}
