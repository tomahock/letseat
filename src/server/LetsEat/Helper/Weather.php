<?php
namespace LetsEat\Helper;

use LetsEat\Type\Position;

class Weather
{
	const ENDPOINT = 'http://api.openweathermap.org/data/2.5/weather';

	/**
	 * @return int
	 */
	public static function getForPosition(Position $position)
	{
		$params = array(
			'lat' => $position->getLatitude(),
			'lon' => $position->getLongitude(),
		);

		$url = self::ENDPOINT . '?' . http_build_query($params);
		$data = file_get_contents($url);
		$response = json_decode($data, true);

		if (empty($response['weather'][0]['id'])) {
			return false;
		}

		$return = $response['weather'][0]['id'];
		return $return;
	}
} 