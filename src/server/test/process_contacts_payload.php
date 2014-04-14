<?php
require '../vendor/autoload.php';

$payload = unserialize(trim(file_get_contents('contacts_payload')));

$relatedContacts = array();
foreach ($payload['relatedContacts'] as $contacts) {
	foreach($contacts['phoneNumbers'] as $numbers) {
		$number = $numbers['value'];
		$number = preg_replace('/\s+/', '', $number);
		$number = substr($number, -9);

		if (substr($number, 0, 1) != 9) {
			continue;
		}

		$relatedContacts[] = $number;
		var_dump($number);
	}
}

var_dump(count($relatedContacts));