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
	return $renderer->render($response, 'invitation.html', ['name' => $user->getName() . ' さま']);
});

$app->run();
