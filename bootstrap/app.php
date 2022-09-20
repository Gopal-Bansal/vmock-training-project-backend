<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));



$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

 $app->withFacades();   //uncommented these lines
 $app->withEloquent();

$app->configure('mail');
$app->configure('services');
$app->configure('database'); 
$app->configure('jwt');




$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

 $app->register(App\Providers\CatchAllOptionsRequestsProvider::class);



$app->configure('app');
$app->configure('mail');





$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'verified' => App\Http\Middleware\EnsureEmailIsVerified::class,
    'auth.role' => App\Http\Middleware\RoleAuthorization::class,
]);
$app->middleware([
    App\Http\Middleware\CorsMiddleware::class
 ]);


$app->register(Illuminate\Mail\MailServiceProvider::class);




$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);

$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);
//$app->alias('Pusher',Pusher\Pusher::class);
$app->alias('Pusher' , Pusher\Pusher::class);







 $app->register(App\Providers\AppServiceProvider::class);
 $app->register(App\Providers\AuthServiceProvider::class);
 $app->register(App\Providers\EventServiceProvider::class);//
//$app->register(Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class); //added this line from jwt tutorial



$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->register(Illuminate\Auth\Passwords\PasswordResetServiceProvider::class);
$app->register(Illuminate\Notifications\NotificationServiceProvider::class);
$app->withFacades(true, [
    'Illuminate\Support\Facades\Notification' => 'Notification',
    ]);


//$app->alias('JWTAuth', Tymon\JWTAuth\Facades\JWTAuth::class);
//$app->alias('JWTFactory' , Tymon\JWTAuth\Facades\JWTFactory::class);
$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
