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

function check_word($word) {
  if (!isset($_POST[$word])) {
    return false;
  }
  $final = trim($_POST[$word]);
  if (strlen($final) == 0) {
    return false;
  }
  if (strpos($final, ' ') === false) {
    return htmlspecialchars($final);
  } else {
    return false;
  }
}

if (isset($_POST['words'])) {
  // generic check whether all words were entered
  $word1 = check_word("word1");
  $word2 = check_word("word2");
  $word3 = check_word("word3");
  if ($word1 === false || $word2 === false || $word3 === false) {
    $_SESSION['flash'] = "Not all words entered are valid.";
    header("Location: index.php");
    exit();
  }
  
  $author = htmlspecialchars(trim($_POST['author']));
  if (strlen($author) == 0) {
    $author = "Anonymous";
  }
  
  $sql_str = "INSERT INTO `words` (`word1`, `word2`, `word3`, `author`, `new`) VALUES ('" . $sql->real_escape_string($word1) . "', '" . $sql->real_escape_string($word2) . "', '" . $sql->real_escape_string($word3) . "', '" . $sql->real_escape_string($author) . "', 1);";
  
  if (!$sql->query($sql_str)) {
    $_SESSION['flash'] = "An error occurred: " . $sql->error;
    header("Location: index.php");
    exit();
  }
  
  $_SESSION['flash'] = "Thank you!";
  header("Location: index.php");
  exit();
}

$tpl->draw("index");