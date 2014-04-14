<?php
namespace LetsEat;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;

class Model
{
	/**
	 * @param Client $client
	 * @param $queryString
	 * @param array $params
	 * @return \Everyman\Neo4j\Query\ResultSet
	 */
	public static function executeQuery(Client $client, $queryString, $params = array())
	{
		$query = self::prepareQuery($client,$queryString, $params);

		$return = $client->executeCypherQuery($query);
		return $return;
	}

	/**
	 * @param Client $client
	 * @param $queryString
	 * @param $params
	 * @return Query
	 */
	protected static function prepareQuery(Client $client, $queryString, $params)
	{
		$query = new Query($client, $queryString, $params);
		return $query;
	}
} 