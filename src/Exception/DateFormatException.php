<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Exception;

use RuntimeException;

/**
 * Thrown when a date request parameter string can not be converted to a date.
 */
final class DateFormatException extends RuntimeException {


	public function __construct(string $dateString) {
		parent::__construct(
			sprintf('Date ["%s"] has an invalid format. Expected format: Y-m-d.', $dateString)
		);
	}
}
