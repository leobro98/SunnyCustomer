<?php

namespace Tests\Integration\Repository;

use DateTimeImmutable;
use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\Repository\CustomerRepository;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Exception\CustomerNotFoundException;
use Leobro\SunnyCustomer\Infrastructure\Persistence\PdoCustomerRepository;
use PDOException;
use Tests\Integration\IntegrationTestCase;

/**
 * Tests that database is created and requests work correctly.
 */
class CustomerRepositoryTest extends IntegrationTestCase {

	public function testFindByUserName_whenCustomerDoesNotExist_thenReturnsNull(): void {
		// Act
		$customer = $this->customerRepository->findByUserName('jdoe');

		// Assert
		self::assertNull($customer);
	}

	public function testFindByUserName_whenCustomerExists_thenReturnsCustomer() {
		// Arrange
		$newCustomer = $this->createNewCustomer();
		$passwordHash = password_hash($newCustomer->plainPassword, PASSWORD_DEFAULT);
		$this->customerRepository->insert($newCustomer, $passwordHash);

		// Act
		$customer = $this->customerRepository->findByUserName($newCustomer->userName);

		// Assert
		self::assertNotNull($customer);
		self::assertSame($newCustomer->userName, $customer->userName);
		self::assertGreaterThan(0, $customer->id);
	}

	public function testGetByUserName_whenCustomerDoesNotExist_thenThrowsException(): void {
		// Arrange
		$this->expectException(CustomerNotFoundException::class);

		// Act
		$this->customerRepository->getByUserName('jdoe');
	}

	public function testGetByUserName_whenCustomerExists_thenReturnsCustomer() {
		// Arrange
		$newCustomer = $this->createNewCustomer();
		$this->insertCustomer($newCustomer);

		// Act
		$customer = $this->customerRepository->getByUserName($newCustomer->userName);

		// Assert
		self::assertNotNull($customer);
		$this->assertCustomerEquals($newCustomer, $customer);
	}

	public function testInsert_whenCustomerDoesNotExist_thenCustomerIsStored(): void {
		// Arrange
		$newCustomer = $this->createNewCustomer();
		$passwordHash = password_hash($newCustomer->plainPassword, PASSWORD_DEFAULT);

		// Act
		$this->customerRepository->insert($newCustomer, $passwordHash);
		$customer = $this->customerRepository->getByUserName($newCustomer->userName);

		// Assert
		$this->assertCustomerEquals($newCustomer, $customer);
	}

	public function testInsert_whenUserAlreadyExists_thenThrowsException(): void {
		// Arrange
		$firstCustomer = $this->createNewCustomer(userName: 'jdoe');
		$secondCustomer = $this->createNewCustomer(
				firstName: 'Jane',
				lastName: 'Smith',
				userName: 'jdoe',
		);
		$this->insertCustomer($firstCustomer);

		$this->expectException(PDOException::class);

		// Act
		$this->insertCustomer($secondCustomer);
	}

	private function createNewCustomer(string $firstName = 'John',
	                                   string $lastName = 'Doe',
	                                   string $userName = 'jdoe',
	                                   string $birthDate = '1990-01-01'
	): NewCustomer {
		return new NewCustomer(
				firstName: $firstName,
				lastName: $lastName,
				birthDate: DateTimeImmutable::createFromFormat('!Y-m-d', $birthDate),
				userName: $userName,
				plainPassword: 'secret'
		);
	}

	private function insertCustomer(NewCustomer $customer): void {
		$this->customerRepository->insert(
				$customer,
				password_hash($customer->plainPassword, PASSWORD_DEFAULT)
		);
	}

	private function assertCustomerEquals(NewCustomer $expected, Customer $actual): void {
		self::assertSame($expected->firstName, $actual->firstName);
		self::assertSame($expected->lastName, $actual->lastName);
		self::assertEquals($expected->birthDate, $actual->birthDate);
		self::assertSame($expected->userName, $actual->userName);

		self::assertTrue(
				password_verify($expected->plainPassword, $actual->passwordHash)
		);
		self::assertGreaterThan(0, $actual->id);
		self::assertInstanceOf(DateTimeImmutable::class, $actual->createdAt);
	}
}
