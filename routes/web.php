<?php

/** @var \Laravel\Lumen\Routing\Router $router */



/*
$router->get('/', function () use ($router) {
    return $router->app->version();
});
*/ //uncommented from jwt tutorial


$router->get('/', function () use ($router) {
    echo "<center> Welcome </center>";
});

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

Route::group([

    //'prefix' => 'api', //uncommented by seeing the jwt tutorial without seeder
    'prefix' => 'auth', //added this seeing the previous tutorial
    'middleware' => 'api',
    

], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
   // Route::post('refresh', 'AuthController@refresh');
   // Route::post('user-profile', 'AuthController@me');

});


 
$router->get('/users',['uses' =>'AuthController@showAllUsers'] );
$router->get('/tasks/{id}',['uses' => 'TaskController@showAllTasks']);
$router->get('/tasks',['uses' => 'TaskController@showAllTasksAdmin']);


$router->post('/user', 'AuthController@create');
$router->post('deleteuser/{id}', ['uses' => 'AuthController@deleteusers']);
$router->post('updateuser/{id}',['uses' => 'AuthController@updateuser']);





  
  $router->group(['middleware' => ['auth', 'verified']], function () use ($router) {
    $router->post('/logout', 'AuthController@logout');
    //$router->get('/user', 'AuthController@user');
    $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'AuthController@emailRequestVerification']);
   // $router->post('/refresh', 'AuthController@refresh');
   // $router->post('/deactivate', 'AuthController@deactivate');
  });



  //$router->post('/password/email', 'PasswordController@postEmail');
  //$router->post('/register', 'AuthController@register');
  $router->post('/login', 'AuthController@login');
  //$router->post('/reactivate', 'AuthController@reactivate');
  $router->post('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
  $router->post('/password/reset', [ 'as' => 'password.reset', 'uses' => 'ResetPasswordController@reset' ]);
  $router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);
  $router->options('user', ['middleware' => 'CorsMiddleware', 'uses' => 'AuthController@create']);



  $router->post('/createtask', 'TaskController@create');
$router->post('/delete/{id}', 'TaskController@delete');
$router->post('/updatetask/{id}', 'TaskController@update');
$router->post('/edittask/{id}', 'TaskController@edittask');
$router->get('/showtasks/{id}', 'TaskController@showAllTasks');
$router->get('/showtasks', 'TaskController@showAllTasksAdmin');
$router->get('search/', 'UsersController@showAllUsers');  //empty box
$router->get('/search/{input}', 'UsersController@searchText');  
$router->get('/searchtask/{input}', 'TaskController@searchTask'); 
$router->get('/searchtask/', 'TaskController@showAllTasksAdmin'); //emptybox
$router->get('/searchtask/{input}/{id}', 'TaskController@searchtaskuser');
$router->get('/searchtask//{id}', 'TaskController@showAllTasks');
$router->get('/filterrole/{field}/{value}', 'UsersController@filterUser');
$router->get('/filtertaskadmin/{field}/{value}','TaskController@filtertaskadmin');
$router->get('/sorttaskadmin/{field}/{order}','TaskController@sorttaskadmin');
$router->get('/filtertask/{field}/{value}/{id}','TaskController@filtertask');
$router->get('/sorttask/{field}/{order}/{id}','TaskController@sorttask');
$router->get('/listNotifs',  ['uses' => 'NotificationController@listNotification']);
$router->delete('/notif/{id}',  ['uses' => 'NotificationController@deleteNotification']);
$router->delete('/clear-notif',  ['uses' => 'NotificationController@clearNotification']);

  