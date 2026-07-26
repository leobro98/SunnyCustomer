<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Domain\Repository;

use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;

/**
 * Contract for all actions with customer.
 */
interface CustomerRepository {

	/**
	 * Find a customer by their ID (technical identifier).
	 * @param int $id customer technical identifier.
	 */
	public function findById(int $id): ?Customer;

	/**
	 * Finds a customer by their user name (unique across the database) if such exists.
	 * @param string $userName user name of the customer.
	 * @return Customer|null the customer object from the database or null if not found.
	 */
	public function findByUserName(string $userName): ?Customer;

	/**
	 * Fetches a customer by their user name (unique across the database).
	 * @param string $userName user name of the customer.
	 * @return Customer the customer object found in the database.
	 */
	public function getByUserName(string $userName): Customer;

	/**
	 * Inserts a new customer.
	 * @param NewCustomer $newCustomer customer data for the new customer (no id and created_at fields).
	 * @param string $passwordHash hashed customer password.
	 * @return void
	 */
	public function insert(NewCustomer $newCustomer, string $passwordHash): void;
}
