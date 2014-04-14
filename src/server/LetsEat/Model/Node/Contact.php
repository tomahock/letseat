<?php
namespace LetsEat\Model\Node;

use Everyman\Neo4j\Client;
use Everyman\Neo4j;
use Everyman\Neo4j\Label;
use Everyman\Neo4j\Query\ResultSet;
use LetsEat\Model\Node;
use LetsEat\Model;
use LetsEat\Type;

class Contact extends Node
{
	/**
	 * @var int
	 */
	protected $mobile;

	/**
	 * @var Type\Imei
	 */
	protected $imei;

	/**
	 * @var int
	 */
	protected $timestamp;


	public function __construct(Client $client, $mobile, $imei, $timestamp)
	{
		parent::__construct($client);

		$this->mobile = $mobile;
		$this->imei = $imei;
		$this->timestamp = $timestamp;
	}

	public static function create(Client $client, $mobile, $imei = null)
	{
		$timestamp = time();

		$node = new self($client, $mobile, $imei, $timestamp);
		$properties = array(
			'mobile' => $mobile,
			'imei' => $imei,
			'timestamp' => $timestamp,
		);
		$node->setProperties($properties);
		$node->save();

		$label = new Label($client, 'CONTACT');
		$node->addLabels(array($label));
		$node->save();

		return $node;
	}

	/**
	 * @param Client $client
	 * @param $contactId
	 * @return Venue
	 */
	public static function getById(Client $client, $contactId)
	{
		$queryString = 'MATCH (contact:CONTACT) WHERE ID(contact)={contactId} RETURN contact';
		$result = Model::executeQuery($client, $queryString, array('contactId' => $contactId));

		$node = @$result->current()['contact'];
		if (empty($node)) {
			return false;
		}

		$return = self::prepareObject($result);
		return $return;
	}

	/**
	 * @param Client $client
	 * @param $mobile\
	 * @return Venue
	 */
	public static function getByMobile(Client $client, $mobile)
	{
		$queryString = 'MATCH (contact:CONTACT) WHERE contact.mobile={mobile} RETURN contact';
		$result = Model::executeQuery($client, $queryString, array('mobile' => (string) $mobile));

		$node = @$result->current()['contact'];
		if (empty($node)) {
			return false;
		}

		$return = self::prepareObject($node);
		return $return;
	}

	public function getMobile()
	{
		return $this->mobile;
	}

	public function setImei($imei)
	{
		$this->imei = $imei;

		$this->setProperty('imei', $this->imei);
		$this->save();

		return $this;
	}
	public function getImei()
	{
		return $this->imei;
	}

	/**
	 * @param Contact $contact
	 * @return \Everyman\Neo4j\Relationship
	 */
	public function relateToContact(Contact $contact)
	{
		$rel = $this->relateTo($contact, 'HAS_CONTACT');
		return $rel;
	}

	/**
	 * @param ResultSet $data
	 * @return Venue
	 */
	public static function prepareObject(Neo4j\Node $node)
	{
		$contact = new self(
			$node->getClient(),
			$node->getProperty('mobile'),
			$node->getProperty('imei'),
			$node->getProperty('timestamp')
		);
		$contact->setId($node->getId());

		return $contact;
	}
} 