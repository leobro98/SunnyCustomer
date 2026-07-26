<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Exception;

use RuntimeException;

/**
 * Signals that an attempt was made to register a route that is already registered.
 */
final class RouteAlreadyRegisteredException extends RuntimeException {

	/**
	 * @param string $method HTTP request method.
	 * @param string $path request path.
	 */
	public function __construct(string $method, string $path) {
		parent::__construct(
			sprintf('Route %s %s is already registered.', $method, $path)
		);
	}
}