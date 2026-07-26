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
}
