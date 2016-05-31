<?php

namespace Assurrussa\GridView\Facades;

use Assurrussa\GridView\GridView;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\Auth\AuthManager
 * @see \Illuminate\Auth\SessionGuard
 */
class GridViewFacade extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return GridView::NAME;
	}

}
