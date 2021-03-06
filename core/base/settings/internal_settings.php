<?php
defined('VG_ACCESS') or die('Access_denied');

const TEMPLATE = 'templates/default/';
const ADMIN_TAMPLATE = 'core/admin/views/';

const COOKIE_VSERSION = '1.0.0';
const CRYPT_KEY = '';
const COOKIE_TIME = 60;
const BLOCK_TIME = 3;

const OTY = 8;
const OTY_LINKS = 3;

const ADMIN_CSS_JS = [
    'styles' => [],
    'scripts' => []
];

const USER_CSS_JS = [
    'styles' => [],
    'scripts' => []
];

use \core\base\exceptions\RouteException;

function autoloadMainClasses($class_name) {
    $class_name = str_replace('\\', '/', $class_name);

    if(!include_once($class_name.'.php')) {
        throw new RouteException('Не верное имя класса - '.$class_name);
    }
}

spl_autoload_register('autoloadMainClasses');