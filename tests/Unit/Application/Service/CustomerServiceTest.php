<?php

declare(strict_types=1);

namespace Application\Service;

use DateTimeImmutable;
use Leobro\SunnyCustomer\Application\Service\CustomerService;
use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\Repository\CustomerRepository;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Exception\UserAlreadyExistsException;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@link CustomerService} class.
 */
final class CustomerServiceTest extends TestCase {

	public function testCreateCustomer_whenUserAlreadyExists_thenThrowsException(): void {
		// Arrange
		$repository = $this->createMock(CustomerRepository::class);
		$customerService = new CustomerService($repository);

		$newCustomer = $this->createNewCustomer(firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$existingCustomer = $this->createCustomer(firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$repository
				->expects($this->once())
				->method('findByUserName')
				->with($newCustomer->userName)
				->willReturn($existingCustomer);
		$this->expectException(UserAlreadyExistsException::class);

		// Act
		$customerService->createCustomer($newCustomer);
	}

	public function testCreateCustomer_whenUserDoesNotExist_thenCreatesCustomer(): void {
		// Arrange
		$repository = $this->createMock(CustomerRepository::class);
		$customerService = new CustomerService($repository);

		$newCustomer = $this->createNewCustomer(firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$createdCustomer = $this->createCustomer(firstName: 'John', lastName: 'Doe', userName: 'jdoe');
		$repository
				->expects($this->once())
				->method('findByUserName')
				->with($newCustomer->userName)
				->willReturn(null);
		$repository
				->expects($this->once())
				->method('insert')
				->with($newCustomer,
						$this->callback(
								fn(string $passwordHash): bool =>
								password_verify($newCustomer->plainPassword, $passwordHash)
						)
				);
		$repository
				->expects($this->once())
				->method('getByUserName')
				->with($newCustomer->userName)
				->willReturn($createdCustomer);

		// Act
		$customer = $customerService->createCustomer($newCustomer);

		// Assert
		$this->assertSame($createdCustomer, $customer);
	}

	private function createNewCustomer(string $firstName, string $lastName, string $userName): NewCustomer {
		return new NewCustomer(
				firstName: $firstName,
				lastName: $lastName,
				birthDate: new DateTimeImmutable('1990-01-01'),
				userName: $userName,
				plainPassword: 'secret');
	}

	private function createCustomer(string $firstName, string $lastName, string $userName): Customer {
		return new Customer(
				id: 1,
				firstName: $firstName,
				lastName: $lastName,
				birthDate: new DateTimeImmutable('1990-01-01'),
				userName: $userName,
				passwordHash: 'any_hash',
				createdAt: new DateTimeImmutable('2026-01-01T00:00:00+00:00'));
	}
}
