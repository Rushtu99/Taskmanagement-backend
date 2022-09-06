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

$router->get('/', function () use ($router) {
  return $router->app->version();
});

$router->group(['prefix' => 'api', 'middleware' => ['auth', 'Cors']], function () use ($router) {
  $router->put('/users/{id}', ['uses' => 'UserController@update']);
  $router->post('me', 'AuthController@me');
  $router->get('role', 'UserController@role');

  $router->post('logout', 'AuthController@logout');
  $router->post('refresh', 'AuthController@refresh');
  $router->post('/showTasks', 'TaskController@showTasks');
  $router->post('changeStatus', 'TaskController@changeStatus');
  $router->post('changeRole', 'UserController@changeRole');

  $router->post('createTask', ['uses' => 'TaskController@createTask']);
});

$router->group(['prefix' => 'api', 'middleware' => 'Cors'], function () use ($router) {
  $router->get('/users',  ['uses' => 'UserController@showAllUsers']);
  $router->get('/users/{id}', ['uses' => 'UserController@showOneUser']);
  $router->post('login', 'AuthController@login');
  $router->post('isAdmin', 'AuthController@admin');
  $router->post('users', 'UserController@create');
});
$router->group(['middleware' => 'Cors'], function () use ($router) {
  $router->options('api/isAdmin', 'AuthController@Cors');
  $router->options('api/showTasks', 'AuthController@Cors');
  $router->options('api/logout', 'AuthController@Cors');
  $router->options('api/changeStatus', 'AuthController@Cors');
  $router->options('api/createTask', 'AuthController@Cors');
  $router->options('api/me', 'AuthController@Cors');
  $router->options('email/request-verification', 'AuthController@Cors');
  $router->options('email/verify', 'AuthController@Cors');
  $router->options('api/changeRole', 'AuthController@Cors');
  $router->options('/password/reset-request', 'AuthController@Cors');
  $router->options('/password/reset', 'AuthController@Cors');


});



$router->group(['prefix' => 'api', 'middleware' => ['adminCheck', 'Cors']], function () use ($router) {
  $router->post('admin/{id}', ['uses' => 'AuthController@createAdmin']);
  $router->delete('/users/{id}', ['uses' => 'UserController@delete']);
});





$router->group(['prefix' => 'api', 'middleware' => ['Cors', 'adminCheck', 'auth']], function () use ($router) {
  $router->post('test', 'AuthController@test');
});


$router->group(['middleware' => ['auth', 'verified']], function () use ($router) {
  $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'AuthController@emailRequestVerification']);
  //$router->post('/deactivate', 'AuthController@deactivate');
});


//$router->post('/reactivate', 'AuthController@reactivate');
$router->post('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
$router->post('/password/reset', ['as' => 'password.reset', 'uses' => 'ResetPasswordController@reset']);
$router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);
