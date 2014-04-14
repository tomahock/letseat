<?php
namespace LetsEat\Model\Node;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Label;
use Everyman\Neo4j\Query\ResultSet;
use LetsEat\Helper\Weather;
use LetsEat\Model\Node;
use LetsEat\Model;
use LetsEat\Type\Time;

class Event extends Node
{
	protected $description;

	protected $time;

	protected $weather;

	/**
	 * @var Venue
	 */
	protected $venue;

	public function __construct(Client $client, $timestamp, $description)
	{
		parent::__construct($client);

		$this->description = $description;
		$this->time = new Time($timestamp);
	}

	public static function create(Client $client, $timestamp, $description)
	{
		$node = new self($client, $timestamp, $description);

		$time = new Time($timestamp);

		$properties = array(
			'description' => $description,
			'timestamp' => $timestamp,
			'dayOfWeek' => $time->getDayOfWeek(),
			'hourOfDay' => $time->getHourOfDay(),
		);

		$node->setProperties($properties);
		$node->save();

		$label = new Label($client, 'EVENT');
		$node->addLabels(array($label));
		$node->save();

		return $node;
	}

	/**
	 * @param $contactId
	 *
	 * @return Event[]
	 */
	public static function getOngoingForContact($contactId)
	{


		return array();
	}

	public static function getById(Client $client, $eventId)
	{
		$queryString = 'MATCH (event:EVENT)-[rsvp:RSVP] WHERE ID(event)={eventId} RETURN event, COLLECT(rsvp) as rsvp';
		$result = Model::executeQuery($client, $queryString, array('eventId' => $eventId));

		$return = self::prepareObject($result);
		return $return;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function getWeather()
	{
		return $this->weather;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setVenue(Venue $venue)
	{
		$this->venue = $venue;

		$this->relateTo($venue, 'EVENT_HAS_VENUE');
		return $this;
	}

	public function loadWeather()
	{
		$this->weather = Weather::getForPosition($this->venue->getPosition());
		$this->setProperty('weather', $this->weather);

		return $this;
	}

	/**
	 * @param ResultSet $data
	 * @return Event
	 */
	protected static function prepareObject(ResultSet $data)
	{
		return '';
	}
} 