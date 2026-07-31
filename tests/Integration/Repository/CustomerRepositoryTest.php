<?php

namespace Tests\Integration\Repository;

use DateTimeImmutable;
use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\Repository\CustomerRepository;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Domain\ValueObject\UpdatedCustomer;
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

	public function testFindByUserName_whenCustomerExists_thenReturnsCustomer(): void {
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

	public function testGetByUserName_whenCustomerExists_thenReturnsCustomer(): void {
		// Arrange
		$newCustomer = $this->createNewCustomer();
		$this->insertCustomer($newCustomer);

		// Act
		$customer = $this->customerRepository->getByUserName($newCustomer->userName);

		// Assert
		self::assertNotNull($customer);
		$this->assertCustomerEquals($newCustomer, $customer);
	}

	public function testFindById_whenCustomerExists_thenReturnsCustomer(): void {
		// Arrange
		$newCustomer = $this->createNewCustomer();
		$passwordHash = password_hash($newCustomer->plainPassword, PASSWORD_DEFAULT);
		$this->customerRepository->insert($newCustomer, $passwordHash);

		$insertedCustomer = $this->customerRepository->findByUserName($newCustomer->userName);

		// Act
		$customer = $this->customerRepository->findById($insertedCustomer->id);

		// Assert
		self::assertNotNull($customer);
		self::assertSame($insertedCustomer->id, $customer->id);
	}

	public function testFindById_whenCustomerDoesNotExist_thenReturnsNull(): void {
		// Act
		$customer = $this->customerRepository->findById(0);

		// Assert
		self::assertNull($customer);
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

	public function testFindAll_whenDatabaseIsEmpty_thenReturnsEmptyArray(): void {
		// Act
		$customers = $this->customerRepository->findAll();

		// Assert
		self::assertSame([], $customers);
	}

	public function testFindAll_whenCustomersExist_thenReturnsCustomersOrderedById(): void {
		// Arrange
		$john = $this->createNewCustomer();
		$jane = $this->createNewCustomer(
				firstName: 'Jane',
				lastName: 'Smith',
				userName: 'jsmith'
		);
		$this->customerRepository->insert($john, password_hash($john->plainPassword, PASSWORD_DEFAULT));
		$this->customerRepository->insert($jane, password_hash($jane->plainPassword, PASSWORD_DEFAULT));

		// Act
		$customers = $this->customerRepository->findAll();

		// Assert
		self::assertCount(2, $customers);

		$this->assertCustomerEquals($john, $customers[0]);
		$this->assertCustomerEquals($jane, $customers[1]);

		$this->assertIdsAscending($customers);
	}

	public function testUpdate_whenCustomerExists_thenCustomerIsUpdated(): void {
		// Arrange
		$newOriginal = $this->createNewCustomer();
		$this->customerRepository->insert($newOriginal, 'hash');
		$original = $this->customerRepository->findByUserName('jdoe');

		self::assertNotNull($original);

		$toBeUpdated = new UpdatedCustomer(
				id: $original->id,
				firstName: 'Johnny',
				lastName: 'Doe Jr.',
				birthDate: new DateTimeImmutable('1992-02-02'),
				userName: 'johnny'
		);

		// Act
		$this->customerRepository->update($toBeUpdated);

		// Assert
		$updated = $this->customerRepository->findByUserName('johnny');

		self::assertNotNull($updated);

		self::assertSame($original->id, $updated->id);
		self::assertSame('Johnny', $updated->firstName);
		self::assertSame('Doe Jr.', $updated->lastName);
		self::assertEquals(
				new DateTimeImmutable('1992-02-02'),
				$updated->birthDate
		);
		self::assertSame('johnny', $updated->userName);

		// These fields should not be updated
		self::assertSame($original->passwordHash, $updated->passwordHash);
		self::assertEquals($original->createdAt, $updated->createdAt);
	}

	public function testUpdate_whenCustomerDoesNotExist_thenThrowsCustomerNotFoundException(): void {
		// Arrange
		$updatedCustomer = new UpdatedCustomer(
				id: 999999,
				firstName: 'John',
				lastName: 'Doe',
				birthDate: new DateTimeImmutable('1990-01-01'),
				userName: 'jdoe',
		);

		$this->expectException(CustomerNotFoundException::class);

		// Act
		$this->customerRepository->update($updatedCustomer);
	}

	public function testDelete_whenCustomerExists_thenCustomerIsRemoved(): void {
		// Arrange
		$newCustomer = $this->createNewCustomer();
		$this->customerRepository->insert($newCustomer, 'passwordHash');
		$customer = $this->customerRepository->findByUserName('jdoe');
		self::assertNotNull($customer);

		// Act
		$this->customerRepository->delete($customer->id);

		// Assert
		self::assertNull(
				$this->customerRepository->findById($customer->id)
		);
		self::assertNull(
				$this->customerRepository->findByUserName('jdoe')
		);
	}

	public function testDelete_whenCustomerDoesNotExist_thenThrowsCustomerNotFoundException(): void {
		// Arrange
		$this->expectException(CustomerNotFoundException::class);

		// Act
		$this->customerRepository->delete(999999);
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
