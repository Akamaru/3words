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

if (!isset($_SESSION['logged_in'])) {
  $_SESSION['logged_in'] = false;
}

if (!isset($notemplate)) {
  require_once 'ext/rain.tpl.class.php';
  
  raintpl::configure("path_replace", false);
  raintpl::configure("tpl_dir", "views/");
  
  $tpl = new RainTPL;
  
  // new words counter
  $new_words_count = -1;
  if ($_SESSION['logged_in'] === true) {
    $res = $sql->query("SELECT `id` FROM `words` WHERE `new` = 1;");
    $new_words_count = $res->num_rows;
  }
  
  // total words count
  $res = $sql->query("SELECT `id` FROM `words`;");
  $words_total_count = $res->num_rows * 3;
  
  // site name
  $res = $sql->query("SELECT `value` FROM `config` WHERE `key` = \"sitename\";")->fetch_assoc();
  $site_name = htmlspecialchars($res['value']);
  
  // user name
  $res = $sql->query("SELECT `value` FROM `config` WHERE `key` = \"username\";")->fetch_assoc();
  $user_name = htmlspecialchars($res['value']);
  
  // show recent?
  $res = $sql->query("SELECT `value` FROM `config` WHERE `key` = \"recent_public\";")->fetch_assoc();
  $recent_public = $res['value'] === "true" ? true : false;
  $recent_count = 0;
  if ($recent_public) {
    $res = $sql->query("SELECT `value` FROM `config` WHERE `key` = \"recent_public\";")->fetch_assoc();
    $recent_count = (int) $res['value'];
  }
  
  // the flash
  $message = null;
  if (isset($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
  }
  
  $tpl->assign("logged_in", $_SESSION['logged_in']);
  $tpl->assign("site_name", $site_name);
  $tpl->assign("user_name", $user_name);
  $tpl->assign("words_total", $words_total_count);
  $tpl->assign("inbox_count", $new_words_count);
  $tpl->assign("recent_public", $recent_public);
  $tpl->assign("recent_count", $recent_count);
  $tpl->assign("message", $message);
}