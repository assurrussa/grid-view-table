<?php

declare(strict_types=1);

/**
 * This file is part of GridView package.
 */

namespace Assurrussa\GridView\Exception;

use Assurrussa\GridView\GridView;

/**
 * Class GridViewException
 *
 * @author  AssurrussA <assurrussa777@gmail.com>
 *
 * @package Assurrussa\GridView\Exception
 */
class GridViewException extends \Exception
{
    /**
     * GridViewException constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message = '', int $code = 0, \Exception $previous = null)
    {
        $message = $message ? $message : 'Unknown "' . GridView::NAME . '" exception.';
        parent::__construct($message, $code, $previous);
    }
}