<?php

namespace MNV\Core;

class Router{
	
	private static array $routes;
	
	public static function post(string $uri, $callback){
		if($uri!='/')
			$uri = ltrim($uri, '/');
		
		self::$routes['post'][$uri] = $callback;
	}
	
	public static function get(string $uri, $callback){
		if($uri!='/')
			$uri = ltrim($uri, '/');
		
		self::$routes['get'][$uri] = $callback;
	}
	
	public static function routes(): array{
		return self::$routes;
	}
	
	public static function call(Request $request){
		[$callback, $args] = self::getCallback();
		$args[] = $request;
		
		if(is_callable($callback)) {
			return $callback(...$args);
		} elseif(is_array($callback)) {
			$method = $callback[1];
			return (new $callback[0])->$method(...$args);
		} elseif(is_string($callback) && str_contains($callback, '@')){
			$callbackArr = explode('@',$callback);
			$method = $callbackArr[1];
			$controller = "MNV\App\Controllers\\".$callbackArr[0];
			return (new $controller)->$method(...$args);
		} else {
			throw new \Exception("Cannot call this callback $callback");
		}
	}
	
	public static function getCallback(): array{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$uri = self::extractUriData($method);
		if($uri!='/')
			$uri = trim($uri, '/');
		
		$routes = self::$routes[$method];
		foreach ($routes as $route => $callback) {
			$route = trim($route, '/');
			
			if (!$route) {
				continue;
			}
			
			$routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($r) => isset($r[2]) ? "({$r[2]})" : '(\w+)', $route) . "$@";
			if (preg_match_all($routeRegex, $uri, $valueMatches)) {
				$args = [];
				for ($i = 1; $i < count($valueMatches); $i++) {
					$args[] = $valueMatches[$i][0];
				}
				
				return [$callback, $args];
			}
		}
		if(!isset($routes[$uri])){
			echo view('errors/404');
			set_response_code(404);
			die();
		}
		
		return [$routes[$uri], []];
	}
	
	private static function extractUriData($method): string{
		$pathToPublic = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$uri = str_replace($pathToPublic, '', $_SERVER['REQUEST_URI']);
		if($uri=='')
			$uri = '/';
		
		return $uri;
	}
	
//	public function get($thisUri,$callback){
//		if(!self::$routeFound){
//			$uri = str_replace(ROOT_URI, "/",$_SERVER['REQUEST_URI']);
//			$method = $_SERVER['REQUEST_METHOD'];
//			if($uri==$thisUri && $method=='GET'){
//				self::$routeFound = true;
//				if(is_callable($callback))
//					$callback();
//				else{
//					$callbackArr = explode('@',$callback);
//					$controller = new $callbackArr[0]();
//					$method = $callbackArr[1];
//					if(method_exists($controller,$method)){
//						$controller->$method();
//					}
//				}
//			}
//		}
//	}
//
//	public function post($thisUri,$callback){
//		if(!self::$routeFound){
//			$uri = str_replace(ROOT_URI, "/",$_SERVER['REQUEST_URI']);
//			$method = $_SERVER['REQUEST_METHOD'];
//			if($uri==$thisUri && $method=='POST'){
//				self::$routeFound = true;
//				if(is_callable($callback))
//					$callback();
//				else{
//					$callbackArr = explode('@',$callback);
//					$controller = new $callbackArr[0]();
//					$method = $callbackArr[1];
//					if(method_exists($controller,$method)){
//						$controller->$method();
//						echo json_encode($controller->response);
//					}
//				}
//			}
//		}
//	}
}