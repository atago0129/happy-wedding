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

$container['errorHandler'] = function ($c) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($c) {
        return $c['renderer']->render($response->withStatus(500)->withHeader('Content-Type', 'text/html'), '500.html', []);
    };
};

$container['notFoundHandler'] = function ($c) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($c) {
        if(strpos($request->getRequestTarget(), '/wedding') === 0) {
            return $c['renderer']->render($response->withStatus(404)->withHeader('Content-Type', 'text/html'), '404-wedding.html', []);
        }
        return $c['renderer']->render($response->withStatus(404)->withHeader('Content-Type', 'text/html'), '404.html', []);
    };
};

$app->get('/wedding/contact', function(\Slim\Http\Request $request, \Slim\Http\Response $response) {
    /** @var \Slim\Views\PhpRenderer $renderer */
    $renderer = $this->renderer;
    return $renderer->render($response, 'contact.html', [
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString(null, time()),
        'formUrl' => '/wedding/contact',
        'topUrl' => '/wedding/',
    ]);
});

$app->post('/wedding/contact', function(\Slim\Http\Request $request, \Slim\Http\Response $response) {
    /** @var \Slim\Views\PhpRenderer $renderer */
    $renderer = $this->renderer;


    $tokenString = $request->getParam('token');

    $token = (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')));
    if (!$token->isValid(null, $tokenString)) {
        // TODO きちんとエラー表示
        return $renderer->render($response->withStatus(500)->withHeader('Content-Type', 'text/html'), '500.html', []);
    }

    $contactType = intval($request->getParam('type'));
    if ($contactType < 0 || 3 < $contactType) {
        // TODO きちんとエラー表示
        return $renderer->render($response->withStatus(500)->withHeader('Content-Type', 'text/html'), '500.html', []);
    }
    $contactTypeMap = [
        0 => 'その他',
        1 => '食べ物のアレルギーについて',
        2 => '別の人の名前が表示されている',
        3 => '自分の専用ページのURLが分からなくなってしまった',
    ];
    $type = $contactTypeMap[$contactType];

    $name = htmlspecialchars($request->getParam('user-name'));
    $description = htmlspecialchars($request->getParam('description'));
    if ($name === '' || mb_strlen($description) > 200) {
        // TODO きちんとエラー表示
        return $renderer->render($response->withStatus(500)->withHeader('Content-Type', 'text/html'), '500.html', []);
    }

    (new \acolish\model\Slack(\acolish\config\CommonConfig::getInstance()->get('slack')))->contactNotify(null, $name, $type, $description);

    return $renderer->render($response, 'contact-result.html', [
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString(null, time()),
        'formUrl' => '/wedding/contact',
        'name' => $name,
        'type' => $type,
        'description' => $description,
        'topUrl' => '/wedding',
    ]);
});

$app->get('/wedding/{id}', function(\Slim\Http\Request $request, \Slim\Http\Response $response) {
	$id = $request->getAttribute('id');
	$user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    $gift = (new \acolish\model\Gift())->getById($user->getGiftId());

	/** @var \Slim\Views\PhpRenderer $renderer */
	$renderer = $this->renderer;
	return $renderer->render($response, 'invitation.html', [
	    'name' => $user->getDisplayName(),
        'status' => $user->getStatus(),
        'giftName' => $gift ? $gift->getName() : null,
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString($id, time()),
        'id' => $id,
    ]);
});

$app->get('/wedding/{id}/contact', function(\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $id = $request->getAttribute('id');
    $user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    /** @var \Slim\Views\PhpRenderer $renderer */
    $renderer = $this->renderer;
    return $renderer->render($response, 'contact.html', [
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString($id, time()),
        'formUrl' => '/wedding/' . $id . '/contact',
        'topUrl' => '/wedding/' . $id,
        'name' => $user->getName(),
    ]);
});

$app->post('/wedding/{id}/contact', function(\Slim\Http\Request $request, \Slim\Http\Response $response) {
    /** @var \Slim\Views\PhpRenderer $renderer */
    $renderer = $this->renderer;

    $id = $request->getAttribute('id');

    $tokenString = $request->getParam('token');

    $token = (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')));
    if (!$token->isValid($id, $tokenString)) {
        // TODO きちんとエラー表示
        return $renderer->render($response->withStatus(500)->withHeader('Content-Type', 'text/html'), '500.html', []);
    }

    $user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    $contactType = intval($request->getParam('type'));
    if ($contactType < 0 || 3 < $contactType) {
        // TODO きちんとエラー表示
        return $renderer->render($response->withStatus(500)->withHeader('Content-Type', 'text/html'), '500.html', []);
    }
    $contactTypeMap = [
        0 => 'その他',
        1 => '食べ物のアレルギーについて',
        2 => '別の人の名前が表示されている',
        3 => '自分の専用ページのURLが分からなくなってしまった',
    ];
    $type = $contactTypeMap[$contactType];

    $name = htmlspecialchars($request->getParam('user-name'));
    $description = htmlspecialchars($request->getParam('description'));
    if ($name === '' || mb_strlen($description) > 200) {
        // TODO きちんとエラー表示
        return $renderer->render($response->withStatus(500)->withHeader('Content-Type', 'text/html'), '500.html', []);
    }

    (new \acolish\model\Slack(\acolish\config\CommonConfig::getInstance()->get('slack')))->contactNotify($user, $name, $type, $description);

    return $renderer->render($response, 'contact-result.html', [
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString(null, time()),
        'formUrl' => '/wedding/contact',
        'name' => $name,
        'type' => $type,
        'description' => $description,
        'topUrl' => '/wedding/' . $id,
    ]);
});

$app->post('/wedding/{id}/rsvp', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $id = $request->getAttribute('id');

    $tokenString = $request->getParam('token');

    $token = (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')));
    if (!$token->isValid($id, $tokenString)) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'invalid_token']], 401);
    }

    $user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'not_found_user']], 404);
    }

    $status = intval($request->getParam('status'));

    if (!in_array($status, [
        \acolish\entity\User::STATUS_NON_PARTICIPANT,
        \acolish\entity\User::STATUS_PARTICIPANT,
    ], true)) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'invalid_status']], 401);
    }

    $user->setStatus($status);

    (new \acolish\model\User())->updateUserStatus($user);

    (new \acolish\model\Slack(\acolish\config\CommonConfig::getInstance()->get('slack')))->rsvpNotify($user);

    return $response->withJson(['status' => 'ok', 'result' => ['id' => $user->getId(), 'status' => $user->getStatus()]], 200);

});

$app->get('/wedding/{id}/present', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $id = $request->getAttribute('id');
    $user = (new \acolish\model\User())->getUserById($id);

    if ($user === null) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    if ($user->getStatus() !== \acolish\entity\User::STATUS_PARTICIPANT || $user->getGiftId()) {
        return $response->withStatus(302)->withHeader('Location', '/wedding/' . $id);
    }

    $gifts = (new \acolish\model\Gift())->getGiftsByType($user->getGiftType());

    /** @var \Slim\Views\PhpRenderer $renderer */
    $renderer = $this->renderer;
    return $renderer->render($response, 'present.html', [
        'id' => $user->getId(),
        'name' => $user->getDisplayName(),
        'gifts' => json_encode(array_map(function(\acolish\entity\Gift $gift) {return $gift->toAssoc();}, $gifts)),
        'token' => (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')))->generateTokenString($id, time())
    ]);
});

$app->post('/wedding/{id}/present', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $id = $request->getAttribute('id');

    $tokenString = $request->getParam('token');

    $token = (new \acolish\model\Token(\acolish\config\CommonConfig::getInstance()->get('csrf_token_salt')));
    if (!$token->isValid($id, $tokenString)) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'invalid_token']], 401);
    }

    $userModel = new \acolish\model\User();
    $user = $userModel->getUserById($id);

    if ($user === null) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'not_found_user']], 404);
    }

    $giftId = $request->getParam('giftId');

    $gift = (new \acolish\model\Gift())->getById($giftId);

    if ($gift === null) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'not_found_gift']], 404);
    }

    if ($gift->getType() !== $user->getGiftType()) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'not_found_gift']], 404);
    }

    if ($user->getStatus() !== \acolish\entity\User::STATUS_PARTICIPANT || $user->getGiftId()) {
        return $response->withJson(['status' => 'ng', 'error' => ['code' => 'unauthorized_request']], 401);
    }

    $user->setGiftId($gift->getId());
    $userModel->decisionGift($user);

    (new \acolish\model\Slack(\acolish\config\CommonConfig::getInstance()->get('slack')))->presentNotify($user, $gift);

    return $response->withJson(['status' => 'ok', 'result' => ['id' => $user->getId(), 'status' => $user->getStatus(), 'giftId' => $gift->getId()]], 200);
});

$app->run();
