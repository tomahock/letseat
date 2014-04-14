<?php
namespace LetsEat\Controller;

use LetsEat\Controller;
use LetsEat\Model;
use LetsEat\Type\Position;
use LetsEat\Helper;
use LetsEat\Model\Node;

class Venue extends Controller
{
	public function getSuggestions($imei, $latitude, $longitude, $accuracy)
	{
		$position = new Position($latitude, $longitude);

		$venues = Helper\Venue::get($position, $accuracy);
		if (empty($venues['response']['groups'][0]['items'])) {
			return false;
		}

		$i = 0;
		$return = array();
		foreach ($venues['response']['groups'][0]['items'] as $venue) {
			$venue = $venue['venue'];
			$i++;
			if ($i >= 10) {
				break;
			}
			$address = @$venue['location']['address'] . ' ' . @$venue['location']['postalCode']
				. ' ' . @$venue['location']['city'] . ' ' . @$venue['location']['country'];


			$score = $this->calculateScore($venue, $imei);

			$venue = array(
				'id' => $venue['id'],
				'lat' => $venue['location']['lat'],
				'lng' => $venue['location']['lng'],
				'name' => $venue['name'],
				'address' => $address,
				'phone' => @$venue['contact']['formattedPhone'],
				'image' => Helper\Venue::getImageUrlForVenue($venue['id'], 200),
				'score' => $score,
			);

			$return[] = $venue;
		}

		return $return;
	}

	private function calculateScore(array $venue, $imei)
	{
		//
		// Distance
		//
		$score = $venue['location']['distance'];

		//
		// Number of visits to this venue
		//
		$query = '
			MATCH
				(contact:CONTACT)-[r:RSVP]->(:EVENT)-[:HAS_VENUE]->(venue:VENUE)
			WHERE
				contact.imei={imei}
				AND r.status=1
				AND venue.id={venuId}
			RETURN COUNT(r) as c';

		$params = array(
			'imei' => $imei,
			'venueId' => $venue['id'],
		);
		$result = Model::executeQuery($this->getClient(), $query, $params);
		$numberOfVisits = $result->current()['c'];

		$score = $score - ($numberOfVisits * 100);

		//
		// Number of visits to this venue on this specific day of week
		//
		$dayOfWeek = date('w', time());

		$query = '
			MATCH
				(contact:CONTACT)-[r:RSVP]->(e:EVENT)-[:HAS_VENUE]->(venue:VENUE)
			WHERE
				contact.imei={imei}
				AND r.status=1
				AND venue.id={venuId}
				AND e.dayOfWeek={dayOfWeek}
			RETURN COUNT(r) as c';

		$params = array(
			'imei' => $imei,
			'venueId' => $venue['id'],
			'dayOfWeek' => $dayOfWeek,
		);
		$result = Model::executeQuery($this->getClient(), $query, $params);
		$numberOfVisits = $result->current()['c'];

		$score = $score - ($numberOfVisits * 50);

		//
		// Hour rate
		//
		$query = '
			MATCH
				(contact:CONTACT)-[r:RSVP]->(e:EVENT)-[:HAS_VENUE]->(venue:VENUE)
			WHERE
				contact.imei={imei}
				AND r.status=1
				AND venue.id={venuId}
			RETURN e.hourOfDay';

		$params = array(
			'imei' => $imei,
			'venueId' => $venue['id'],
		);
		$result = Model::executeQuery($this->getClient(), $query, $params);
		$hoursVenueWasVisited = $result->current();
		$hourOfDay = date('H', time());

		$sum = 0;
		foreach ($hoursVenueWasVisited as $hour) {
			$sum = $sum + (min(0, -60 + abs($hourOfDay, $hour)));
		}

		$score = $score + $sum;

		return $score;
	}
} 