<?php

function db_connect() {
  global $mysql_link, $config;

  $mysql_link = mysql_connect($config['mysql']['host'], $config['mysql']['user'], $config['mysql']['password']);

  if ($mysql_link === FALSE) {
    exit('Неудалось подключиться к MySQL.');
  }

  if (FALSE === mysql_select_db($config['mysql']['database'], $mysql_link)) {
    exit('Неудалось подключиться к базе.');
  }

  db_query('SET NAMES utf8', 'Неудалось установить кодировку.');
}

function db_query($query, $error = 'Ошибка в запросе.') {
  global $mysql_link;

  return mysql_query($query);
}

function db_prepere($query, $placeholders = array()) {
  foreach ($placeholders as $key => $value) {
    if (in_array($value, array('NULL'))) {
      $placeholders[$key] = $value;
    }
    else {
      $placeholders[$key] = '"' . mysql_escape_string($value) . '"';
    }
  }

  $query = str_replace(array_keys($placeholders), array_values($placeholders), $query);

  return db_query($query);
}

function db_fetch($query, $placeholders = array()) {
  $data = array();
  $result = db_prepere($query, $placeholders);

  while ($row = mysql_fetch_assoc($result)) {
    $data[] = $row;
  }

  return $data;
}
