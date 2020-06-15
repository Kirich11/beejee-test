<?php
/**
 * Config file for describing available routes.
 * Supports only two part URIs, e.g. /aaa/bbb or /aaa
 * 
 *  modelname => [ 
 *      HTTP method => [ 
 *          uri for function => controller function
 *      ]
 *  ]
 */
return [
    'users' => [
        'POST' => [
            'login' => 'login',
        ],
        'GET' => [
            'login' => 'loginPage',
            'logout' => 'logout',
        ]
    ],
    'tasks' => [
        'GET' => [
            '/' => 'index',
            'create' => 'createPage'
        ],
        'POST' => [
            '/' => 'create',
            'update' => 'update',
        ]
    ],
    'admin' => [
        'GET' => [
            '/' => 'index'
        ]
    ]
];