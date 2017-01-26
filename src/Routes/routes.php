<?php
/**
 * Автоматизация запросов
 *
 * @var \Illuminate\Routing\Router $router
 */

if(config('amigrid.routes')) {
    $router->get('{model}/create', ['as' => 'amigrid.create', 'uses' => 'ModelBaseGridController@create']);
    $router->get('{model}/{id}', ['as' => 'amigrid.show', 'uses' => 'ModelBaseGridController@show']);
    $router->get('{model}/{id}/edit', ['as' => 'amigrid.edit', 'uses' => 'ModelBaseGridController@edit']);
    $router->post('{model}', ['as' => 'amigrid.store', 'uses' => 'ModelBaseGridController@store']);
    $router->post('{model}/{id}', ['as' => 'amigrid.update', 'uses' => 'ModelBaseGridController@update']);
    $router->delete('{model}/{id}', ['as' => 'amigrid.delete', 'uses' => 'ModelBaseGridController@destroy']);
    $router->post('{model}/{id}/restore', ['as' => 'amigrid.restore', 'uses' => 'ModelBaseGridController@restore']);
}