<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Filme::index');
$routes->get('/filmes', 'Filme::index');

$routes->get('filme/excluir/(:num)', 'Filme::excluir/$1');
$routes->get('filme/editar/(:num)', 'Filme::editar/$1');
$routes->post('filme/atualizar/(:num)', 'Filme::atualizar/$1');
$routes->get('filme/formulario', 'Filme::formulario');
$routes->post('filme/cadastrar', 'Filme::cadastrar');
$routes->get('recomendacoes', 'Filme::recomendacoes');
$routes->get('filmes/search', 'Filme::search');
$routes->get('filme/view/(:num)', 'Filme::view/$1');





$routes->get('filmes/(:any)', 'Filme::index/$1');
