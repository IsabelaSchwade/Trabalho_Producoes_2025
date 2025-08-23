<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Producao::index');
$routes->get('/producoes', 'Producao::index');

$routes->get('producao/delete/(:num)', 'Producao::delete/$1');
$routes->get('producao/edit/(:num)', 'Producao::edit/$1');

$routes->post('producao/update/(:num)', 'Producao::update/$1');
$routes->get('producao/create', 'Producao::create');
$routes->post('producao/store', 'Producao::store');
$routes->get('recomendacoes', 'Producao::recomendacoes');
$routes->get('producoes/search', 'Producao::search');
$routes->get('producoes/(:any)', 'Producao::index/$1');
$routes->get('producao/view/(:num)', 'Producao::view/$1');



