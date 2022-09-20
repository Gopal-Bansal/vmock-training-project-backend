<?php

return [


    'default' => env('BROADCAST_DRIVER', 'pusher'),

    

    'connections' => [

        // 'pusher' => [
        //     'options' => [
        //         'cluster' => 'ap2',
        //         'useTLS' => true
        //       ],
        //     'driver' => 'pusher',
        //     'key' => env('PUSHER_KEY'),
        //     'secret' => env('PUSHER_SECRET'),
        //     'app_id' => env('PUSHER_APP_ID'),
        // ],


        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                // 'cluster' => env('PUSHER_APP_CLUSTER'),
                'cluster' => 'ap2',
                'useTLS' => false,
                // 'encrypted' => true,
                // 'host' => '127.0.0.1',
                // 'port' => 6001,
                // 'scheme' => 'http'
            ],
        ],

        
        // 'redis' => [
        //     'driver' => 'redis',
        //     'connection' => env('BROADCAST_REDIS_CONNECTION', 'default'),
        // ],

        'log' => [
            'driver' => 'log',
        ],

    ],

];