<?php

namespace ProgLib\Telegram\Bot\Routing;

use Closure;
use Illuminate\Http\Request;

/**
 * Представляет методы регистрации новых методов «Request».
 *
 * @mixin Request
 */
class TelegramBotRequestMethods {

    #region Properties

    protected $telegramResolver = null;

    #endregion

    /**
     * Устанавливает реализацию для авторизации «<b>Telegram API</b>».
     *
     * @return Closure
     */
    public function setTelegramResolver() {

        /**
         * Устанавливает реализацию для авторизации «<b>Telegram API</b>».
         *
         * @param  Closure $callback
         * @return $this
         */
        return function (Closure $callback) {
            $this->telegramResolver = $callback;

            return $this;
        };
    }

    /**
     * Возвращает реализацию для авторизации «<b>Telegram API</b>».
     *
     * @return Closure
     */
    public function getTelegramResolver() {

        /**
         * Возвращает реализацию для авторизации «<b>Telegram API</b>».
         *
         * @return Closure
         */
        return function () {
            return $this->telegramResolver ?: function () { };
        };
    }

    /**
     * Возвращает авторизованного клиента «<b>Telegram API</b>».
     *
     * @return Closure
     */
    public function telegram() {

        /**
         * Возвращает авторизованного клиента «<b>Telegram API</b>».
         *
         * @return mixed
         */
        return function () {
            return call_user_func($this->getTelegramResolver());
        };
    }
}
