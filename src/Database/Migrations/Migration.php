<?php

namespace ProgLib\Telegram\Database\Migrations;

use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration {

    #region Properties

    /**
     * @var string Наименование таблицы
     */
    protected $table = null;

    #endregion

    #region Helpers

    /**
     * Возвращает строку с именем индекса для указанного столбца.
     *
     * @param  string $column Имя столбца.
     * @return string
     */
    public function getIndexByColumnName($column) {
        return "{$this->table}_{$column}_index";
    }

    /**
     * Возвращает строку с именем индекса для указанного столбца.
     *
     * @param  string $column Имя столбца.
     * @return string
     */
    public function getUniqueByColumnName($column) {
        return "{$this->table}_{$column}_unique";
    }

    /**
     * Возвращает строку с именем индекса для указанного столбца.
     *
     * @param  string $column Имя столбца.
     * @return string
     */
    public function getForeignByColumnName($column) {
        return "{$this->table}_{$column}_foreign";
    }

    #endregion

    /**
     * Применяет изменения в базе данных.
     *
     * @return void
     */
    abstract function up();

    /**
     * Выполняет откат изменений.
     *
     * @return void
     */
    abstract function down();
}
