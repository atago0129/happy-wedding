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
    return $renderer->render($response, 'present.html', []);
});

$app->get('/{id}', function(\Slim\Http\Request $request, \Slim\Http\Response $response) {
	$id = $request->getAttribute('id');
	$user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

	/** @var \Slim\Views\PhpRenderer $renderer */
	$renderer = $this->renderer;
	return $renderer->render($response, 'invitation.html', [
	    'name' => $user->getDisplayName(),
        'status' => $user->getStatus(),
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString($id, time())
    ]);
});

$app->post('/{id}/rsvp', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $id = $request->getAttribute('id');

    $tokenString = $request->getParam('token');

    $token = (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')));
    if (!$token->isValid($id, $tokenString)) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'invalid_token']], 401);
    }

    $user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'invalid_id']], 401);
    }

    $status = intval($request->getParam('status'));

    if (!in_array($status, [
        \acolish\entity\User::STATUS_NON_PARTICIPANT,
        \acolish\entity\User::STATUS_UNSELECTED_PRESENT,
    ], true)) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'invalid_status']], 401);
    }

    if ($user->getStatus() === $status) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'invalid_status']], 401);
    }

    $user->setStatus($status);

    (new \acolish\model\User())->updateUserStatus($user);

    // TODO Slack通知

    return $response->withJson(['status' => 'ok', 'result' => ['id' => $user->getId(), 'status' => $user->getStatus()]], 200);

});

$app->get('/{id}/present', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $id = $request->getAttribute('id');
    $user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    if ($user->getStatus() === \acolish\entity\User::STATUS_UNANSWERED || $user->getStatus() === \acolish\entity\User::STATUS_SELECTED_PRESENT) {
        $response->withStatus(301)->withHeader('Location', '/' . $id);
    }

    /** @var \Slim\Views\PhpRenderer $renderer */
    $renderer = $this->renderer;
    return $renderer->render($response, 'present.html', [
        'name' => $user->getDisplayName(),
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString($id, time())
    ]);
});

$app->run();
