<?php

declare(strict_types=1);

namespace Tests\Integration;

use Leobro\SunnyCustomer\Domain\Entity\Customer;
use Leobro\SunnyCustomer\Domain\Repository\CustomerRepository;
use Leobro\SunnyCustomer\Infrastructure\Database\Database;
use Leobro\SunnyCustomer\Infrastructure\Persistence\PdoCustomerRepository;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Serves as a base for all integration tests. Creates a connection to the test database and initializes it.
 */
class IntegrationTestCase extends TestCase {

	protected PDO $pdo;
	protected CustomerRepository $customerRepository;

	protected function setUp(): void {
		parent::setUp();

		$this->initializeDatabase();
		$this->customerRepository = new PdoCustomerRepository($this->pdo);
	}

	private function initializeDatabase(): void {
		$this->createConnection();
		$this->createSchema();
		$this->clearDatabase();
	}

	private function createConnection(): void {
		$config = require $this->databaseConfigFile();
		$database = new Database($config);
		$this->pdo = $database->getConnection();
	}

	private function createSchema(): void {
		$schema = file_get_contents($this->schemaFile());
		$this->pdo->exec($schema);
	}

	private function clearDatabase(): void {
		$this->pdo->exec('DELETE FROM customer');
	}

	private function databaseConfigFile(): string {
		return dirname(__DIR__, 2) . '/config/database.test.php';
	}

	private function schemaFile(): string {
		return dirname(__DIR__, 2) . '/resources/sql/schema.sqlite.sql';
	}


	protected function assertIdsAscending(array $customers): void {
		$customerIds = array_map(
				static fn(Customer $customer) => $customer->id,
				$customers
		);

		$sortedIds = $customerIds;
		sort($sortedIds);

		self::assertSame($customerIds, $sortedIds);
	}
}
