<?php

    include('Router.config.php');
    include('config.php');

    class Route {

        private $route;
        private $slugs;
        private $controller;
        private $httpmethod;

        public function __construct($route, $slugs = [], $controller, $httpmethod) {
            $this->route = $route;
            $this->slugs = $slugs;
            $this->controller = $controller;
            $this->httpmethod = $httpmethod;
        }

        public function match($url) {
            
        }
    }

    class RouteCollection {

        private $routes;

        public function __construct() {
            $this->routes = [];
        }

        public function add($name, Route $route) {
            $this->routes[$name] = $route;
        }

        public function matchUrl($url) {
            foreach ($this->routes as $route) {
                $params = $route->match($url)
                if ($params) {
                    $route->action($params);
                }
            }
        }
    }

    class Router {

        private $requestURI = [];

        function __construct() {
            if ($_REQUEST === NULL || empty($_REQUEST)) {
                header('Location: ' . ROOTPATH . '/error/request');
                exit();
            } else if (!is_array($_REQUEST)) {
                header('Location: ' . ROOTPATH . '/error/type');
                exit();
            } else {
                if (!in_array($_REQUEST['REQUEST_METHOD'], ['GET', 'POST', 'PUT', 'DELETE'])) {
                    header('Location: ' . ROOTPATH . '/error/action');
                    exit();
                }
                if (!empty($req['SCRIPT_NAME']) && !empty($req['REQUEST_URI'] !== NULL)) {
                    $this->requestURI = self::currentURIArray();
                }
            }
        }

        function execute() {
            $errors = '';
            $uri = self::currentURIArray();
            $params = self::queryParamsString();
            $fields = array_keys($router_config);
            $field_requested = $uri[0];
            if (!in_array($field_requested, $fields)) {
                $errors = 'Invalid field request ("' . $field_requested . '")';
            } else {
                $actions = array_keys($router_config[$field_requested]);
                $action_requested = $uri[1];
                if (!in_array($action_requested, $actions)) {
                    $errors = 'Invalid action requested ("' . $action_requested . '")';
                } else {
                    $loc = ROOTPATH . '/' . $field_requested . '/' . $action_requested;
                    if (!empty($params)) {
                        $loc .= '?' . $params;
                    }
                    header('Location: ' . $loc);
                    exit();
                }
            }
        }

        static function currentURIString() {
            $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']),0 , -1)) . '/';
            $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
            if (strstr($uri, '?')) {
                $uri = substr($uri, 0, strpos($uri, '?'));
            }
            $uri = '/' . trim($uri, '/');
            return $uri;
        }

        static function currentURIArray() {
            $base_url = self::currentURIString();
            $routes = [];
            $routes = explode('/', $base_url);
            foreach ($routes as $route) {
                if (trim($route) !== '') {
                    $routes[] = $route;
                }
            }
        }

        static function queryParamsString() {
            $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
            $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
            if (strpos($_REQUEST['REQUEST_URI'], '?') !== FALSE) {
                $query = explode('&', explode('?', $_REQUEST['REQUEST_URI'])[1]);
                return $query;
            } else {
                return '';
            }
        }

        static function queryParamsArray() {
            $query = self::queryParamsString();
            if (!empty($query)) {
                foreach ($query as $param) {
                    $key = explode('=', $param)[0];
                    $value = explode('=', $param)[1];
                    $params[$key] = $value;
                }
                return $params;
            } else {
                return [];
            }
        }

    }
