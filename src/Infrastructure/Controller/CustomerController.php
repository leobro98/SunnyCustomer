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

/**
 * Handles usage of services. Transforms data from input, passes to services,
 * accepts their response and transforms to output.
 */
final readonly class CustomerController {

	public function __construct(
		private CustomerService $customerService
	) {
	}

	/**
	 * Creates a new customer.
	 *
	 * @param Request $request data of the HTTP request.
	 * @return Response result of the request execution.
	 * @throws DateFormatException if date representation in the request is wrong.
	 * @throws JsonException on any errors of the object encoding into JSON.
	 * @throws MissingRequestParameterException if one of the request parametrs is missing.
	 * @throws UserAlreadyExistsException if the customer is already registered.
	 */
	public function createCustomer(Request $request): Response {
		$newCustomer = CustomerMapper::fromCreateRequest($request);

		$customer = $this->customerService->createCustomer($newCustomer);

		return Response::json(
			body: CustomerMapper::toArray($customer),
			statusCode: 201
		);
	}

	/**
	 * Returns all customers from the database.
	 *
	 * @param Request $request request to fetch customers.
	 * @return Response JSON response whith the list of customers.
	 * @throws JsonException
	 */
	public function listCustomers(Request $request): Response {
		$customers = $this->customerService->getAllCustomers();

		return Response::json(
				CustomerMapper::toArrayList($customers)
		);
	}

	/**
	 * Updates the customer specified by ID with data from the request.
	 *
	 * @param Request $request request containing data to update.
	 * @return Response empty response when update was successful.
	 */
	public function updateCustomer(Request $request): Response {
		$updatedCustomer = CustomerMapper::fromUpdateRequest($request);

		$this->customerService->updateCustomer($updatedCustomer);

		return Response::empty(204);
	}

	/**
	 * Deletes existing customer specified by its ID.
	 *
	 * @param Request $request request containing ID of the customer to be deleted.
	 * @return Response empty response when successfully deleted.
	 */
	public function deleteCustomer(Request $request): Response {
		$id = (int) $request->requireQueryParameter('id');

		$this->customerService->delete($id);

		return Response::empty(204);
	}
}
