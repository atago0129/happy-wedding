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

$app->get('/{id}', function(\Slim\Http\Request $request, $response) {
	$id = $request->getAttribute('id');
	/** @var \Slim\Views\PhpRenderer $renderer */
	$renderer = $this->renderer;
	return $renderer->render($response, 'test.html', ['id' => $id]);
});

$app->run();
