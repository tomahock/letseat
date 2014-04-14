<?php
namespace LetsEat;

use Everyman\Neo4j\Client;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller
{
	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Client
	 */
	private $client;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getClient()
	{
		return $this->client;
	}
} 