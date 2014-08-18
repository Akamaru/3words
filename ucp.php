<?php
/* This file is part of 3words
 * 
 * (c) 2014 Leafcat Coding -- http://leafc.at
 * 
 * License: AGPLv3, see LICENSE for full license text
 *
 * This file was touched by:
 *  - nilsding  <nilsding@nilsding.org>
 *
 * Oh, and before I forget...
 *     ________  __________ __    ____  __  ______ 
 *    / ____/ / / / ____/ //_/   / __ \/ / / / __ \
 *   / /_  / / / / /   / ,<     / /_/ / /_/ / /_/ / with
 *  / __/ / /_/ / /___/ /| |   / ____/ __  / ____/   a
 * /_/    \____/\____/_/ |_|  /_/   /_/ /_/_/     cactus!
 *
 * Thanks for listening.
 */

include_once 'config.php';

function check_privileges() {
  if (!$_SESSION['logged_in']) {
    $_SESSION['flash'] = "Log in to continue.";
    header('Location: ucp.php?page=login');
    exit();
  }
}

switch ($_GET['page']) {
  case "login": {
    if ($_SESSION['logged_in']) {
      $_SESSION['flash'] = "You're already logged in.";
      header('Location: ucp.php');
      exit();
    }
    if (!isset($_POST['login'])) {
      $tpl->draw("login");
    } else {
      $res = $sql->query("SELECT `value` FROM `config` WHERE `key` = \"username\";")->fetch_assoc();
      $username = $res['value'];
      $res = $sql->query("SELECT `value` FROM `config` WHERE `key` = \"password\";")->fetch_assoc();
      $password = $res['value'];
      $post_pass = crypt($_POST['password'], $password);
      if (($_POST['username'] === $username) && ($post_pass === $password)) {
        // successful login
        $_SESSION['logged_in'] = true;
        $_SESSION['flash'] = "You are now logged in.";
        header('Location: ucp.php');
        exit();
      } else {
        // failed login
        $_SESSION['flash'] = "Wrong user name or password";
        header('Location: ucp.php?page=login');
        exit();
      }
    }
    break;
  }
  case "logout": {
    check_privileges();
    
    session_destroy();
    session_start();
    $_SESSION['flash'] = "Sucessfully logged out";
    header('Location: index.php');
    exit();
    break;
  }
  case "settings": {
    check_privileges();
    
    $tpl->draw("settings");
    break;
  }
  case "inbox":
  default: {
    check_privileges();
    
    $tpl->draw("inbox");
  }
}