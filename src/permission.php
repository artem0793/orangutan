<?php

function component_permission($json_data) {
  global $data;

  if (!empty($json_data['permissions']) && is_array($json_data['permissions'])) {
    $data['permissions'] = array();

    foreach ($json_data['permissions'] as $type) {
      $data['permissions'][$type] = permission($type);
    }
  }
}
