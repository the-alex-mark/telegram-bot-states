<?php

use ProgLib\Telegram\Bot\Models\TelegramChat;
use Telegram\Bot\Api;

if (!function_exists('tb_api')) {

    /**
     * Возвращает текущего авторизованный API чат-бота.
     *
     * @return Api
     */
    function tb_api() {
        return request()->{'telegram'}();
    }
}

if (!function_exists('tb_chat')) {

    /**
     * Возвращает текущего авторизованный чат.
     *
     * @return TelegramChat
     */
    function tb_chat() {
        return request()->user();
    }
}
