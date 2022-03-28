<?php

namespace MNV\Core\Base;


abstract class Controller {
	
	const SUCCESS = 200;
	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const VALIDATION_ERROR = 422;
	
	protected array $resData;
	protected array $errData;
	protected string $msg;
	
	public function __construct(){
		$this->resData = $this->errData = [];
		$this->msg = '';
	}
	
	public function response(array $data, string $message='', int $code=self::SUCCESS) {
		$response = [
			'data'      => 	$data,
			'message'   =>  $message,
		];
		
		http_response_code($code);
		return json_encode($response);
	}
	
	public function error(string $message, array $errors=[], int $code=self::BAD_REQUEST){
		$response = [
			'message'   => 	$message,
			'errors'    =>  $errors,
		];
		
		http_response_code($code);
		return json_encode($response);
	}
	
}