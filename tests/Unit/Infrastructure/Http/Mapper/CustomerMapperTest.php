<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Http\Mapper;

use DateTimeImmutable;
use Leobro\SunnyCustomer\Exception\DateFormatException;
use Leobro\SunnyCustomer\Exception\MissingRequestParameterException;
use Leobro\SunnyCustomer\Infrastructure\Http\Mapper\CustomerMapper;
use Leobro\SunnyCustomer\Infrastructure\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@link CustomerMapper} class.
 */
final class CustomerMapperTest extends TestCase {

	public function testFromRequest_whenRequestIsValid_thenReturnsNewCustomer(): void {
		// Arrange
		$request = $this->createPostRequest();

		// Act
		$customer = CustomerMapper::fromCreateRequest($request);

		// Assert
		$this->assertSame('John', $customer->firstName);
		$this->assertSame('Doe', $customer->lastName);
		$this->assertEquals(new DateTimeImmutable('1990-01-01'), $customer->birthDate);
		$this->assertSame('jdoe', $customer->userName);
		$this->assertSame('secret', $customer->plainPassword);
	}

	public function testFromRequest_whenRequiredParameterIsMissing_thenThrowsMissingRequiredParameterException(): void {
		// Arrange
		$request = $this->createPostRequest(['password' => null]);
		$this->expectException(MissingRequestParameterException::class);

		// Act
		CustomerMapper::fromCreateRequest($request);
	}

	public function testFromRequest_whenBirthDateHasInvalidFormat_thenThrowsDateFormatException(): void {
		// Arrange
		$request = $this->createPostRequest(['birth_date' => 'abc']);
		$this->expectException(DateFormatException::class);

		// Act
		CustomerMapper::fromCreateRequest($request);
	}

	public function testFromUpdateRequest_whenRequestIsValid_thenReturnsUpdatedCustomer(): void {
		// Arrange
		$request = $this->createPutRequest(['id' => '17']);

		// Act
		$customer = CustomerMapper::fromUpdateRequest($request);

		// Assert
		self::assertSame(17, $customer->id);
		self::assertSame('John', $customer->firstName);
		self::assertSame('Doe', $customer->lastName);
		self::assertEquals(
				new DateTimeImmutable('1990-01-01'),
				$customer->birthDate
		);
		self::assertSame('jdoe', $customer->userName);
	}

	/**
	 * Creates a valid POST /customers request.
	 * Parameters whose value is null are omitted from the request.
	 */
	private function createPostRequest(array $parameters = []): Request {
		$defaultParameters = [
				'first_name' => 'John',
				'last_name' => 'Doe',
				'birth_date' => '1990-01-01',
				'user_name' => 'jdoe',
				'password' => 'secret',
		];

		$defaultParameters = $this->replacePassedParameters($defaultParameters, $parameters);

		return new Request(
				method: 'POST',
				path: '/customers',
				query: [],
				post: $defaultParameters,
		);
	}

	private function createPutRequest(array $parameters = []): Request {
		$defaultParameters = [
				'first_name' => 'John',
				'last_name' => 'Doe',
				'birth_date' => '1990-01-01',
				'user_name' => 'jdoe',
		];

		$defaultParameters = $this->replacePassedParameters($defaultParameters, $parameters);

		return new Request(
				method: 'PUT',
				path: '/customers',
				query: ['id' => $parameters['id']],
				post: $defaultParameters
		);
	}

	/**
	 * If null is passed for a parameter, corresponding default parameter is removed.
	 */
	private function replacePassedParameters(array $defaultParameters, array $parameters): array {
		foreach ($parameters as $name => $value) {
			if ($value === null) {
				unset($defaultParameters[$name]);
			} else {
				$defaultParameters[$name] = $value;
			}
		}

		return $defaultParameters;
	}
}
