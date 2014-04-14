<?php
namespace LetsEat\Model\Node;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Label;
use Everyman\Neo4j\Query\ResultSet;
use LetsEat\Model\Node;
use LetsEat\Model;
use LetsEat\Type;

class Venue extends Node
{
	/**
	 * @var Type\Position
	 */
	protected $position;

	protected $name;

	protected $description;

	public function getPosition()
	{
		return $this->position;
	}

	public function __construct(Client $client, $latitude, $longitude, $name, $description)
	{
		parent::__construct($client);

		$this->position = new Type\Position($latitude, $longitude);
		$this->name = $name;
		$this->description = $description;
	}

	public static function __create(Client $client, $latitude, $longitude, $name, $description)
	{
		$node = new self($client, $latitude, $longitude, $name, $description);

		$properties = array(
			'latitude' => $longitude,
			'longitude' => $latitude,
			'name' => $name,
			'description' => $description,
		);
		$node->setProperties($properties);
		$node->save();

		$label = new Label($client, 'VENUE');
		$node->addLabels(array($label));
		$node->save();

		return $node;
	}

	/**
	 * @param Client $client
	 * @param $venueId
	 * @return Venue
	 */
	public static function getById(Client $client, $venueId)
	{
		$queryString = 'MATCH (venue:VENUE) WHERE venue.id={venueId} RETURN venue';
		$result = Model::executeQuery($client, $queryString, array('venueId' => $venueId));

		$return = self::prepareObject($result);
		return $return;
	}

	/**
	 * @param ResultSet $data
	 * @return Venue
	 */
	protected static function prepareObject(ResultSet $data)
	{
		return 1;
	}
} 