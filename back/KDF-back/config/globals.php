<?php

    // TLS config
    define('HTTPS_ACTIVE', false);

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
        define('IS_HTTPS', true);
    else
        define('IS_HTTPS', false);

    // Paths constants
    define('HOST', 'localhost');
    define('APPS_DIR', '/');
    define('BASEPATH', HOST.APPS_DIR);
    define('ROOTPATH', realpath(dirname(__FILE__) . '/../') . '/');
    define('FILES_PATH', ROOTPATH . '_files/');

    // JWT files
    define('JWTHEADER', 'config/jwt.header.json');
    define('JWTPAYLOAD', 'config/jwt.payload.json');
    define('JWTSECRET', 'config/jwt.secret.json');

    // Cryptographic and API keys
    define('MYMODEL_CRYPTO_KEY', 'H8s56cYtrd51fXdc6s684m8eCf6e56j181q5q7j');
    define('RECAPTCHA_PUBLIC_KEY', '6LcU2eUSAAAAAIW5XIVhV6frjiuspt7tKTGcNdyd');
    define('RECAPTCHA_PRIVATE_KEY', '6LcU2eUSAAAAAEw17_4E1q6UvcZf3tenXJz-PUck');
    define('GMAP_KEY', 'AIzaSyCw96PdKth4SnFjWMeickt6nhffe3_tXl8');

    // Mailer setup
    $mailer_code = "";
    $mailer_sender = "";
    $mailer_user = "";
    $mailer_pass = "";
    $mailer_host = "";
    $mailer_authtype = "";
    $mailer_port = 587;