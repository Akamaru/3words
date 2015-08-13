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

function check_privileges($ajax = false) {
  if (!$_SESSION['logged_in']) {
    if ($ajax) {
      echo json_encode(array("success" => false));
    } else {
      $_SESSION['flash'] = "Einloggen um fortzufahren.";
      header('Location: ucp.php?page=login');
    }
    exit();
  }
}

switch ($_GET['page']) {
  case "ajax": {
    check_privileges(true);
    $response = array("success" => false);
    switch ($_GET['action']) {
      case "delete-word": {
        if (isset($_GET['id'])) {
          if (is_numeric($_GET['id'])) {
            $id = (int) $_GET['id'];
            if ($sql->query("DELETE FROM `words` WHERE `id`=" . $id . ";")) {
              $response["success"] = true;
            }
          }
        }
        break;
      }
    }
    echo json_encode($response);
    exit();
    break;
  }
  case "login": {
    if ($_SESSION['logged_in']) {
      $_SESSION['flash'] = "Du bist bereits eingeloggt.";
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
        $_SESSION['flash'] = "Erfolgreich eingeloggt.";
        header('Location: ucp.php');
        exit();
      } else {
        // failed login
        $_SESSION['flash'] = "Falscher Name oder Passwort";
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
    $_SESSION['flash'] = "Erfolgreich ausgeloggt.";
    header('Location: index.php');
    exit();
    break;
  }
  case "settings": {
    check_privileges();
    
    if (!isset($_POST['action'])) {
      $tpl->draw("settings");
    } else {
      switch ($_POST['action']) {
        case "generic": {
          if (isset($_POST['sitename'])) {
            $sql->query("UPDATE `config` SET `value`='" . $sql->real_escape_string(trim($_POST['sitename'])) . "' WHERE `key`='sitename'");
          }
          if (isset($_POST['recent_check'])) {
            $sql->query("UPDATE `config` SET `value`='true' WHERE `key`='recent_public'");
          } else {
            $sql->query("UPDATE `config` SET `value`='false' WHERE `key`='recent_public'");
          }
          if (isset($_POST['recent_count'])) {
            if (is_numeric($_POST['recent_count'])) {
              $sql->query("UPDATE `config` SET `value`='" . (int) $_POST['recent_count'] . "' WHERE `key`='recent_count'");
            }
          }
          $_SESSION['flash'] = "Änderungen erfolgreich gespeichert.";
          header('Location: ucp.php?page=settings');
          exit();
          break;
        }
        case "password": {
          if (isset($_POST['password_change']) && isset($_POST['password_verify'])) {
            if ($_POST['password_change'] === $_POST['password_verify']) {
              if (strlen($_POST['password_change']) > 3) {
                $sql->query("UPDATE `config` SET `value`='" . $sql->real_escape_string(crypt_password($_POST['password_change'], gen_salt(22))) . "' WHERE `key`='password';");
                $_SESSION['flash'] = "Passwort erfolgreich geändert.";
                header('Location: ucp.php?page=settings');
                exit();
              }
            }
          }
          $_SESSION['flash'] = "Das Passwort stimmt nicht überein oder ist zu kurz.";
          header('Location: ucp.php?page=settings');
          exit();
          break;
        }
        default: {
          $tpl->draw("settings");
        }
      }
    }
    break;
  }
  case "inbox":
  default: {
    check_privileges();
    
    $sql_str = "SELECT `id`, `word1`, `word2`, `word3`, `author`, `new` FROM `words` ORDER BY `id` DESC;";
    $res = $sql->query($sql_str);
    
    $words = array();
    
    while ($r = $res->fetch_assoc()) {
      array_push($words, array(
        "id"     =>  $r['id'],
        "word1"  =>  $r['word1'],
        "word2"  =>  $r['word2'],
        "word3"  =>  $r['word3'],
        "author" =>  $r['author'],
        "new"    => ($r['new'] == 1 ? true : false)
      ));
    }
    
    $sql_str = "UPDATE `words` SET `new` = 0;";
    $sql->query($sql_str);
    
    $tpl->assign("words", $words);
    $tpl->draw("inbox");
  }
}