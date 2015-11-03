<?php

function get_menu() {
  global $menu, $request_path;

  foreach ($menu as $path => $item) {
    if (preg_match('/^' . str_replace(array('%s', '%d', '/'), array('\w+', '\d+', '\/'), $path) . '$/i', $request_path)) {
      return $item;
    }
  }

  return FALSE;
}

function arg($index = NULL) {
  global $args;

  if ($index !== NULL) {
    return $args[$index];
  }

  return $args;
}

function menu_execute($menu) {
  $arguments = array();

  if (permission($menu['access'])) {
    if (!empty($menu['file'])) {
      include DROOT . '/src/' . $menu['file'];
    }

    if (isset($menu['args'])) {
      foreach ($menu['args'] as $key) {
        $arguments[] = arg($key);
      }
    }

    if (isset($menu['jsonData'])) {
      $request_body = file_get_contents('php://input');

      if (!empty($request_body)) {
        array_unshift($arguments, json_decode($request_body, TRUE));
      }
    }

    call_user_func_array($menu['callback'], $arguments);
  }
  else {
    header('HTTP/1.1 403 Forbidden');
  }
}
