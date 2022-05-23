<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ProgLib\Telegram\Database\Migrations\Migration;

class CreateCacheTable extends Migration {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $table = 'telegram_cache';

    #endregion

    /**
     * @inheritDoc
     */
    public function up() {

        // Создание таблицы
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('key')->primary()->comment('Ключ');
            $table->mediumText('value')->comment('Значение');
            $table->integer('expiration')->comment('Время жизни');
        });

        // Создание таблицы для блокировки
        Schema::create($this->table . '_locks', function (Blueprint $table) {
            $table->string('key')->comment('Ключ');
            $table->string('owner')->comment('Владелец');
            $table->integer('expiration')->comment('Время жизни');
        });
    }

    /**
     * @inheritDoc
     */
    public function down() {

        // Удаление таблицы
        Schema::dropIfExists($this->table);

        // Удаление таблицы для блокировки
        Schema::dropIfExists($this->table . '_locks');
    }
}
