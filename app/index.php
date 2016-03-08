<?php
date_default_timezone_set('Asia/Tokyo');  //TODO fix php.ini

require_once 'vendor/autoload.php';
require_once 'lib/fileManager.php';
require_once 'lib/cloudVisionHandler.php';
require_once 'lib/errorHandler.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


$configuration = array();
if (!isset($_SERVER['SLIM_ENV'])) {
	$configuration['settings'] = ['displayErrorDetails' => true];
}
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

$container['log'] = function ($container) {
	$logger = new Monolog\Logger('Easy_GoogleCloudVision');
    $filename = __DIR__.'/logs/'.date('Ymd').'.log';
	$logLevel = isset($_SERVER['SLIM_ENV'])
		? Monolog\Logger::INFO
		: Monolog\Logger::DEBUG;
	$stream = new Monolog\Handler\StreamHandler($filename, $logLevel);
    $fingersCrossed = new Monolog\Handler\FingersCrossedHandler($stream, Monolog\Logger::INFO);
    $logger->pushHandler($fingersCrossed);

    return $logger;
};

$container['errorHandler'] = function ($container) {
    return new Error($container['log']);
};


/* routing */
$detectTypeMember = array(
	'LABEL_DETECTION',
	'TEXT_DETECTION',
	'FACE_DETECTION',
	'LANDMARK_DETECTION',
	'LOGO_DETECTION',
	'SAFE_SEARCH_DETECTION',
	'IMAGE_PROPERTIES',
);

// redirect to
$app->get('/', function($req, $res) {
	$this->log->addInfo('use redirect', array('path' => '/'));
	return $res->withRedirect('/upload');
});

// show file input form
$app->get('/upload', function($req, $res) {
	return $this->view->render($res, 'index.html');
})->setName('root');

// File Upload
$app->post('/upload', function($req, $res) use ($detectTypeMember) {
	$input = $req->getParsedBody();
	$detectType = isset($input['detectType']) ? $input['detectType'] : null;
	if (!$detectType || !in_array($detectType, $detectTypeMember)) {
		throw new Exception('Invalid detectType input ERROR!');
	}

	$uploadfile = $req->getUploadedFiles()['inputFile'];
	$fm = new FileManager($uploadfile);
	$path = $fm->getFilePath();

	$cvh = new CloudVisionHandler();
	$cvh->setFile($path);
	$cvh->setDetection($detectType);

	$results = $cvh->call();

	return $this->view->render($res, 'index.html', [
		'imagePath'  => $fm->getFileURL(),
		'results'    => $results,
		'detectType' => $detectType,
	]);
});

$app->run();
