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
	 * @return self entity of this class.
	 */
	public static function fromGlobals(): self {
		return new self(
			method: strtoupper($_SERVER['REQUEST_METHOD']),
			path: self::extractPath($_SERVER['REQUEST_URI']),
			query: $_GET,
			post: $_POST
		);
	}

	private static function extractPath($uri): string {
		$path = parse_url($uri, PHP_URL_PATH);
		return is_string($path) ? $path : '/';
	}

	public function requirePostParameter(string $name): string {
		if (!isset($this->post[$name])) {
			throw new MissingRequestParameterException($name);
		}

		return $this->post[$name];
	}
}
