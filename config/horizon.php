<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => 'st-horizon:',

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:st-redis' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 60,
        'failed' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'environments' => [
        'production' => [
            'st-worker-1' => [
                'connection' => 'redis',
                'queue'      => ['broadcast'],
                'balance'    => false,
                'processes'  => 1,
                'tries'      => 3,
            ],

            'st-worker-2' => [
                'connection' => 'redis',
                'queue'      => ['default', 'low-fast'],
                'balance'    => false,
                'processes'  => 1,
                'tries'      => 1,
            ],

            'st-worker-3' => [
                'connection' => 'redis',
                'queue'      => ['slow-high', 'sub-idx'],
                'balance'    => false,
                'processes'  => 1,
                'tries'      => 1,
            ],
        ],


        'local' => [
            'st-worker-1' => [
                'connection' => 'redis',
                'queue'      => ['broadcast'],
                'balance'    => false,
                'processes'  => 1,
                'tries'      => 3,
            ],

            'st-worker-2' => [
                'connection' => 'redis',
                'queue'      => ['default', 'low-fast'],
                'balance'    => false,
                'processes'  => 1,
                'tries'      => 1,
            ],

            'st-worker-3' => [
                'connection' => 'redis',
                'queue'      => ['slow-high', 'sub-idx'],
                'balance'    => false,
                'processes'  => 1,
                'tries'      => 1,
            ],
        ],
    ],
];
