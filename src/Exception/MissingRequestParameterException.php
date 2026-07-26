<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Exception;

use RuntimeException;

/**
 * Shows that required request parameter is missing.
 */
final class MissingRequestParameterException extends RuntimeException {

	/**
	 * @param string $parameterName name of the missing parameter.
	 */
	public function __construct(string $parameterName) {
		parent::__construct(
			sprintf('Required request parameter "%s" is missing.', $parameterName)
		);
	}
}
