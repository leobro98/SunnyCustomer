<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Domain\ValueObject;

use DateTimeImmutable;
use Leobro\SunnyCustomer\Domain\Entity\Customer;

/**
 * Data needed for modification of a customer. In comparison with {@link Customer},
 * does not contain passwordHash and createdAt.
 */
final readonly class UpdatedCustomer {

	public function __construct(
			public int               $id,
			public string            $firstName,
			public string            $lastName,
			public DateTimeImmutable $birthDate,
			public string            $userName,
	) {
	}

	/**
	 * Compares fields which can be updated with those of passed customer and returns true if there are differences.
	 *
	 * @param Customer $customer customer whose fields are used for the comparison.
	 * @return bool if updateable fields are differ.
	 */
	public function hasChangesComparedTo(Customer $customer): bool {
		return $this->firstName !== $customer->firstName
				|| $this->lastName !== $customer->lastName
				|| !$this->areEqual($this->birthDate, $customer->birthDate)
				|| $this->userName !== $customer->userName;
	}

	private function areEqual(DateTimeImmutable $date, DateTimeImmutable $otherDate): bool {
		return $date->format('Y-m-d') === $otherDate->format('Y-m-d');
	}
}
