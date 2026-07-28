<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Domain\Entity;

use DateTimeImmutable;

final class Customer {
	public function __construct(
		public readonly int               $id,
		public readonly string            $firstName,
		public readonly string            $lastName,
		public readonly DateTimeImmutable $birthDate,
		public readonly string            $userName,
		public readonly string            $passwordHash,
		public readonly DateTimeImmutable $createdAt,
	) {
	}
}
