<?php

declare(strict_types=1);

namespace Tests\Integration\Http;

use JsonException;
use Leobro\SunnyCustomer\Application\Service\CustomerService;
use Leobro\SunnyCustomer\Infrastructure\Bootstrap\Bootstrap;
use Leobro\SunnyCustomer\Infrastructure\Controller\CustomerController;
use Leobro\SunnyCustomer\Infrastructure\Http\Request;
use Leobro\SunnyCustomer\Infrastructure\Http\Response;
use Leobro\SunnyCustomer\Infrastructure\Http\Router;
use Tests\Integration\IntegrationTestCase;

/**
 * Serves a base for all integration HTTP tests.
 */
abstract class HttpIntegrationTestCase extends IntegrationTestCase {

	protected CustomerController $customerController;
	protected Router $router;

	protected function setUp(): void {
		parent::setUp();

		$config = require dirname(__DIR__, 3) . '/config/database.test.php';
		$bootstrap = new Bootstrap($config);
		$this->router = $bootstrap->createRouter();
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

	/**
	 * Creates PUT /customers/{id} request where parameters with null value are excluded.
	 */
	protected function createPutRequest(
			int $id,
			string $firstName,
			string $lastName,
			string $birthDate,
			string $userName
	): Request {
		// HTTP request fields (CustomerMapper expects snake_case)
		$parameters = array_filter(
				[
						'first_name' => $firstName,
						'last_name' => $lastName,
						'birth_date' => $birthDate,
						'user_name' => $userName
				],
				static fn ($value) => $value !== null
		);

		return new Request(
				method: 'PUT',
				path: '/customers',
				query: ['id' => (string) $id],
				post: $parameters
		);
	}

	protected function createGetRequest(string $path): Request {
		return new Request(
				method: 'GET',
				path: $path,
				query: [],
				post: []
		);
	}

	protected function createDeleteRequest(int $id): Request {
		return new Request(
				method: 'DELETE',
				path: '/customers',
				query: ['id' => (string) $id],
				post: []
		);
	}

	/**
	 * @throws JsonException
	 */
	protected function decodeJson(string $responseBody): array {
		self::assertJson($responseBody);
		return json_decode($responseBody, true, flags: JSON_THROW_ON_ERROR);
	}

	/**
	 * @throws JsonException
	 */
	protected function send(Request $request): Response {
		return $this->router->handle($request);
	}
}
