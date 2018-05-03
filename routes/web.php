<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});


$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('recipes', 'RecipeController@index');
    $router->post('recipes/create', 'RecipeController@store');
    $router->post('recipes/delete/{id}', 'RecipeController@destroy');
});

$router->post('users/store', 'UserController@store');
$router->post('users/login', 'UserController@login');
