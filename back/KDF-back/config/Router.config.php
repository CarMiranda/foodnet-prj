<?php

    $router_config = [
        'users' => [
            'add' => 'adduser',             // POST
            'login' => 'loginuser',         // POST
            'delete' => 'deluser',          // DELETE
            'update' => 'updateuser',       // PUT
            'search' => 'searchuser'        // GET
        ],
        'post' => [
            'add' => 'addpost',             // POST
            'delete' => 'delpost',          // DELETE
            'update' => 'updatepost',       // PUT
            'search' => 'searchpost',       // GET
        ],
        'friends' => [
            'add' => 'addfriends',          // POST
            'delete' => 'deletefriends',    // DELETE
            'update' => 'updatefriends',    // PUT
            'search' => 'searchfriends'     // GET
        ]
    ];
