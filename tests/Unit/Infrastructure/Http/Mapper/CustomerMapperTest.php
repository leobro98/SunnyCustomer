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
		$request = $this->createRequest();

		// Act
		$customer = CustomerMapper::fromRequest($request);

		// Assert
		$this->assertSame('John', $customer->firstName);
		$this->assertSame('Doe', $customer->lastName);
		$this->assertEquals(new DateTimeImmutable('1990-01-01'), $customer->birthDate);
		$this->assertSame('jdoe', $customer->userName);
		$this->assertSame('secret', $customer->plainPassword);
	}

	public function testFromRequest_whenRequiredParameterIsMissing_thenThrowsMissingRequiredParameterException(): void {
		// Arrange
		$request = $this->createRequest(['password' => null]);
		$this->expectException(MissingRequestParameterException::class);

		// Act
		CustomerMapper::fromRequest($request);
	}

	public function testFromRequest_whenBirthDateHasInvalidFormat_thenThrowsDateFormatException(): void {
		// Arrange
		$request = $this->createRequest(['birth_date' => 'abc']);
		$this->expectException(DateFormatException::class);

		// Act
		CustomerMapper::fromRequest($request);
	}

	/**
	 * Creates a valid POST /customers request.
	 * Parameters whose value is null are omitted from the request.
	 */
	private function createRequest(array $parameters = []): Request {
		$defaultParameters = [
				'first_name' => 'John',
				'last_name' => 'Doe',
				'birth_date' => '1990-01-01',
				'user_name' => 'jdoe',
				'password' => 'secret',
		];

		foreach ($parameters as $name => $value) {
			if ($value === null) {
				unset($defaultParameters[$name]);
			} else {
				$defaultParameters[$name] = $value;
			}
		}

		return new Request(
				method: 'POST',
				path: '/customers',
				query: [],
				post: $defaultParameters,
		);
	}
}
