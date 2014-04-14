<?php
namespace LetsEat\Model;

use Everyman\Neo4j;

abstract class Node extends Neo4j\Node
{
	/**
	 * @var Neo4j\Client;
	 */
	protected $client;

	public function __construct(Neo4j\Client $client)
	{
		$this->client = $client;
	}
} 