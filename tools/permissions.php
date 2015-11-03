<?php

function permission_load() {
  global $permission;

  foreach (db_fetch('SELECT rid, type FROM `permissions`') as $row) {
    if (!isset($permission[$row['type']])) {
      $permission[$row['type']] = array();
    }

    $permission[$row['type']][$row['rid']] = TRUE;
  }
}

function permission_add($type, $rid) {
  global $permission;

  if (empty($permission[$type][$rid])) {
    $permission[$type][$rid] = TRUE;

    db_query('INSERT INTO `permissions` (`rid`, `type`) VALUES (:rid, :type)', array(
      ':rid' => $rid,
      ':type' => $type,
    ));
  }
}

function permission_remove($type, $rid) {
  global $permission;

  if (!empty($permission[$type][$rid])) {
    unset($permission[$type][$rid]);

    db_query('DELETE FROM `permissions` WHERE `rid` = :rid AND `type` = :type', array(
      ':rid' => $rid,
      ':type' => $type,
    ));
  }
}

function permission($type, $account = NULL) {
  global $permission, $user;

  if (empty($account)) {
    $account = &$user;
  }

  if ($account->uid === 1) {
    return TRUE;
  }

  return count(array_intersect_key((array) $permission[$type], $account->roles)) > 0;
}
