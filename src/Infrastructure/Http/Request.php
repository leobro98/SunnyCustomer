<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Http;

use Leobro\SunnyCustomer\Exception\MissingRequestParameterException;

/**
 * Parses HTTP request into its properties.
 */
final readonly class Request {

	public function __construct(
		public string $method,
		public string $path,
		public array $query,
		public array $post
	) {
	}

	/**
	 * Parses components of HTTP request containing in global PHP variables.
	 *
	 * @return self entity of this class.
	 */
	public static function fromGlobals(): self {
		$query = $_GET;

		$path = self::extractPath($_SERVER['REQUEST_URI']);
		$segments = explode('/', trim($path, '/'));
		$last = array_key_last($segments);

		// last segment in the path will be treated as some object's ID
		if ($last !== null && ctype_digit($segments[$last])) {
			$query['id'] = $segments[$last];
			unset($segments[$last]);

			$path = '/' . implode('/', $segments);
		}

		return new self(
				method: strtoupper($_SERVER['REQUEST_METHOD']),
				path: $path,
				query: $query,
				post: $_POST
		);
	}

	private static function extractPath($uri): string {
		$path = parse_url($uri, PHP_URL_PATH);
		return is_string($path) ? $path : '/';
	}

	/**
	 * Retrieves a post parameter from the request and throws {@link MissingRequestParameterException}
	 * if the parameter is not found.
	 *
	 * @param string $name parameter name.
	 * @return string value of the parameter.
	 * @throws MissingRequestParameterException if the parameter is not found in the request.
	 */
	public function requirePostParameter(string $name): string {
		if (!isset($this->post[$name])) {
			throw new MissingRequestParameterException($name);
		}
		return $this->post[$name];
	}

	/**
	 * Retrieves a get or path parameter from the request and throws {@link MissingRequestParameterException}
	 * if the parameter is not found.
	 *
	 * @param string $name parameter name.
	 * @return string value of the parameter.
	 * @throws MissingRequestParameterException if the parameter is not found in the request.
	 */
	public function requireQueryParameter(string $name): string {
		if (!isset($this->query[$name])) {
			throw new MissingRequestParameterException($name);
		}
		return $this->query[$name];
	}
}
