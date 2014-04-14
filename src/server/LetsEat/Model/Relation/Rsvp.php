<?php
namespace LetsEat\Model\Relation;


use Everyman\Neo4j\Client;
use Everyman\Neo4j\Relationship;
use LetsEat\Model\Node\Contact;
use LetsEat\Model\Node\Event;
use LetsEat\Model\Relation;

class Rsvp extends Relation
{
	const LATE_MARGIN = 1800;

	const STATUS_PENDING = 0;
	const STATUS_ACCEPTED = 1;
	const STATUS_REJECTED = -1;

	protected $status;
	protected $timestamp;
	protected $replyTime;
	protected $message;
	protected $ttl;


	public function __construct(Client $client, $status, $timestamp, $ttl, $message, $replyTime)
	{
		parent::__construct($client);

		$this->status = $status;
		$this->timestamp = $timestamp;
		$this->replyTime = $replyTime;
		$this->message = $message;
	}

	public static function create(Client $client, Event $event, Contact $contact, $status)
	{
		$ttl = $event->getTime()->getTimestamp() + self::LATE_MARGIN;
		$timestamp = time();

		$rsvp = new self($client, $status, $timestamp, $ttl, null, null);
		$rsvp->setType('RSVP');

		$rsvp->setStartNode($event)
			->setEndNode($contact);

		$properties = array(
			'status' => $status,
			'timestamp' => $timestamp,
			'ttl' => $ttl,
		);

		$rsvp->setProperties($properties);
		$rsvp->save();
	}
} 