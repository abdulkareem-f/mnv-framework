<?php

namespace MNV\App\Controllers;

use MNV\Core\Base\Controller;
use MNV\Core\Request;
use MNV\App\Models\Article;

class ArticleController extends Controller {

	public function index(){
		$articleObj = new Article();
		$allArticles = $articleObj->getDB()->get();
		
		return view('blog/index', ['articles' => $allArticles]);
	}
	
	public function create(){
		return view('blog/create');
	}
	
	public function store(Request $request){
		$data = $request->all();
		if($data['title'] && $data['body'] && $data['author']) {
			$data['created_at'] = 'date("Y-m-d H:i:s")';
			
			$article = new Article();
			$article->getDB()->insert($data);
			
			redirect(url('articles'));
		} else {
			set_response_code(self::VALIDATION_ERROR);
			redirect(url('articles/create'));
		}
	}
	
	public function show($id){
		$articleObj = new Article();
		$article = $articleObj->getDB()->get(['id' => $id]);
		
		if(isset($article[0])){
			return view('blog/show', ['article' => $article[0]]);
		} else {
			set_response_code(self::NOT_FOUND);
			return view('errors/404');
		}
	}
	
	public function update($id, Request $request){
		$data = $request->all();
		if($data['title'] && $data['body'] && $data['author']) {
			$article = new Article();
			$article->getDB()->update($data, ['id' => $id]);
			
			redirect(url('articles'));
		} else {
			set_response_code(self::VALIDATION_ERROR);
			redirect(url("articles/$id/edit"));
		}
	}
	
	public function edit($id){
		$articleObj = new Article();
		$article = $articleObj->getDB()->get(['id' => $id]);
		
		if(isset($article[0])){
			return view('blog/edit', ['article' => $article[0]]);
		} else {
			set_response_code(self::NOT_FOUND);
			return view('errors/404');
		}
	}
	
	
	public function articlesByDate($year, $month, $day){
		$byDate = "$year-$month-$day";
		$articleObj = new Article();
		$allArticles = $articleObj->getDB()->get(['created_at' => $byDate]);
		
		return view('blog/index', ['articles' => $allArticles, 'by_date' => $byDate]);
	}
}