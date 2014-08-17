<?php
/*
 * 3words configuration file
 */

$MYSQL_SERVER   = "localhost";
$MYSQL_DATABASE = "";
$MYSQL_USERNAME = "";
$MYSQL_PASSWORD = "";

// Please comment or delete the next line.
die('Please edit config.php!');

// Don't touch anything below here.

$sql = new mysqli($MYSQL_SERVER,
                  $MYSQL_USERNAME,
                  $MYSQL_PASSWORD,
                  $MYSQL_DATABASE);

if ($sql->connect_errno) {
  echo '<!-- Failed to connect to MySQL server: ' . $sql->connect_error . ' ('
        . $sql->connect_errno . ') --><style>*{background:red !important;}'
        . '</style>';
}

// some useful functions
function gen_salt($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
  $str = '';
  $count = strlen($charset);
  while ($length--) {
    $str .= $charset[mt_rand(0, $count-1)];
  }
  return $str;
}

function crypt_password ($pass, $salt) {
  return crypt($pass, "$2y$08$" . $salt);
}

// the
session_start();

if (!isset($notemplate)) {
  require_once 'ext/rain.tpl.class.php';
  
  raintpl::configure("path_replace", false);
  raintpl::configure("tpl_dir", "views/");
  
  $tpl = new RainTPL;
}