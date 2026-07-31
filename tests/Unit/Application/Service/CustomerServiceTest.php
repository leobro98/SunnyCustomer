<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Service;

use DateTimeImmutable;
use Leobro\SunnyCustomer\Application\Service\CustomerService;
use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\Repository\CustomerRepository;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Domain\ValueObject\UpdatedCustomer;
use Leobro\SunnyCustomer\Exception\CustomerNotFoundException;
use Leobro\SunnyCustomer\Exception\UserAlreadyExistsException;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@link CustomerService} class.
 */
final class CustomerServiceTest extends TestCase {

	private $repository;
	private $service;

	protected function setUp() :void {
		parent::setUp();

		$this->repository = $this->createMock(CustomerRepository::class);
		$this->service = new CustomerService($this->repository);
	}

	public function testCreateCustomer_whenUserAlreadyExists_thenThrowsException(): void {
		// Arrange
		$newCustomer = $this->createNewCustomer(firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$existingCustomer = $this->createCustomer(id: 1, firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$this->repository
				->expects($this->once())
				->method('findByUserName')
				->with($newCustomer->userName)
				->willReturn($existingCustomer);
		$this->expectException(UserAlreadyExistsException::class);

		// Act
		$this->service->createCustomer($newCustomer);
	}

	public function testCreateCustomer_whenUserDoesNotExist_thenCreatesCustomer(): void {
		// Arrange
		$newCustomer = $this->createNewCustomer(firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$createdCustomer = $this->createCustomer(id: 1, firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$this->repository
				->expects($this->once())
				->method('findByUserName')
				->with($newCustomer->userName)
				->willReturn(null);
		$this->repository
				->expects($this->once())
				->method('insert')
				->with($newCustomer,
						$this->callback(
								fn(string $passwordHash): bool =>
								password_verify($newCustomer->plainPassword, $passwordHash)
						)
				);
		$this->repository
				->expects($this->once())
				->method('getByUserName')
				->with($newCustomer->userName)
				->willReturn($createdCustomer);

		// Act
		$customer = $this->service->createCustomer($newCustomer);

		// Assert
		$this->assertSame($createdCustomer, $customer);
	}

	public function testGetAllCustomers_returnsRepositoryResult(): void {
		// Arrange
		$customers = [
				$this->createCustomer(
						id: 1,
						firstName: 'John',
						lastName: 'Doe',
						userName: 'jdoe'
				),
				$this->createCustomer(
						id: 2,
						firstName: 'Jane',
						lastName: 'Smith',
						userName: 'jsmith'
				),
		];

		$this->repository
				->expects(self::once())
				->method('findAll')
				->willReturn($customers);

		// Act
		$result = $this->service->getAllCustomers();

		// Assert
		self::assertSame($customers, $result);
	}

	public function testDelete_whenCustomerExists_thenDeletesCustomer(): void {
		// Arrange
		$customer = new UpdatedCustomer(
				id: 17,
				firstName: 'John',
				lastName: 'Doe',
				birthDate: new DateTimeImmutable('1990-01-01'),
				userName: 'jdoe'
		);
		$this->repository
				->expects(self::once())
				->method('delete')
				->with(17);

		// Act
		$this->service->delete($customer->id);
	}

	public function testDelete_whenRepositoryThrowsException_thenRethrowsException(): void {
		// Arrange
		$this->repository
				->expects(self::once())
				->method('delete')
				->willThrowException(CustomerNotFoundException::byId(17));
		$this->expectException(CustomerNotFoundException::class);

		// Act
		$this->service->delete(17);
	}

	private function createNewCustomer(string $firstName, string $lastName, string $userName): NewCustomer {
		return new NewCustomer(
				firstName: $firstName,
				lastName: $lastName,
				birthDate: new DateTimeImmutable('1990-01-01'),
				userName: $userName,
				plainPassword: 'secret');
	}

	private function createCustomer(int $id, string $firstName, string $lastName, string $userName): Customer {
		return new Customer(
				id: $id,
				firstName: $firstName,
				lastName: $lastName,
				birthDate: new DateTimeImmutable('1990-01-01'),
				userName: $userName,
				passwordHash: 'any_hash',
				createdAt: new DateTimeImmutable('2026-01-01T00:00:00+00:00'));
	}
}
