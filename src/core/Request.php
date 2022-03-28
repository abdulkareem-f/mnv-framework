<?php

namespace MNV\Core;

class Request {
	
	public function getMethod(): string {
		return strtolower($_SERVER['REQUEST_METHOD']);
	}
	
	public function isGet(): bool{
		return $this->getMethod() === 'get';
	}
	
	public function isPost(): bool{
		return $this->getMethod() === 'post';
	}
	
	public function all(): array{
		$data = [];
		if ($this->isGet()) {
			foreach ($_GET as $key => $value) {
				$data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
		if ($this->isPost()) {
			foreach ($_POST as $key => $value) {
				$data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
		return $data;
	}
}