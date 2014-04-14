<?php
namespace LetsEat\Controller;

use LetsEat\Controller;
use LetsEat\Model\Node\Contact;
use LetsEat\Model;
use LetsEat\Type\Position;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Contacts extends Controller
{
	public function create($contactData, $relatedContactsData)
	{
		$contact = Contact::getByMobile($this->getClient(), $contactData['mobile']);
		if (!$contact instanceof Contact) {
			$contact = Contact::create($this->getClient(), $contactData['mobile'], $contactData['imei']);
		}
		elseif (!$contact->getImei()) {
			$contact->setImei($contact['imei']);
		}

		if (!empty($relatedContactsData)) {
			$relatedContactsData = $this->parseRelatedContents($relatedContactsData);

			foreach ($relatedContactsData as $mobile) {
				if (Contact::getByMobile($this->getClient(), $mobile) instanceof Contact) {
					continue;
				}

				$relatedContact = Contact::create($this->getClient(), $mobile);
				$rel = $contact->relateToContact($relatedContact);
				$rel->save();
			}
		}

		return true;
	}

	public function getSuggestions($imei, $latitude, $longitude, $venueId)
	{
		$position = new Position($latitude, $longitude);

		$query = '
			MATCH (c:CONTACT)-[:RSVP]->(event:EVENT)<-[r:RSVP]-(c2:CONTACT)
			WHERE
				c.imei={imei}
			WITH
				c2, COUNT(c2) AS numberOfTimes
			RETURN c2
			ORDER BY numberOfTimes DESC
		';

		$params = array(
			'imei' => $imei,
		);
		$result = Model::executeQuery($this->getClient(), $query, $params);

	}

//	private function calculateScore();

	private static function parseRelatedContents($data)
	{
		$relatedContacts = array();
		foreach ($data as $contacts) {
			foreach($contacts['phoneNumbers'] as $numbers) {
				$number = $numbers['value'];
				$number = preg_replace('/\s+/', '', $number);
				$number = substr($number, -9);

				if (substr($number, 0, 1) != 9) {
					continue;
				}

				$relatedContacts[] = $number;
			}
		}

		return $relatedContacts;
	}
} 