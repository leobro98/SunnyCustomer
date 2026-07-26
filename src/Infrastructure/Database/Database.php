<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Database;

use PDO;

/**
 * Creates and holds a connection to the database.
 */
class Database {
	private ?PDO $connection = null;

	public function __construct(
		private readonly array $config
	) {}

	/**
	 * @return PDO connection to the confugured database.
	 */
	public function getConnection(): PDO {
		if ($this->connection === null) {
			$this->connection = $this->createConnection();
		}

		return $this->connection;
	}

	/**
	 * @return PDO created connection to the database.
	 */
	private function createConnection(): PDO {
		$dsn = $this->createDsn();
		$userName = $this->config['username'];
		$password = $this->config['password'];

		return new PDO($dsn, $userName, $password,
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			]
		);
	}

	/**
	 * @return string data source name required to create a connection.
	 */
	private function createDsn(): string {
		$host = $this->config['host'];
		$port = $this->config['port'];
		$database = $this->config['database'];
		$charset = $this->config['charset'];

		return sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
			$host, $port, $database, $charset);
	}
}
