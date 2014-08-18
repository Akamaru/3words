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

switch ($_GET['page']) {
  case "login": {
    $tpl->draw("login");
    break;
  }
  case "logout": {
    session_destroy();
    session_start();
    $_SESSION['flash'] = "Sucessfully logged out";
    header('Location: index.php');
    break;
  }
  case "settings": {
    $tpl->draw("settings");
    break;
  }
  case "inbox":
  default: {
    $tpl->draw("inbox");
  }
}