<?php

ob_start();

header('Content-Type: text/plain; charset=utf8');

if (version_compare('5.5.0', PHP_VERSION, '<=')) {
  exit('Версия PHP: ' . PHP_VERSION . ' необходимо 5.5.0 и ниже.');
}

define('CURRENT_VERSION', '1.1.beta');

$config = array();
$config_js = array();
$menu = array();
$user = new stdClass();

define('DROOT', __DIR__);

include DROOT . '/config.php';

$config = !empty($config[$_SERVER['HTTP_HOST']]) ? $config[$_SERVER['HTTP_HOST']] : $config['default'];
$request_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

include DROOT . '/tools/common.php';
include DROOT . '/src/menu.php';

bootstrap();

if ($request_path != FALSE) {
  $args = explode('/', $request_path);
  $data = array();
  $item = get_menu();

  if ($item === FALSE) {
    header('HTTP/1.1 404 Not Found');
    exit;
  }

  menu_execute($item);
  
  ob_clean();
  
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Cache-Control: post-check=0, pre-check=0', FALSE);
  header('Pragma: no-cache');
  header('Content-Type: application/javascript; charset=utf8');
  
  exit(json_encode($data));
}

ob_clean();

header('Content-Type: text/html; charset=utf8');

?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf8">
  <title>Версия <?php print CURRENT_VERSION; ?></title>
  <style type="text/css">
    @import "libraries/extjs/css/ext-all.css";
  </style>
  <script type="text/javascript" src="libraries/extjs/js/ext-all.js"></script>
  <script type="text/javascript" src="libraries/extjs/js/ext-lang-ru.js"></script>
  <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php print $config['google_api_key']; ?>&signed_in=true"></script>
  <script type="text/javascript">
    Ext.Loader.setConfig({
      enabled: true
    });

    Ext.Loader.setPath({
      System: 'system'
    });

    Ext.require('System.Main');

    Ext.onReady(function () {
      new System.Main(<?php print json_encode($config_js); ?>);
    });
  </script>
</head>
<body>
</body>
</html>
