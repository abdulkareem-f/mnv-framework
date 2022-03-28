<?php

use MNV\Core\Router;
use MNV\App\Controllers\ArticleController;

Router::get('/', function(){
	return view('welcome');
});

Router::get('/articles', 'ArticleController@index');

Router::get('/articles/create', [ArticleController::class, 'create']);
Router::post('/articles', [ArticleController::class, 'store']);

Router::get('/articles/{id}/edit', [ArticleController::class, 'edit']);
Router::post('/articles/{id}/update', [ArticleController::class, 'update']);

Router::get('/articles/{id}', [ArticleController::class, 'show']);
Router::get('/articles/{year}/{month}/{day}', [ArticleController::class, 'articlesByDate']);