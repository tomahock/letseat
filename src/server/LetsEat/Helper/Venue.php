<?php
namespace LetsEat\Helper;
use LetsEat\Type\Position;

/**
 * Class Venue
 * https://developer.foursquare.com/docs/venues/search
 *
 * @package LetsEat\Helper
 */
class Venue
{
	const CLIENT_ID = 'F4IDCLHAR3CRCXOIUHKKPVY14K5JAQ0HQAEQMWKD1JT5GZGT';
	const CLIENT_SECRET = '3VTE1FKB1P41XV2OZRBO1MWMD53Y4GOWGAENIF4V4TNDTVQP';
	const ENDPOINT = 'https://api.foursquare.com/v2/venues/';


	public static function get(Position $position, $accuracy)
	{
		$params = array(
			'client_id' => self::CLIENT_ID,
			'client_secret' => self::CLIENT_SECRET,
			'v' => 20130815,
			'll' => $position->getLatitude() . ',' . $position->getLongitude(),
			'llAcc' => $accuracy,
			'radius' => 10000,
			'section' => 'food',
			'limit' => 50,
			'openNow' => 1,
			'sortByDistance' => 1,
			'price' => '1,2,3,4',
		);

		$url = self::ENDPOINT . 'explore?' . http_build_query($params);
		$data = file_get_contents($url);
		$return = json_decode($data, true);

		return $return;
	}

	public static function getImageUrlForVenue($id, $size)
	{
		$params = array(
			'client_id' => self::CLIENT_ID,
			'client_secret' => self::CLIENT_SECRET,
			'v' => 20130815
		);

		$url = self::ENDPOINT . $id . '/photos?' . http_build_query($params);
		$data = file_get_contents($url);
		$response = json_decode($data, true);

		if (empty($response['response']['photos']['items'])) {
			return false;
		}

		$data = $response['response']['photos']['items'][0];
		$url = $data['prefix'] . $size . $data['suffix'];
		return $url;
	}
} 