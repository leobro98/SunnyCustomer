<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Exception;

use RuntimeException;

/**
 * Signals that the customer which is expected to be in the database is not found.
 */
final class CustomerNotFoundException extends RuntimeException {

	/**
	 * @param string $userName user name of the not found customer.
	 */
	public function __construct(string $userName) {
		parent::__construct(
			sprintf('Customer "%s" not found.', $userName)
		);
	}

	/**
	 * Creates an instance of the exception with message about ID.
	 *
	 * @param int $id ID of the customer which is not found.
	 * @return self
	 */
	public static function byId(int $id): self {
		return new self("Customer with ID {$id} not found.");
	}

	/**
	 * Creates an instance of the exception with message about customer username.
	 *
	 * @param string $userName user name of the customer which is not found.
	 * @return self
	 */
	public static function byUserName(string $userName): self {
		return new self("Customer with user name '{$userName}' not found.");
	}
}
