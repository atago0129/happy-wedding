<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/autoload.php';

$settings = require __DIR__ . '/../conf/slim.php';
$app = new \Slim\App($settings);

$container = $app->getContainer();

$container['renderer'] = function ($c) {
	$settings = $c->get('settings')['renderer'];
	return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['renderer']->render($response, '404.html', []);
    };
};

$app->get('/list_test', function(\Slim\Http\Request $request, $response) {
    /** @var \Slim\Views\PhpRenderer $renderer */
    $renderer = $this->renderer;
    return $renderer->render($response, 'index.html', []);
});

$app->get('/{id}', function(\Slim\Http\Request $request, $response) {
	$id = $request->getAttribute('id');
	$user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

	/** @var \Slim\Views\PhpRenderer $renderer */
	$renderer = $this->renderer;
	return $renderer->render($response, 'invitation.html', [
	    'name' => $user->getName() . ' さま',
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString($id, time())
    ]);
});

$app->post('/{id}/rvsp', function ($request, $response) {
    /** @var \Slim\Http\Request $request */
    /** @var \Slim\Http\Response $response */
    $id = $request->getAttribute('id');

    $tokenString = $request->getParam('token');

    $token = (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')));
    if (!$token->isValid($id, $tokenString)) {
        return $response->withJson(['status' => 'ng', 'message' => 'invalid_token'], 401);
    }

    $user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        return $response->withJson(['status' => 'ng', 'message' => 'invalid_id'], 401);
    }

    $status = intval($request->getParam('status'));

    if (!in_array($status, [
        \acolish\entity\User::STATUS_UNANSWERED,
        \acolish\entity\User::STATUS_NON_PARTICIPANT,
        \acolish\entity\User::STATUS_UNSELECTED_PRESENT,
        \acolish\entity\User::STATUS_SELECTED_PRESENT,
    ], true)) {
        return $response->withJson(['status' => 'ng', 'message' => 'invalid_status'], 401);
    }

    $user->setStatus($status);

    (new \acolish\model\User())->updateUserStatus($user);

    return $response->withJson(['status' => 'ok',], 200);

});

$app->run();
