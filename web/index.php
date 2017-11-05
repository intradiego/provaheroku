<?php

use Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider;
use Silex\Application;

// database config

$app->register(
    // you can customize services and options prefix with the provider first argument (default = 'pdo')
    new PDOServiceProvider('pdo'),
    array(
        'pdo.server'   => array(
            // PDO driver to use among : mysql, pgsql , oracle, mssql, sqlite, dblib
            'driver'   => 'mysql',
            'host'     => 'us-cdbr-iron-east-05.cleardb.net',
            'dbname'   => 'heroku_5a1d1b26eb4d349',
            'port'     => 3306,
            'user'     => 'bea3371a839a4b',
            'password' => '00a24169',
        )
        // optional PDO attributes used in PDO constructor 4th argument driver_options
        // some PDO attributes can be used only as PDO driver_options
        // see http://www.php.net/manual/fr/pdo.construct.php
        //'pdo.options' => array(
        //    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
        //),
        // optional PDO attributes set with PDO::setAttribute
        // see http://www.php.net/manual/fr/pdo.setattribute.php
        //'pdo.attributes' => array(
        //    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        //),
    )
);

// get PDO connection
$pdo = $app['pdo'];

// impostare data-ora italiana
date_default_timezone_set('Europe/Rome');

require('../vendor/autoload.php');

$app = new Application();
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
  return str_repeat('Hello. <br>', getenv('TIMES'));
});

//funzione per ottenere l'orario

$app->get('/datetime', function() use($app) {
  $app['monolog']->addDebug('logging output.');
$time1 = date('H:i:s', gmdate('U')); // 13:50:29
  return "Oggi è " . date("Y/m/d") . ". Buona Giornata!<br>Sono le ore ". $time1 .".";
});

//  https://github.com/plehr/Heroku-and-PDO/blob/master/clearDB.php

/*  //connessione con database cleardb mysqli

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);

$conn = new mysqli($server, $username, $password, $db);

*/

// query database

$app->get('/db/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT name FROM test_table');
  $st->execute();

  $names = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $names[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'names' => $names
  ));
});

$app->run();