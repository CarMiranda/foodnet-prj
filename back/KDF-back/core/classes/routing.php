<?php
    
    use Symfony\Component\Routing\Matcher\UrlMatcher;
    use Symfony\Component\Routing\RequestContext;
    use Symfony\Component\Routing\RouteCollection;
    use Symfony\Component\Routing\Route;

    $routes = new RouteCollection();

    $routes->add('user_show', new Route('/user/show?{idsrc}={id}', [
        '_controller' => 'User:'
    ]));