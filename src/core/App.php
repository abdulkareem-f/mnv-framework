<?php

namespace MNV\Core;

use Dotenv\Dotenv;

class App{
	
	public string $baseDirPath;
	public Request $request;
	
	public function __construct(string $baseDirPath)
	{
		$this->baseDirPath = $baseDirPath;
		$this->request = new Request();
	}
	
	public function run(){
		$dotenv = Dotenv::createImmutable($this->baseDirPath);
		$dotenv->load();
		
		try {
			echo Router::call($this->request);
		} catch (\Exception $e){
			echo $e->getMessage();
		}
	}
	
}