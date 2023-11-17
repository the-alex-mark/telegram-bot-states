<?php

namespace ProgLib\Telegram\Bot\State;

class State {

    #region Properties

    /**
     * @var string Имя
     */
    protected $name;

    /**
     * @var array Список фильтров
     */
    protected $filters = [];

    /**
     * @var array Список шагов
     */
    protected $callbacks;

    #endregion

    /**
     * ...
     */
    public function execute() {


        call_user_func($this->callbacks);
    }
}
