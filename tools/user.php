<?php

function users_fields() {
  return array(
    'name' => array(
      'type' => 'text',
    ),
    'mail' => array(
      'type' => 'text',
    ),
    'password' => array(
      'type' => 'text',
    ),
    'roles' => array(
      'type' => 'int',
    ),
  );
}

function user_load($uid) {
  return entity_load('users', $uid);
}

function user_save(&$user) {
  entity_save('users', $user);
}

function user_delete(&$user) {
  entity_delete('users', $user);
}

function user_authorize() {

}

function user_logout() {

}
