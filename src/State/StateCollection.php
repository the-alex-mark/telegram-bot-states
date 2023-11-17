<?php

namespace ProgLib\Telegram\Bot\State;

use ArrayIterator;
use Countable;
use Illuminate\Support\Arr;
use IteratorAggregate;

class StateCollection implements Countable, IteratorAggregate {

    #region Properties

    /**
     * @var array ...
     */
    protected $states = [];

    /**
     * @var array ...
     */
    protected $allStates = [];

    /**
     * @var array ...
     */
    protected $nameList = [];

    /**
     * @var array ...
     */
    protected $actionList = [];

    #endregion

    /**
     * ...
     *
     * @param  string|null  $method
     * @return array
     */
    public function get($method = null) {
        return is_null($method) ? $this->getStates() : Arr::get($this->states, $method, []);
    }

    /**
     * ...
     *
     * @param  string  $name
     * @return bool
     */
    public function hasNamedState($name) {
        return !is_null($this->getByName($name));
    }

    /**
     * ...
     *
     * @param  string  $name
     * @return mixed|null
     */
    public function getByName($name) {
        return $this->nameList[$name] ?? null;
    }

    /**
     * ...
     *
     * @param  string  $action
     * @return mixed|null
     */
    public function getByAction($action) {
        return $this->actionList[$action] ?? null;
    }

    /**
     * ...
     *
     * @return array
     */
    public function getStates() {
        return array_values($this->allStates);
    }

    /**
     * ...
     *
     * @return array
     */
    public function getRoutesByMethod() {
        return $this->states;
    }

    /**
     * ...
     *
     * @return array
     */
    public function getRoutesByName() {
        return $this->nameList;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->getStates());
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->getStates());
    }
}
