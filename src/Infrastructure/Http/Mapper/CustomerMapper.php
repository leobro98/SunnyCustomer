<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Http\Mapper;

use DateTimeImmutable;
use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\ValueObject\NewCustomer;
use Leobro\SunnyCustomer\Domain\ValueObject\UpdatedCustomer;
use Leobro\SunnyCustomer\Exception\DateFormatException;
use Leobro\SunnyCustomer\Exception\MissingRequestParameterException;
use Leobro\SunnyCustomer\Infrastructure\Http\Request;

/**
 * Maps {@link Customer} from input and to output.
 */
final class CustomerMapper {

	/**
	 * Prevents creation of the class instance as all its members are static.
	 */
	private function __construct() {
	}

	/**
	 * Composes {@link NewCustomer} object from HTTP request.
	 *
	 * @param Request $request POST request wrapper.
	 * @return NewCustomer object representation of the request data.
	 * @throws DateFormatException if date in the request is malformed.
	 * @throws MissingRequestParameterException if one of the required parameters is missing.
	 */
	public static function fromCreateRequest(Request $request): NewCustomer {
		$userName = trim(
				$request->requirePostParameter('user_name')
		);

		if ($userName === '') {
			throw new MissingRequestParameterException('user_name');
		}

		return new NewCustomer(
			firstName: $request->requirePostParameter('first_name'),
			lastName: $request->requirePostParameter('last_name'),
			birthDate: self::convertToDate($request->requirePostParameter('birth_date')),
			userName: $userName,
			plainPassword: $request->requirePostParameter('password')
		);
	}

	/**
	 * Composes {@link UpdatedCustomer} object from HTTP request.
	 *
	 * @param Request $request PUT request wrapper.
	 * @return UpdatedCustomer object representation of the request data.
	 * @throws DateFormatException if date in the request is malformed.
	 * @throws MissingRequestParameterException if one of the required parameters is missing.
	 */
	public static function fromUpdateRequest(Request $request): UpdatedCustomer {
		return new UpdatedCustomer(
				id: (int)$request->requireQueryParameter('id'),
				firstName: $request->requirePostParameter('first_name'),
				lastName: $request->requirePostParameter('last_name'),
				birthDate: self::convertToDate($request->requirePostParameter('birth_date')),
				userName: $request->requirePostParameter('user_name')
		);
	}

	private static function convertToDate(string $dateString): DateTimeImmutable {
		if (str_contains($dateString, "\0")) {
			throw new DateFormatException($dateString);
		}

		$date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateString);
		$errors = DateTimeImmutable::getLastErrors();

		if ($date === false
				|| $date->format('Y-m-d') !== $dateString
				|| ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
			throw new DateFormatException($dateString);
		}

		return $date;
	}

	/**
	 * Transforms the {@link Customer} object into a form suitable for response.
	 *
	 * @param Customer $customer customer domain object.
	 * @return array representation of the customer object properties for output purpose.
	 */
	public static function toArray(Customer $customer): array {
		return [
			'id' => $customer->id,
			'first_name' => $customer->firstName,
			'last_name' => $customer->lastName,
			'birth_date' => $customer->birthDate->format('Y-m-d'),
			'user_name' => $customer->userName,
			'created_at' => $customer->createdAt->format(DATE_ATOM)
		];
	}

	/**
	 * Transforms array of {@link Customer} objects into an array of maps suitable for response.
	 *
	 * @param list<Customer> $customers customer domain objects.
	 * @return list<array<string, mixed>> array of maps convenient for transforming into JSON.
	 */
	public static function toArrayList(array $customers): array {
		return array_map(
				self::toArray(...),
				$customers,
		);
	}
}
