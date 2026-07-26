<?php

declare(strict_types=1);

namespace Leobro\SunnyCustomer\Infrastructure\Http;

use JsonException;

/**
 * Accepts components of HTTP response into its properties and generates HHTP response.
 */
final class Response {

	private int $statusCode = 200;
	private array $headers = [];
	private string $body = '';

	/**
	 * Prevents creation of incomplete response object.
	 */
	private function __construct() {
	}

	/**
	 * Creates a new {@link  Response} object with JSON content from output data.
	 * @param array $body response body.
	 * @param int $statusCode response status code.
	 * @return Response reponse object ready for sending.
	 * @throws JsonException on any errors of the object encoding into JSON.
	 */
	public static function json(array $body, int $statusCode = 200): Response {
		$response = new Response();
		$response->setStatusCode($statusCode);
		$response->setHeader('Content-Type', 'application/json');
		$response->setBody(json_encode($body, JSON_THROW_ON_ERROR));
		return $response;
	}


	private function setStatusCode(int $statusCode): void {
		$this->statusCode = $statusCode;
	}

	private function setHeader(string $name, string $value): void {
		$this->headers[$name] = $value;
	}

	private function setBody(string $body): void {
		$this->body = $body;
	}

	/**
	 * Sends HTTP response.
	 * @return void
	 */
	public function send(): void {
		http_response_code($this->statusCode);

		foreach ($this->headers as $name => $value) {
			header("$name: $value");
		}
		echo $this->body;
	}
}
