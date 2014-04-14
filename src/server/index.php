<?php
require 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Everyman\Neo4j;

use LetsEat\Controller;
use LetsEat\Model\Node;
use LetsEat\Model\Relation;
use LetsEat\Helper;

$client = new Neo4j\Client();

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function() use($app) {
	return '<pre>' . print_r($_SERVER, true);
});

$app->post('/api/contact', function(Request $request) use($app, $client) {
	$controller = new Controller\Contacts($request);
	$controller->setClient($client);

	$result = $controller->create($request->get('contact'), $request->get('relatedContacts'));

	if (!$result) {
		$response = Helper\Response::get()
			->setStatusFail()
			->setCode(SymfonyResponse::HTTP_BAD_REQUEST)
			->setMessage('Os dados enviados estão uma bosta!');
	} else {
		$response = Helper\Response::get()
			->setStatusSuccess()
			->setCode(SymfonyResponse::HTTP_OK)
			->setMessage('O evento foi apagado com sucesso');
	}

	return $app->json($response);
});

$app->get('/api/venue', function(Request $request) use($app, $client) {
	$controller = new Controller\Venue($request);
	$controller->setClient($client);

	$result = $controller->getSuggestions(
		$request->get('imei'),
		$request->get('latitude'),
		$request->get('longitude'),
		$request->get('accuracy')
	);

	if (!$result) {
		$response = Helper\Response::get()
			->setStatusFail()
			->setCode(SymfonyResponse::HTTP_NOT_FOUND)
			->setMessage('Não foram encontradas sugestões de estabelecimentos para a localização atual');
	} else {
		$response = Helper\Response::get()
			->setStatusSuccess()
			->setData($result)
			->setCode(SymfonyResponse::HTTP_OK);
	}

	return $app->json($response, $response->code);
});

$app->get('/api/contact', function(Request $request) use($app, $client) {
	$controller = new Controller\Contacts($request);
	$controller->setClient($client);

	$result = $controller->getSuggestions(
		$request->get('imei'),
		$request->get('latitude'),
		$request->get('longitude'),
		$request->get('venueId')
	);

	if (!$result) {
		$response = Helper\Response::get()
			->setStatusFail()
			->setCode(SymfonyResponse::HTTP_BAD_REQUEST)
			->setMessage('Os dados enviados estão uma bosta!');
	} else {
		$response = Helper\Response::get()
			->setStatusSuccess()
			->setCode(SymfonyResponse::HTTP_OK)
			->setMessage('O evento foi apagado com sucesso');
	}

	return $app->json($response);
});

$app->post('/api/event', function(Request $request) use($app) {

});

/**
 * EVENT
 */
$app->get('/api/event', function(Request $request) use($app) {

});

$app->get('/api/event/{id}', function(Request $request, $id) use($app) {

});

$app->delete('/api/event', function(Request $request) use($app, $client) {
	$controller = new Controller\Events($request);
	$controller->setClient($client);

	$result = $controller->delete($request->get('eventId'));

	if (!$result) {
		$response = Helper\Response::get()
			->setStatusFail()
			->setCode(SymfonyResponse::HTTP_NOT_FOUND)
			->setMessage('O evento não existe');
	} else {
		$response = Helper\Response::get()
			->setStatusSuccess()
			->setCode(SymfonyResponse::HTTP_OK)
			->setMessage('O evento foi apagado com sucesso');
	}

	return $app->json($response, $response->code);
});

$app->get('/api/dashboard', function(Request $request) use($app, $client) {
	$controller = new Controller\Dashboard($request);
	$controller->setClient($client);

	$controller->index();
});

$app->put('/api/rsvp', function(Request $request) use($app) {

});




$app->run();