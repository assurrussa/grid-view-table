<?php
/**
 * Автоматизация запросов
 *
 * @var \Illuminate\Routing\Router $router
 */

if(config('amigridview.routes')) {
    $router->get('{model}/create', ['as' => 'create', 'uses' => 'ModelBaseGridController@create']);
    $router->get('{model}/{id}', ['as' => 'show', 'uses' => 'ModelBaseGridController@show']);
    $router->get('{model}/{id}/edit', ['as' => 'edit', 'uses' => 'ModelBaseGridController@edit']);
    $router->post('{model}', ['as' => 'store', 'uses' => 'ModelBaseGridController@store']);
    $router->post('{model}/{id}', ['as' => 'update', 'uses' => 'ModelBaseGridController@update']);
    $router->delete('{model}/{id}', ['as' => 'delete', 'uses' => 'ModelBaseGridController@destroy']);
    $router->post('{model}/{id}/restore', ['as' => 'restore', 'uses' => 'ModelBaseGridController@restore']);

    $router->get('{model}/sync', ['as' => 'sync', 'uses' => 'ModelBaseGridController@sync']);
    $router->get('{model}/{scope}/sync', ['as' => 'sync', 'uses' => 'ModelBaseGridController@sync']);
}