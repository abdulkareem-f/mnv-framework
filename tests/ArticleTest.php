<?php

namespace Tests;

use MNV\App\Models\Article;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
	public Article $articleObj;
	public array $articleData;
	
	public function __construct(?string $name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		$this->articleObj = new Article();
		$testArticleData = $this->getArticleData();
		$this->articleData = $this->articleObj->getDB()->insert($testArticleData);
	}
	
	public function test_create_an_article(){
		$testArticleData = $this->getArticleData();
		$articleData = $this->articleObj->getDB()->insert($testArticleData);
		
		$this->assertSame($testArticleData['title'], $articleData['title']);
		$this->assertSame($testArticleData['body'], $articleData['body']);
		$this->assertSame($testArticleData['author'], $articleData['author']);
		$this->assertSame($testArticleData['created_at'], $articleData['created_at']);
	}
	
	public function test_get_an_article(){
		$testArticleData = $this->getArticleData();
		$articleData = $this->articleObj->getDB()->get(['id' => $this->articleData['id']])[0];
		
		$this->assertSame($testArticleData['title'], $articleData['title']);
		$this->assertSame($testArticleData['body'], $articleData['body']);
		$this->assertSame($testArticleData['author'], $articleData['author']);
	}
	
	public function test_update_an_article(){
		$testArticleUpdateData = $this->getArticleUpdateData();
		$articleUpdateData = $this->articleObj->getDB()->update($testArticleUpdateData, ['id' => $this->articleData['id']]);
		
		$this->assertSame($testArticleUpdateData['title'], $articleUpdateData['title']);
		$this->assertSame($testArticleUpdateData['body'], $articleUpdateData['body']);
		$this->assertSame($testArticleUpdateData['author'], $articleUpdateData['author']);
	}
	
	private function getArticleData(): array{
		return [
			'title'         =>  'Some article title',
			'body'          =>  'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet animi assumenda autem distinctio esse facilis harum laudantium libero mollitia, natus odio, saepe veniam. Aperiam facilis magnam quis tempora vero! Tenetur!',
			'author'        =>  'Joe Doe',
			'created_at'    =>  date("Y-m-d H:i:s")
		];
	}
	
	private function getArticleUpdateData(): array{
		return [
			'title'         =>  'Update Some article title',
			'body'          =>  'Update Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet animi assumenda autem distinctio esse facilis harum laudantium libero mollitia, natus odio, saepe veniam. Aperiam facilis magnam quis tempora vero! Tenetur!',
			'author'        =>  'Jane Doe'
		];
	}
}
