<?php

declare(strict_types=1);

/**
 * This file is part of GridView package.
 */

namespace Assurrussa\GridView\Exception;

/**
 * Class GridViewException
 *
 * @author  AssurrussA <assurrussa777@gmail.com>
 *
 * @package Assurrussa\GridView\Exception
 */
class QueryException extends GridViewException
{
    public function __construct(string $message = '', int $code = 0, \Exception $previous = null)
    {
        parent::__construct('Not set Query Builder. Query Builder is null', $code, $previous);
    }
}