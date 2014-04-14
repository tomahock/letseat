<?php
namespace LetsEat\Controller;

use LetsEat\Model\Node;
use LetsEat\Controller;

class Dashboard extends Controller
{
	public function index()
	{
		$contactId = '963740561';
		$ongoingEvents = Node\Event::getOngoingForContact($contactId);

		$dashboard = array(

		);
	}
} 