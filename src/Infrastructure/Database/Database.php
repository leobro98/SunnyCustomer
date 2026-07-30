<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Database;

use InvalidArgumentException;
use PDO;

/**
 * Creates and holds a connection to the database.
 */
class Database {

	const DRIVER_MYSQL = 'mysql';
	const DRIVER_SQLITE = 'sqlite';

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
		$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
		];

		if ($this->config['driver'] === self::DRIVER_SQLITE) {
			return new PDO($dsn, null, null, $options);
		}

		return new PDO(
				$dsn,
				$this->config['username'],
				$this->config['password'],
				$options
		);
	}

	/**
	 * @return string data source name required to create a connection.
	 */
	private function createDsn(): string {
		return match ($this->config['driver']) {
			self::DRIVER_MYSQL => sprintf(
					'mysql:host=%s;port=%d;dbname=%s;charset=%s',
					$this->config['host'],
					$this->config['port'],
					$this->config['database'],
					$this->config['charset'],
			),

			self::DRIVER_SQLITE => sprintf(
					'sqlite:%s',
					$this->config['database'],
			),

			default => throw new InvalidArgumentException(
					sprintf(
							'Unsupported database driver "%s".',
							$this->config['driver']
					)
			)
		};
	}
}
