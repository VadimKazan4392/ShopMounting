<?php
define('VG_ACCESS', true);


header('Content-type:text\html;charset=utf-8');

session_start();

require_once('config.php');
require_once('core/base/settings/internal_settings.php');

use \core\base\controllers\RouteController;
use core\base\exceptions\DBException;
use \core\base\exceptions\RouteException;

try {
    RouteController::instance()->route();
} 
catch(RouteException $e) {
    exit($e->getMessage());
}
catch(DBException $e) {
    exit($e->getMessage());
}
