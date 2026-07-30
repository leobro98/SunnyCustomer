<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Http;

use JsonException;
use Leobro\SunnyCustomer\Exception\DateFormatException;
use Leobro\SunnyCustomer\Exception\CustomerNotFoundException;
use Leobro\SunnyCustomer\Exception\RouteNotFoundException;
use Leobro\SunnyCustomer\Exception\UserAlreadyExistsException;
use Leobro\SunnyCustomer\Exception\MissingRequestParameterException;
use Throwable;

/**
 * Translates internal project exceptions into JSON Response with corresponding HTTP codes.
 */
final class ExceptionHandler {

	/**
	 * Maps exception classes to HTTP status codes.
	 *
	 * @var array<class-string<Throwable>, int>
	 */
	private const EXCEPTION_STATUS_CODES = [
			MissingRequestParameterException::class => 400,
			DateFormatException::class              => 400,
			CustomerNotFoundException::class        => 404,
			RouteNotFoundException::class           => 404,
			UserAlreadyExistsException::class       => 409
	];

	/**
	 * Translates exception into response.
	 *
	 * @param Throwable $exception internal project exception.
	 * @return Response response object with JSON body and HTTP status.
	 * @throws JsonException on any errors of the object encoding into JSON.
	 */
	public function handle(Throwable $exception): Response {
		$statusCode = self::EXCEPTION_STATUS_CODES[$exception::class] ?? 500;

		$message = $statusCode === 500
				? 'Internal server error.'
				: $exception->getMessage();

		return Response::json(
				body: ['message' => $message],
				statusCode: $statusCode
		);
	}
}
