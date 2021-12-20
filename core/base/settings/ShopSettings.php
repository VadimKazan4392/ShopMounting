<?php
namespace core\base\settings;

use core\base\controllers\Singletone;
use core\base\settings\Settings;

class ShopSettings
{
    use Singletone;

    private  $baseSettings;
    
    private  $templateArr = [
        'text' => ['price', 'start'],
        'textarea' => ['woods']
    ];

    private  $routes = [
        'plugins' => [
            'path' => 'core/plugins/',
            'hrUrl' => false,
            'dir' => 'controller',
            'routes' => [
                'product' => 'goods'
            ]
        ],
    ];


    static public function get($property)
    {
        return self::getInstance()->$property;
    }

    static private function getInstance()
    {
        if(self::$_instance instanceof self) {
            return self::$_instance;

        }

        self::instance()->baseSettings = Settings::instance();
        $baseProperties = self::$_instance->baseSettings->clueProperties(get_class());
        self::$_instance->setProperty($baseProperties);

        return self::$_instance;
    }

    protected function setProperty($properties)
    {
        if($properties) {
            foreach ($properties as $name => $property) {
                $this->$name = $property;
            }
        }
    }
}