<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Persistence;

use DateTimeImmutable;
use PDO;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\Repository\CustomerRepository;
use Leobro\SunnyCustomer\Exception\CustomerNotFoundException;

/**
 * Implementation of the {@implements CustomerRepository} contract with the usage of PDO.
 */
final readonly class PdoCustomerRepository implements CustomerRepository {

	public function __construct(
		private PDO $pdo,
	) {
	}

	public function findById(int $id): ?Customer {
		$statement = $this->pdo->prepare(
			'
        SELECT
            id,
            first_name,
            last_name,
            birth_date,
            user_name,
            password_hash,
            created_at
        FROM customer
        WHERE id = :id
        '
		);

		$statement->bindValue(':id', $id, PDO::PARAM_INT);
		$statement->execute();
		$row = $statement->fetch(PDO::FETCH_ASSOC);

		if ($row === false) {
			return null;
		}
		return $this->mapRowToCustomer($row);
	}

	public function findByUserName(string $userName): ?Customer {
		$statement = $this->pdo->prepare(
			'
        SELECT
            id,
            first_name,
            last_name,
            birth_date,
            user_name,
            password_hash,
            created_at
        FROM customer
        WHERE user_name = :user_name
        '
		);

		$statement->bindValue(':user_name', $userName, PDO::PARAM_STR);
		$statement->execute();
		$row = $statement->fetch(PDO::FETCH_ASSOC);

		if ($row === false) {
			return null;
		}
		return $this->mapRowToCustomer($row);
	}

	/**
	 * @param array<string, mixed> $row
	 */
	private function mapRowToCustomer(array $row): Customer {
		return new Customer(
			id: (int) $row['id'],
			firstName: (string) $row['first_name'],
			lastName: (string) $row['last_name'],
			birthDate: new DateTimeImmutable((string) $row['birth_date']),
			userName: (string) $row['user_name'],
			passwordHash: (string) $row['password_hash'],
			createdAt: new DateTimeImmutable((string) $row['created_at']),
		);
	}

	public function getByUserName(string $userName): Customer {
		$customer = $this->findByUserName($userName);

		if ($customer === null) {
			throw new CustomerNotFoundException($userName);
		}
		return $customer;
	}

	public function insert(NewCustomer $newCustomer, string $passwordHash): void {
		$statement = $this->pdo->prepare(
			'
        INSERT INTO customer (
            first_name,
            last_name,
            birth_date,
            user_name,
            password_hash
        )
        VALUES (
            :first_name,
            :last_name,
            :birth_date,
            :user_name,
            :password_hash
        )
        '
		);

		$statement->bindValue(':first_name', $newCustomer->firstName, PDO::PARAM_STR);
		$statement->bindValue(':last_name', $newCustomer->lastName, PDO::PARAM_STR);
		$statement->bindValue(':birth_date', $this->formatDate($newCustomer->birthDate), PDO::PARAM_STR);
		$statement->bindValue(':user_name', $newCustomer->userName, PDO::PARAM_STR);
		$statement->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);

		$statement->execute();
	}

	private function formatDate(DateTimeImmutable $birthDate): string {
		return $birthDate->format('Y-m-d');
	}
}
