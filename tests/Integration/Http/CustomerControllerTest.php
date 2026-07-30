<?php

namespace Tests\Integration\Http;

use DateTimeImmutable;
use JsonException;
use Leobro\SunnyCustomer\Infrastructure\Controller\CustomerController;

/**
 * Tests {@link CustomerController} class.
 */
class CustomerControllerTest extends HttpIntegrationTestCase {

	/**
	 * @throws JsonException
	 */
	public function testCreateCustomer_whenRequestIsValid_thenReturnsCreated(): void {
		// Arrange
		$request = $this->createPostRequest();

		// Act
		$response = $this->customerController->createCustomer($request);

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
}
