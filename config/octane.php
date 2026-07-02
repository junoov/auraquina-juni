<?php

use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerStopping;
use Laravel\Octane\Listeners\CollectGarbage;
use Laravel\Octane\Listeners\DisconnectFromDatabases;
use Laravel\Octane\Listeners\EnsureUploadedFilesAreValid;
use Laravel\Octane\Listeners\FlushTemporaryContainerInstances;
use Laravel\Octane\Listeners\ReportException;
use Laravel\Octane\Listeners\StopWorkerIfNecessary;

return [

    /*
    |--------------------------------------------------------------------------
    | Octane Server
    |--------------------------------------------------------------------------
    |
    | This value determines the default "server" that will be used by Octane
    | when starting, restarting, or stopping your server via the CLI. You
    | are free to change the server to the one you want to use here.
    |
    */

    'server' => env('OCTANE_SERVER', 'frankenphp'),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, Octane will prepare URLs so that all
    | generated URLs are using the HTTPS protocol. This is useful if your
    | application is running behind a TLS-terminating proxy or similar.
    |
    */

    'https' => (bool) env('OCTANE_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Listeners
    |--------------------------------------------------------------------------
    |
    | The following array lists the event listeners that will be registered
    | for Octane events. You may modify or remove any of these listeners
    | as required to implement your own custom application behavior.
    |
    */

    'listeners' => [
        WorkerStarting::class => [
            EnsureUploadedFilesAreValid::class,
        ],

        RequestReceived::class => [
            ...Octane::prepareApplicationForNextOperation(),
            ...Octane::prepareApplicationForNextRequest(),
            FlushTemporaryContainerInstances::class,
            // Fix Spatie Permission state bleed (User A gets User B's permissions)
            function () {
                if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                }
            },
        ],

        RequestHandled::class => [
            //
        ],

        TaskReceived::class => [
            ...Octane::prepareApplicationForNextOperation(),
            FlushTemporaryContainerInstances::class,
        ],

        TickReceived::class => [
            ...Octane::prepareApplicationForNextOperation(),
            FlushTemporaryContainerInstances::class,
        ],

        WorkerStopping::class => [
            ...Octane::prepareApplicationForNextOperation(),
            DisconnectFromDatabases::class,
            CollectGarbage::class,
        ],

        OperationTerminated::class => [
            CollectGarbage::class,
        ],

        RequestTerminated::class => [
            StopWorkerIfNecessary::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Warm / Flush
    |--------------------------------------------------------------------------
    |
    | The following list of classes or services will get warm when a fresh
    | Octane worker starts. You may add any classes or services here to
    | prepare them for the next request cycle.
    |
    */

    'warm' => [
        // ...
    ],

    /*
    |--------------------------------------------------------------------------
    | Flush
    |--------------------------------------------------------------------------
    |
    | The following list of classes or services will get flushed when a fresh
    | Octane worker starts. You may add any classes or services here to
    | prepare them for the next request cycle.
    |
    */

    'flush' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Terminate
    |--------------------------------------------------------------------------
    |
    | The following list of classes or services will get terminated when the
    | Octane worker stops. You may add any classes or services here to
    | perform any necessary cleanup before the next worker starts.
    |
    */

    'terminate' => [
        //
    ],

];
