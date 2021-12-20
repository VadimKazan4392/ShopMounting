<?php

namespace core\base\controllers;

trait BaseMethods
{
    protected function clearStr($str)
    {
        if(is_array($str)) {
            foreach($str as $key => $value) {
                $str[$key] = trim(strip_tags($value));
                return $str;
            }
        } else {
            trim(strip_tags($str));
        }
    }

    protected function clearNum($num)
    {
        return $num * 1;
    }

    protected function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }

    protected function isAjax()
    {
        return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] === 'XMLHttpRequest';
    }

    protected function redirect($http = false, $code = false)
    {
        if($code) {
            $codes = ['301' => 'HTTP/1.1 301 Move Premanently'];

            if($code[$code]) {
                header($codes[$code]);
            }
        }
        if($http) {
            $redirect = $http;
        } else {
            $redirect = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : PATH;
        }
        header("Location: $redirect");
        exit;
    }

    protected function writeLog($message, $file = 'log.txt', $event = 'Fault')
    {
        $dateTime = new \DateTime();

        $str = $event . ':' . $dateTime->format('d-m-y G:i:s') . '-' . $message . "\r\n";

        $a = file_put_contents('log/' . $file, $str, FILE_APPEND);
        
        exit($message);
    }
}