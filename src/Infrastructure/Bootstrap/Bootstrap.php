<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Bootstrap;

use Leobro\SunnyCustomer\Application\Service\CustomerService;
use Leobro\SunnyCustomer\Infrastructure\Controller\CustomerController;
use Leobro\SunnyCustomer\Infrastructure\Database\Database;
use Leobro\SunnyCustomer\Infrastructure\Http\Router;
use Leobro\SunnyCustomer\Infrastructure\Persistence\PdoCustomerRepository;

/**
 * Composition root for the application. Creates all needed components.
 */
final class Bootstrap {

	public function __construct(
		private readonly array $config
	) {
	}

	public function createRouter(): Router {
		$database = new Database($this->config);
		$customerRepository = new PdoCustomerRepository(pdo: $database->getConnection());
		$customerService = new CustomerService(repository: $customerRepository);
		$customerController = new CustomerController(customerService: $customerService);

		$router = new Router();
		$router->registerPost('/customers', [$customerController, 'createCustomer']);
		$router->registerGet('/customers', [$customerController, 'listCustomers']);

		return $router;
	}
}
