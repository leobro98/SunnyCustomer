<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Domain\ValueObject;

use DateTimeImmutable;

/**
 * Data needed for creation of a customer. In comparison with {@link Customer}, does not contain id and createdAt.
 */
final readonly class NewCustomer {

	public function __construct(
		public string $firstName,
		public string $lastName,
		public DateTimeImmutable $birthDate,
		public string $userName,
		public string $plainPassword,
	) {
	}
}
