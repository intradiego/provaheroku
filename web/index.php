<?php

// impostare data-ora italiana
date_default_timezone_set('Europe/Rome');


require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

//funzione aggiunta seguendo la guida iniziale

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

//aggiunta funzione per leggere un parametro dal file di configurazione

$app->get('/ripeti', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return str_repeat('Hello', getenv('TIMES'));
});

//funzione per ottenere l'orario del server

$app->get('/datetime', function() use($app) {
  $app['monolog']->addDebug('logging output.');
$time1 = date('H:i:s', gmdate('U')); // 13:50:29
  return "Oggi è " . date("Y/m/d") . ". Buona Giornata!<br>Sono le ore ". $time1 .".";
});



$app->run();

