<?php
namespace LetsEat\Controller;

use LetsEat\Controller;
use LetsEat\Model\Relation\Rsvp;
use LetsEat\Type\Position;
use Symfony\Component\HttpFoundation\Request;
use LetsEat\Model\Node;

class Events extends Controller
{
	public function create($description, $venueId)
	{
		$timestamp = $this->getRequest()->get('timestamp');
		$description = $this->getRequest()->get('description');
		$venueId = $this->getRequest()->get('venueId');

		$position = new Position(
			$this->getRequest()->get('latitude'),
			$this->getRequest()->get('longitude')
		);

		$event = Node\Event::create($this->getClient(), $timestamp, $description);

		$venue = Node\Venue::getById($this->getClient(), $venueId);
		$event->setVenue($venue);

		$event->loadWeather();

		$contact = Node\Contact::getById($this->getClient(), '123123');

		//
		// Create RSVP relation for the Event creator
		//
		Rsvp::create($this->getClient(), $event, $contact, Rsvp::STATUS_ACCEPTED);

		$invitees = $this->getRequest()->get('invitees');
		foreach ($invitees as $contactId) {
			$contact = Node\Contact::getById($this->getClient(), $contactId);
			Rsvp::create($this->getClient(), $event, $contact, Rsvp::STATUS_PENDING);
		}

		return true;
	}

	public function delete($eventId)
	{
		$event = Node\Event::getById($this->getClient(), $eventId);

		if (!$event instanceof Node\Event) {
			return false;
		}

		$event->delete();
		return true;
	}

	/**
	 * @return bool|Node\Event[]
	 */
	public function getOngoing($contactId)
	{
		$contactId = $this->getRequest()->get('contactId');

		$events = Node\Event::getOngoingForContact($contactId);
		if (empty($events)) {
			return false;
		}

		return $events;
	}
} 