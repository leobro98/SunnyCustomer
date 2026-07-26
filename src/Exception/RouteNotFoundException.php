<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Exception;

use RuntimeException;

/**
 * Signals that a request was received with a non-registered route.
 */
final class RouteNotFoundException extends RuntimeException {

	/**
	 * @param string $method HTTP request method.
	 * @param string $path request path.
	 */
	public function __construct(string $method, string $path) {
		parent::__construct(
			sprintf('No route registered for %s %s.', $method, $path,)
		);
	}
}
