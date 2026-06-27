<?php

$routes->group('admin', function ($routes) {
    $routes->resource('log', [
        'filter' => 'permission:log-permission',
        'controller' => 'logController',
        'namespace' => 'julio101290\boilerplatelog\Controllers',
        'except' => 'show'
    ]);
    $routes->post('log/save',
            'LogController::save',
            ['namespace' => 'julio101290\boilerplatelog\Controllers']
    );
    $routes->post('log/getLog',
            'LogController::getLog',
            ['namespace' => 'julio101290\boilerplatelog\Controllers']
    );

    // =============================================
    // RUTAS compacDB
    // =============================================
    $routes->resource('compacDB', [
        'filter' => 'permission:compacDB-permission',
        'controller' => 'CompacDBController',
        'except' => 'show',
        'namespace' => 'julio101290\boilerplatecompac\Controllers',
    ]);
    $routes->post('compacDB/save',
            'CompacDBController::save',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers', 'filter' => 'csrf']
    );
    $routes->post('compacDB/getCompacDB',
            'CompacDBController::getCompacDB',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );
    $routes->post('compacDB/testConnection',
            'CompacDBController::testConnection',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    // =============================================
    // RUTAS compacEmpleados
    // =============================================
    $routes->get('empleadosCompac',
            'CompacEmpleadosController::index',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers', 'filter' => 'permission:compacDB-permission']
    );

    // Listado server-side para DataTables
    $routes->post('compacEmpleados/getEmpleadosServerSide',
            'CompacEmpleadosController::getEmpleadosServerSide',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    // Listado completo (legacy / Linux)
    $routes->post('compacEmpleados/getEmpleados',
            'CompacEmpleadosController::getEmpleados',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    // Configuración de credencial
    $routes->post('compacEmpleados/getCredencialConfig',
            'CompacEmpleadosController::getCredencialConfig',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    // Generador de credencial (vista)
    $routes->get('compacEmpleados/credencialGenerator',
            'CompacEmpleadosController::credencialGenerator',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    // Fotos de empleados
    $routes->post('compacEmpleados/uploadLocalFoto',
            'CompacEmpleadosController::uploadLocalFoto',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );
    $routes->post('compacEmpleados/guardarFotoEmpleado',
            'CompacEmpleadosController::guardarFotoEmpleado',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );
    $routes->post('compacEmpleados/getLocalFotoBase64',
            'CompacEmpleadosController::getLocalFotoBase64',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );

    $routes->get('compacEmpleados/datosGenerator',
            'CompacEmpleadosController::datosGenerator',
            ['namespace' => 'julio101290\boilerplatecompac\Controllers']
    );
});
