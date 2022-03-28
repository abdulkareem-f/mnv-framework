<?php

use Twig\Loader\FilesystemLoader;
use Twig\Environment;


if (! function_exists('view')) {
	function view(string $viewPath, array $params=[]): string {
		try {
			$views = __DIR__ . '/../../resources/views';
			
			$loader = new FilesystemLoader($views);
			$twig = new Environment($loader, []);
			
			if (!ends_with($viewPath, '.twig') && !ends_with($viewPath, '.html.twig')) {
				$viewPath = file_exists("$views/$viewPath.twig") ? "$viewPath.twig" : "$viewPath..html.twig";
			}
			if (!file_exists("$views/$viewPath")) {
				throw new Exception("[$views/$viewPath] file is not exist");
			}
			$params['baseUrl'] = url();
			
			return $twig->render($viewPath, $params);
		} catch (Exception $e){
			return $e->getMessage();
		}
	}
}

if (! function_exists('env')) {
	function env($key, $default = null): mixed {
		return $_ENV[$key] ?? $default;
	}
}

if (! function_exists('mm')) {
	function mm(...$data): void {
		print_r($data);exit(1);
	}
}

if (! function_exists('ends_with')) {
	function ends_with($string, $endString): bool{
		$len = strlen($endString);
		if ($len == 0) {
			return true;
		}
		return (substr($string, -$len) === $endString);
	}
}

if (! function_exists('url')) {
	function url(string $path=''): string {
		$pathToPublic = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$baseUrl = "https://" . $_SERVER['HTTP_HOST'] . "$pathToPublic";
		
		return $baseUrl . $path;
	}
}

if (! function_exists('redirect')) {
	function redirect(string $url): void {
		header("Location: $url");
	}
}

if (! function_exists('set_response_code')) {
	function set_response_code(int $code): void {
		http_response_code($code);
	}
}