<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Controller;

use JsonException;
use Leobro\SunnyCustomer\Application\Service\CustomerService;
use Leobro\SunnyCustomer\Exception\DateFormatException;
use Leobro\SunnyCustomer\Exception\MissingRequestParameterException;
use Leobro\SunnyCustomer\Exception\UserAlreadyExistsException;
use Leobro\SunnyCustomer\Infrastructure\Http\Mapper\CustomerMapper;
use Leobro\SunnyCustomer\Infrastructure\Http\Request;
use Leobro\SunnyCustomer\Infrastructure\Http\Response;

final readonly class CustomerController {

	public function __construct(
		private CustomerService $customerService
	) {
	}

	/**
	 * Creates a new customer.
	 * @param Request $request data of the HTTP request.
	 * @return Response result of the request execution.
	 * @throws DateFormatException if date representation in the request is wrong.
	 * @throws JsonException on any errors of the object encoding into JSON.
	 * @throws MissingRequestParameterException if one of the request parametrs is missing.
	 * @throws UserAlreadyExistsException if the customer is already registered.
	 */
	public function createCustomer(Request $request): Response {
		$newCustomer = CustomerMapper::fromRequest($request);

		$customer = $this->customerService->createCustomer($newCustomer);

		return Response::json(
			body: CustomerMapper::toArray($customer),
			statusCode: 201
		);
	}

	public function listCustomers(Request $request): Response {
		throw new \LogicException('Not implemented.');
	}
}
