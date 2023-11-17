<?php

if (!function_exists('tb_remote_ip')) {

    /**
     * Возвращает адрес IP клиента.
     *
     * @return string
     */
    function tb_remote_ip () {

        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_REAL_IP']))
            return $_SERVER['HTTP_X_REAL_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        if (!empty($_SERVER['REMOTE_ADDR']))
            return $_SERVER['REMOTE_ADDR'];

        return '0.0.0.0';
    }
}
