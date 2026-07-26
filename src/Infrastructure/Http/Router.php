<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Http;

use Leobro\SunnyCustomer\Exception\RouteAlreadyRegisteredException;
use Leobro\SunnyCustomer\Exception\RouteNotFoundException;

/**
 * Registers handlers for different HTTP requests and dispatches coming request to the handler.
 */
final class Router {
	/**
	 * @var array<string, array<string, callable>>
	 */
	private array $routes = [];

	/**
	 * Registers handlers for GET requests.
	 * @param string $path HTTP request path.
	 * @param callable $handler request handler.
	 * @return void
	 */
	public function registerGet(string $path, callable $handler): void {
		$this->register('GET', $path, $handler);
	}

	/**
	 * Registers handlers for POST requests.
	 * @param string $path HTTP request path.
	 * @param callable $handler request handler.
	 * @return void
	 */
	public function registerPost(string $path, callable $handler): void {
		$this->register('POST', $path, $handler);
	}

	/**
	 * Registers handlers for PUT requests.
	 * @param string $path HTTP request path.
	 * @param callable $handler request handler.
	 * @return void
	 */
	public function registerPut(string $path, callable $handler): void {
		$this->register('PUT', $path, $handler);
	}

	/**
	 * Registers handlers for DELETE requests.
	 * @param string $path HTTP request path.
	 * @param callable $handler request handler.
	 * @return void
	 */
	public function registerDelete(string $path, callable $handler): void {
		$this->register('DELETE', $path, $handler);
	}

	/**
	 * Dispatches a request to its handler.
	 * @param Request $request wrapper of HTTP request.
	 * @return Response result of the request execution.
	 */
	public function handle(Request $request): Response {
		$handler = $this->routes[$request->method][$request->path] ?? null;

		if ($handler === null) {
			throw new RouteNotFoundException($request->method, $request->path);
		}

		return $handler($request);
	}

	private function register(string $method, string $path, callable $handler): void {
		if (isset($this->routes[$method][$path])) {
			throw new RouteAlreadyRegisteredException($method, $path);
		}

		$this->routes[$method][$path] = $handler;
	}
}
