<?php
require_once 'vendor/autoload.php';
require_once 'lib/fileManager.php';
require_once 'lib/cloudVisionCaller.php';

$configuration = [
	'settings' => [
		'displayErrorDetails' => true,
	],
];
$c = new \Slim\Container($configuration);

$app = new \Slim\App($c);
$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('./views');
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    return $view;
};


/* routing */

// redirect to
$app->get('/', function($req, $res) {
	return $res->withRedirect('/upload');
});

// show file input form
$app->get('/upload', function($req, $res) {
	return $this->view->render($res, 'index.html');
})->setName('root');

// File Upload
$app->post('/upload', function($req, $res) {
	$uploadfile = $req->getUploadedFiles()['inputFile'];
	$fm = new FileManager($uploadfile);
	$path = $fm->getFilePath();

	$cvc = new CloudVisionCaller();
	$cvc->setFile($path);
	$cvc->setDetection('LABEL_DETECTION');

	list($results, $typeKey) = $cvc->call();

	return $this->view->render($res, 'index.html', [
		'imagePath' => $fm->getFileURL(),
		'results'   => $results,
		'typeKey'   => $typeKey,
	]);
});

$app->run();
