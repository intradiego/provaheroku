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
  return str_repeat('Hello. <br>', getenv('TIMES'));
});

//funzione per ottenere l'orario del server

$app->get('/datetime', function() use($app) {
  $app['monolog']->addDebug('logging output.');
$time1 = date('H:i:s', gmdate('U')); // 13:50:29
  return "Oggi è " . date("Y/m/d") . ". Buona Giornata!<br>Sono le ore ". $time1 .".";
});

//utilizzo  https://github.com/plehr/Heroku-and-PDO/blob/master/clearDB.php
/*
$dbstr = getenv('CLEARDB_DATABASE_URL');
$dbstr = substr("$dbstr", 8);
$dbstrarruser = explode(":", $dbstr);
//Please don't look at these names. Yes I know that this is a little bit trash :D
$dbstrarrhost = explode("@", $dbstrarruser[1]);
$dbstrarrrecon = explode("?", $dbstrarrhost[1]);
$dbstrarrport = explode("/", $dbstrarrrecon[0]);
$dbpassword = $dbstrarrhost[0];
$dbhost = $dbstrarrport[0];
$dbport = $dbstrarrport[0];
$dbuser = $dbstrarruser[0];
$dbname = $dbstrarrport[1];
unset($dbstrarrrecon);
unset($dbstrarrport);
unset($dbstrarruser);
unset($dbstrarrhost);
unset($dbstr);
*/
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
               array(
                'pdo.server' => array(
                   'driver'   => 'mysql',
                    // PDO driver to use among : mysql, pgsql , oracle, mssql, sqlite, dblib
                   'user' => "bea3371a839a4b",
                   'password' => "00a24169",
                   'host' => "us-cdbr-iron-east-05.cleardb.net",
                   'port' => "3306",
                   'dbname' => "heroku_5a1d1b26eb4d349" )
                   )
               )
);


/*
php per connessione con database cleardb mysqli

<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);

$conn = new mysqli($server, $username, $password, $db);
?>

*/
// utilizzo di PDO astrazione accesso database php
/*
$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
               array(
                'pdo.server' => array(
                   'driver'   => 'pgsql',
                   'user' => $dbopts["user"],
                   'password' => $dbopts["pass"],
                   'host' => $dbopts["host"],
                   'port' => $dbopts["port"],
                   'dbname' => ltrim($dbopts["path"],'/')
                   )
               )
);
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

?>