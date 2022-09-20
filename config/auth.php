<?php

 
return [
    
    'secret' => env('JWT_SECRET'),
    'defaults' => [
        'guard' => env('AUTH_GUARD','api'),
        'passwords' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
      ],
    'password_timeout' => 10800,
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  =>  App\Models\User::class,
        ]
    ],
   
    'keys' => [
       
        'public' => env('JWT_PUBLIC_KEY'),
        /*
        |--------------------------------------------------------------------------
        | Private Key
        |--------------------------------------------------------------------------
        |
        | A path or resource to your private key.
        |
        | E.g. 'file://path/to/private/key'
        |
        */
        'private' => env('JWT_PRIVATE_KEY'),
        /*
        |--------------------------------------------------------------------------
        | Passphrase
        |--------------------------------------------------------------------------
        |
        | The passphrase for your private key. Can be null if none set.
        |
        */
        'passphrase' => env('JWT_PASSPHRASE'),
    ],
   
    'ttl' => env('JWT_TTL', 60),
    
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),
    
    'algo' => env('JWT_ALGO', 'HS256'),
    
    'required_claims' => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ],
    
    'persistent_claims' => [
        // 'foo',
        // 'bar',
    ],
   
    'lock_subject' => true,
    
    'leeway' => env('JWT_LEEWAY', 0),
    
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
    
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),
    
    'decrypt_cookies' => false,
    
    'providers' => [
        
        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,
        
        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,
        
        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,
        'users' => [
            'driver' => 'eloquent',
            'model'  =>  App\Models\User::class,
        ]
    ],
];