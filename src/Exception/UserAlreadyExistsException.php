<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Exception;

use RuntimeException;

/**
 * Signals that the user can not be created as their user name is not unique.
 */
final class UserAlreadyExistsException extends RuntimeException
{

	/**
	 * @param string $userName name of the existing user.
	 */
	public function __construct(string $userName) {
		parent::__construct(
			sprintf('User "%s" already exists.', $userName)
		);
	}
}
