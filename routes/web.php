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

$router->group(['prefix' => 'api','middleware'=>['auth','Cors']], function () use ($router) {
  $router->put('/users/{id}', ['uses' => 'UserController@update']);
  $router->post('me', 'AuthController@me');
  $router->post('logout', 'AuthController@logout');
  $router->post('refresh', 'AuthController@refresh');
  $router->post('/showTasks','TaskController@showTasks');
  $router->put('changeStatus','TaskController@changeStatus');
});

$router->group(['prefix' => 'api','middleware'=>'Cors'], function () use ($router) {
  $router->get('/users',  ['uses' => 'UserController@showAllUsers']);
  $router->get('/users/{id}', ['uses' => 'UserController@showOneUser']);
  $router->post('login', 'AuthController@login');
  $router->post('isAdmin','AuthController@admin');

  $router->options('isAdmin', 'AuthController@Cors');
  $router->options('me', 'AuthController@Cors');
  $router->options('showTasks','AuthController@Cors');
  $router->options('logout','AuthController@Cors');
  $router->options('changeStatus','AuthController@Cors');
  


});


$router->group(['prefix' => 'api','middleware'=>['adminCheck','Cors']], function () use ($router) {
  $router->post('admin/createTask',['uses' => 'TaskController@createTask']);
  $router->post('admin/{id}', ['uses' => 'AuthController@createAdmin']);
  $router->delete('/users/{id}', ['uses' => 'UserController@delete']);
  $router->post('users', 'UserController@create');

});





$router->group(['prefix' => 'api','middleware'=>['Cors','adminCheck','auth']], function () use ($router) {
  $router->post('test','AuthController@test');

});


$router->group(['middleware' => ['auth', 'verified']], function () use ($router) {
  $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'AuthController@emailRequestVerification']);
  //$router->post('/deactivate', 'AuthController@deactivate');
});


//$router->post('/reactivate', 'AuthController@reactivate');
$router->post('/password/reset-request', 'PasswordController@postEmail');
$router->post('/password/reset', [ 'as' => 'password.reset', 'uses' => 'PasswordController@postReset' ]);
$router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);