<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Application\Service;

use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\Repository\CustomerRepository;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Domain\ValueObject\UpdatedCustomer;
use Leobro\SunnyCustomer\Exception\UserAlreadyExistsException;

/**
 * Contains business logic related to customer.
 */
final readonly class CustomerService {

	public function __construct(
		private CustomerRepository $repository
	) {
	}

	/**
	 * Creates a new customer on the base of passed data.
	 * @param NewCustomer $newCustomer data for customer creation.
	 * @return Customer customer object created.
	 */
	public function createCustomer(NewCustomer $newCustomer): Customer {
		if ($this->repository->findByUserName($newCustomer->userName) !== null) {
			throw new UserAlreadyExistsException($newCustomer->userName);
		}

		$passwordHash = password_hash($newCustomer->plainPassword, PASSWORD_DEFAULT);

		$this->repository->insert($newCustomer, $passwordHash);

		return $this->repository->getByUserName($newCustomer->userName);
	}

	/**
	 * Fetches all customers from the database.
	 *
	 * @return list<Customer> all customers in the database.
	 */
	public function getAllCustomers(): array {
		return $this->repository->findAll();
	}

	/**
	 * Updates existing customer. If the request does not contain changes compared to the existing customer,
	 * update is not performed.
	 *
	 * @param UpdatedCustomer $updatedCustomer data to update for the existing customer, contains also customer ID.
	 * @return void
	 */
	public function updateCustomer(UpdatedCustomer $updatedCustomer): void {
		$this->repository->update($updatedCustomer);
	}

	/**
	 * Deletes existing customer specified by its ID.
	 *
	 * @param int $id technical ID of the customer.
	 * @return void
	 */
	public function delete(int $id): void {
		$this->repository->delete($id);
	}
}
