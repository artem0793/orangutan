<?php

$menu['user/login'] = array(
  'file' => 'user.php',
  'callback' => 'user_authorize',
  'args' => array(),
  'access' => 'user anonymous'
);

$menu['user/logout'] = array(
  'file' => 'user.php',
  'callback' => 'user_logout',
  'args' => array(),
  'access' => 'user authorized'
);

$menu['components/permission'] = array(
  'file' => 'permission.php',
  'jsonData' => TRUE,
  'callback' => 'component_permission',
  'args' => array(),
  'access' => 'user anonymous'
);
