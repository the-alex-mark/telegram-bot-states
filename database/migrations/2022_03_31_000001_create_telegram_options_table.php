<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use ProgLib\Telegram\Database\Migrations\Migration;

class CreateTelegramOptionsTable extends Migration {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $table = 'telegram_options';

    #endregion

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        // Создание таблицы
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('id', true, true);
            $table->string('name', 191)->nullable(false)->comment('Имя');
            $table->longText('value')->nullable(false)->comment('Значение');
        });

        // Индексация полей
        Schema::table($this->table, function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        // Удаление индексируемых полей
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex($this->getIndexByColumnName('name'));
        });

        // Удаление таблицы
        Schema::dropIfExists($this->table);
    }
}
