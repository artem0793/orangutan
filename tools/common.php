<?php

include DROOT . '/tools/mysql.php';
include DROOT . '/tools/menu.php';
include DROOT . '/tools/permissions.php';
include DROOT . '/tools/roles.php';
include DROOT . '/tools/entity.php';
include DROOT . '/tools/user.php';

function bootstrap() {
  global $user;

  session_start();
  db_connect();
  install();
  permission_load();

  if (!empty($_SESSION['uid'])) {
    $user = user_load($_SESSION['uid']);
  }
  else {
    $user->roles = array(1 => 1);
  }
}

function install() {
  db_query('DROP TABLE IF EXISTS `permissions`');
  db_query('CREATE TABLE `permissions` (`rid` int NOT NULL, `type` varchar(255) CHARACTER SET utf8 NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8');
  db_query('INSERT INTO `permissions` (`rid`, `type`) VALUES (\'1\', \'user anonymous\')');
  db_query('INSERT INTO `permissions` (`rid`, `type`) VALUES (\'2\', \'user authorized\')');

  db_query('DROP TABLE IF EXISTS `roles`');
  db_query('CREATE TABLE `roles` (`rid` int NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` varchar(255) CHARACTER SET utf8 NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8');
  db_query('INSERT INTO `roles` (`rid`, `name`) VALUES (\'1\', \'Aнонимный\')');
  db_query('INSERT INTO `roles` (`rid`, `name`) VALUES (\'2\', \'Авторизованный\')');
  db_query('INSERT INTO `roles` (`rid`, `name`) VALUES (\'3\', \'Редактор\')');

  entity_create('users');

  $admin = (object) array(
    'name' => array('Artem'),
    'mail' => array('artem_07_93@mail.ru'),
    'password' => array('123456'),
  );

  user_save($admin);

  $editor = (object) array(
    'name' => array('Редактор Стас'),
    'mail' => array('editor@mail.com'),
    'password' => array('654321'),
    'roles' => array(
      2 => 2,
      3 => 3,
    ),
  );

  user_save($editor);
}
