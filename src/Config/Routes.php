<?php

$routes->group('admin', function ($routes) {


    $routes->resource('log', [
        'filter' => 'permission:log-permission',
        'controller' => 'logController',
        'namespace' => 'julio101290\boilerplatelog\Controllers',
        'except' => 'show'
    ]);
    $routes->post('log/save'
            , 'LogController::save'
            , ['namespace' => 'julio101290\boilerplatelog\Controllers']);

    $routes->post('log/getLog'
            , 'LogController::getLog'
            , ['namespace' => 'julio101290\boilerplatelog\Controllers']
    );

    // Rutas para compacDB
    // Rutas para compacDB
    $routes->resource('compacDB', [
        'filter' => 'permission:compacDB-permission',
        'controller' => 'CompacDBController',
        'except' => 'show',
        'namespace' => 'julio101290\boilerplatecompac\Controllers',
    ]);

    $routes->post('compacDB/save'
            , 'CompacDBController::save'
            , ['namespace' => 'julio101290\boilerplatecompac\Controllers', 'filter' => 'csrf']
    );

    $routes->post('compacDB/getCompacDB'
            , 'CompacDBController::getCompacDB'
            , ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    $routes->post(
            'compacDB/testConnection',
            'CompacDBController::testConnection',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    // Rutas para empleadosCompac
    $routes->get('empleadosCompac'
            , 'CompacEmpleadosController::index'
            , ['namespace' => 'julio101290\boilerplatecompac\Controllers', 'filter' => 'permission:compacDB-permission']
    );

    $routes->post('compacEmpleados/getEmpleados'
            , 'CompacEmpleadosController::getEmpleados'
            , ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    $routes->post('compacEmpleados/getEmpleados'
            , 'CompacEmpleadosController::getEmpleados'
            , ['namespace' => 'julio101290\boilerplatecompac\Controllers', 'filter' => 'csrf']
    );

    $routes->get('compacEmpleados/credencialGenerator'
            , 'CompacEmpleadosController::credencialGenerator'
            , ['namespace' => 'julio101290\boilerplatecompac\Controllers']);
});
