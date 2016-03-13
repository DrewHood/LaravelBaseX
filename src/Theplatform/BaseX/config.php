<?php

return [
	/*
    |--------------------------------------------------------------------------
    | BaseX Configuration
    |--------------------------------------------------------------------------
    | 
    | Settings for BaseX database acces and credentials.
    |
    */
    'baseX' => [
        'basePath' => env('BASEX_PATH'),
        'user' => 'admin',
        'password' => 'admin',
        'options' => '?stripns=true&intparse=true',
    ],
];