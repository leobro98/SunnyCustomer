<?php

declare(strict_types=1);

namespace Tests\Integration\Http;

use DateTimeImmutable;
use JsonException;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Infrastructure\Http\Request;
use Leobro\SunnyCustomer\Infrastructure\Http\Response;

/**
 * Tests HTTP request processing through the whole application with all real dependencies. The chain is tested:
 * request - business logic - database - response.
 */
class RouterTest extends HttpIntegrationTestCase {

	/**
	 * @throws JsonException
	 */
	public function testCreateCustomer_whenRequestIsValid_thenReturnsCreated(): void {
		// Arrange
		$request = $this->createPostRequest();

		// Act
		$response = $this->send($request);

		// Assert
		self::assertSame(201, $response->getStatusCode());

		$body = $this->decodeJson($response->getBody());

		self::assertSame('John', $body['first_name']);
		self::assertSame('Doe', $body['last_name']);
		self::assertSame('jdoe', $body['user_name']);
		self::assertSame('1990-01-01', $body['birth_date']);

		$customer = $this->customerRepository->getByUserName('jdoe');

		self::assertSame('John', $customer->firstName);
		self::assertSame('Doe', $customer->lastName);
		self::assertSame('jdoe', $customer->userName);
		self::assertEquals(
				new DateTimeImmutable('1990-01-01'),
				$customer->birthDate
		);
		self::assertTrue(
				password_verify('secret', $customer->passwordHash)
		);
	}

	/**
	 * @throws JsonException
	 */
	public function testCreateCustomer_whenUserAlreadyExists_thenReturnsConflict(): void {
		// Arrange
		$this->customerRepository->insert(
				$this->createNewCustomer(),
				password_hash('secret', PASSWORD_DEFAULT),
		);

		$request = $this->createPostRequest(
				firstName: 'Jane',
				lastName: 'Smith',
				birthDate: '1991-02-02',
				userName: 'jdoe',
				plainPassword: 'anotherSecret'
		);

		// Act
		$response = $this->send($request);

		// Assert
		self::assertSame(409, $response->getStatusCode());
		$body = $this->decodeJson($response->getBody());
		self::assertArrayHasKey('message', $body);
	}

	/**
	 * @throws JsonException
	 */
	public function testCreateCustomer_whenBirthDateIsInvalid_thenReturnsBadRequest(): void {
		// Arrange
		$request = $this->createPostRequest(birthDate: '31.02.1990');

		// Act
		$response = $this->router->handle($request);

		// Assert
		self::assertSame(400, $response->getStatusCode());
		$body = $this->decodeJson($response->getBody());
		self::assertArrayHasKey('message', $body);
	}

	/**
	 * @throws JsonException
	 */
	public function testGetCustomers_whenCustomersExist_thenReturnsCustomersOrderedById(): void {
		// Arrange
		$john = $this->createNewCustomer();
		$jane = $this->createNewCustomer(
				firstName: 'Jane',
				lastName: 'Smith',
				userName: 'jsmith',
				birthDate: '1991-02-02'
		);
		$this->customerRepository->insert($john, 'hash1');
		$this->customerRepository->insert($jane, 'hash2');
		$request = $this->createGetRequest('/customers');

		// Act
		$response = $this->send($request);

		// Assert
		self::assertSame(200, $response->getStatusCode());

		$body = $this->decodeJson($response->getBody());

		self::assertCount(2, $body);

		self::assertArrayHasKey('id', $body[0]);
		self::assertIsInt($body[0]['id']);
		self::assertSame('John', $body[0]['first_name']);
		self::assertSame('Doe', $body[0]['last_name']);
		self::assertSame('jdoe', $body[0]['user_name']);
		self::assertSame('1990-01-01', $body[0]['birth_date']);

		self::assertArrayHasKey('id', $body[1]);
		self::assertIsInt($body[1]['id']);
		self::assertSame('Jane', $body[1]['first_name']);
		self::assertSame('Smith', $body[1]['last_name']);
		self::assertSame('jsmith', $body[1]['user_name']);
		self::assertSame('1991-02-02', $body[1]['birth_date']);

		self::assertRowsOrderedById($body);
	}

	/**
	 * @throws JsonException
	 */
	public function testUpdateCustomer_whenRequestIsValid_thenDatabaseContainsUpdated(): void {
		// Arrange
		$this->customerRepository->insert(
				$this->createNewCustomer(),
				'passwordHash'
		);
		$customer = $this->customerRepository->findByUserName('jdoe');
		self::assertNotNull($customer);

		$request = $this->createPutRequest(
				id: $customer->id,
				firstName: 'Johnny',
				lastName: 'Doe Jr.',
				birthDate: '1992-02-02',
				userName: 'johnny'
		);

		// Act
		$response = $this->send($request);

		// Assert
		self::assertSame(204, $response->getStatusCode());

		$updated = $this->customerRepository->findById($customer->id);

		self::assertNotNull($updated);

		self::assertSame('Johnny', $updated->firstName);
		self::assertSame('Doe Jr.', $updated->lastName);
		self::assertSame('johnny', $updated->userName);
		self::assertEquals(
				new DateTimeImmutable('1992-02-02'),
				$updated->birthDate
		);
	}

	public function testDeleteCustomer_whenCustomerExists_thenReturnsNoContent(): void {
		// Arrange
		$this->customerRepository->insert($this->createNewCustomer(), 'passwordHash');
		$customer = $this->customerRepository->findByUserName('jdoe');
		self::assertNotNull($customer);
		$request = $this->createDeleteRequest(id: $customer->id);

		// Act
		$response = $this->send($request);

		// Assert
		self::assertSame(204, $response->getStatusCode());
		self::assertNull(
				$this->customerRepository->findById($customer->id)
		);
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

	protected function assertRowsOrderedById(array $rows): void {
		$ids = array_column($rows, 'id');

		$sortedIds = $ids;
		sort($sortedIds);

		self::assertSame($sortedIds, $ids);
	}
}
