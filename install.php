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

$notemplate = true;

include_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Installation</title>
</head>
<body>

  <h1>Installation</h1>

<?php if (isset($_POST['step1'])) { // step 1: create tables
  $sql_str = <<<SQL
CREATE TABLE IF NOT EXISTS `words` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `word1` VARCHAR(45) NULL,
  `word2` VARCHAR(45) NULL,
  `word3` VARCHAR(45) NULL,
  `author` VARCHAR(45) NULL,
  `new` BOOLEAN,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `config` (
  `key` VARCHAR(25) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
;

  if (!$sql->multi_query($sql_str)) { ?>
<h2>Ein Fehler ist aufgetreten</h2>
<pre><?php echo $sql->error; ?></pre>
<form method="POST">
  <input type="hidden" name="step1" value="1">
  <button type="submit">Nochmal</button>
</form>
  <?php } else {
hell: ?>

<h2>Schritt 1: Login Daten</h2>
<form method="POST">
  <label>Seiten Name: <input type="text" name="sitename" value="3words"></label><br />
  <label>User Name: <input type="text" name="username"></label><br />
  <label>Passwort: <input type="password" id="p1" name="password"></label><br />
  <label>Passwort wiederholen: <input type="password" id="p2" name="passwordconfirm"></label><br />
  <input type="hidden" name="step2" value="2">
  <button type="submit">Weiter</button>
</form>

<?php }} else if (isset($_POST['step2'])) { // step2: create user
  if ($_POST['password'] !== $_POST['passwordconfirm']) {
    echo "<p>The passwords did not match</p>";
    goto hell; // the goto keyword was introduced in PHP 5.3... so why don't use it?
  }
  $sql_str = "INSERT INTO `config` (`key`, `value`) VALUES ('sitename', '" . $sql->real_escape_string($_POST['sitename']) . "'); " .
             "INSERT INTO `config` (`key`, `value`) VALUES ('username', '" . $sql->real_escape_string($_POST['username']) . "'); " .
             "INSERT INTO `config` (`key`, `value`) VALUES ('password', '" . $sql->real_escape_string(crypt_password($_POST['password'], gen_salt(22))) . "'); " .
             "INSERT INTO `config` (`key`, `value`) VALUES ('recent_public', 'false'); " .
             "INSERT INTO `config` (`key`, `value`) VALUES ('recent_count', '5');";
  if (!$sql->multi_query($sql_str)) { ?>
<h2>Ein Fehler ist aufgetreten</h2>
<pre><?php echo $sql->error; ?></pre>
<form method="POST">
  <label>Seiten Name: <input type="text" name="sitename" value="<?php echo htmlspecialchars($_POST['sitename']); ?>"></label><br />
  <label>User Name: <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username']); ?>"></label><br />
  <label>Passwort: <input type="password" id="p1" name="password"></label><br />
  <label>Passwort wiederholen: <input type="password" id="p2" name="passwordconfirm"></label><br />
  <input type="hidden" name="step2" value="2">
  <button type="submit">Nochmal</button>
</form>
  <?php } else { ?>
<h2>Schritt 3: Viel Spaß!</h2>
Bitte lösche nun die install.php
<?php }} else { // step 0: click next to continue ?>
  <h2>Schritt 0: Klick auf den Button um zu starten!</h2>
  <form method="POST">
    <input type="hidden" name="step1" value="1">
    <button type="submit">Klick mich!</button>
  </form>
<?php } ?>

</body>
</html>