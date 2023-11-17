<?php

namespace ProgLib\Telegram\Bot\Routing\Resolvers;

use Closure;
use Illuminate\Http\Request;

/**
 * ...
 *
 * @mixin Request
 */
trait ApiRequestResolver {

    #region Properties

    protected $telegramChatResolver = null;

    #endregion

    /**
     * Устанавливает реализацию для авторизации «<b>Telegram API</b>».
     *
     * @return Closure
     */
    public function setChatResolver() {

        /**
         * Устанавливает реализацию для авторизации «<b>Telegram API</b>».
         *
         * @param  Closure $callback
         * @return $this
         */
        return function (Closure $callback) {
            $this->telegramChatResolver = $callback;

            return $this;
        };
    }

    /**
     * Возвращает реализацию для авторизации «<b>Telegram API</b>».
     *
     * @return Closure
     */
    public function getChatResolver() {

        /**
         * Возвращает реализацию для авторизации «<b>Telegram API</b>».
         *
         * @return Closure
         */
        return function () {
            return $this->telegramChatResolver ?: function () { };
        };
    }

    /**
     * Возвращает авторизованного клиента «<b>Telegram API</b>».
     *
     * @return Closure
     */
    public function chat() {

        /**
         * Возвращает авторизованного клиента «<b>Telegram API</b>».
         *
         * @return mixed
         */
        return function () {
            return call_user_func($this->getChatResolver());
        };
    }
}
