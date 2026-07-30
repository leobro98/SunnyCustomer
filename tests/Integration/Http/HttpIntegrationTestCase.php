<?php

declare(strict_types=1);

namespace Tests\Integration\Http;

use Leobro\SunnyCustomer\Application\Service\CustomerService;
use Leobro\SunnyCustomer\Infrastructure\Controller\CustomerController;
use Leobro\SunnyCustomer\Infrastructure\Http\Request;
use Leobro\SunnyCustomer\Infrastructure\Http\Response;
use Tests\Integration\IntegrationTestCase;

/**
 * Serves a base for all integration HTTP tests.
 */
abstract class HttpIntegrationTestCase extends IntegrationTestCase {

	protected CustomerController $customerController;

	protected function setUp(): void {
		parent::setUp();

		$customerService = new CustomerService($this->customerRepository);
		$this->customerController = new CustomerController($customerService);
	}

	/**
	 * Creates POST /customers request where parameters with null value are excluded.
	 */
	protected function createPostRequest(
			?string $firstName = 'John',
			?string $lastName = 'Doe',
			?string $birthDate = '1990-01-01',
			?string $userName = 'jdoe',
			?string $plainPassword = 'secret',
	): Request {
		// HTTP request fields (CustomerMapper expects snake_case)
		$parameters = array_filter(
				[
						'first_name' => $firstName,
						'last_name' => $lastName,
						'birth_date' => $birthDate,
						'user_name' => $userName,
						'password' => $plainPassword,
				],
				static fn ($value) => $value !== null
		);

		return new Request(
				method: 'POST',
				path: '/customers',
				query: [],
				post: $parameters,
		);
	}

	protected function decodeJson(string $responseBody): array {
		self::assertJson($responseBody);
		return json_decode($responseBody, true, flags: JSON_THROW_ON_ERROR);
	}
}
