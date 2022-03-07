<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelegramTelegramChatsTable extends Migration {

    #region Properties

    /**
     * @var string Наименование таблицы
     */
    public $table = 'telegram_chats';

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
            $table->bigInteger('chat_id')->nullable(false)->comment('Идентификатор чата');
            $table->string('chat_type', 255)->nullable(false)->comment('Тип чата');
            $table->longText('chat_data')->nullable()->comment('Пользовательская информация');
        });

        // Индексация полей
        Schema::table($this->table, function (Blueprint $table) {
            $table->index('chat_id');
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
            $table->dropIndex($this->getIndexByColumnName('chat_id'));
        });

        // Удаление таблицы
        Schema::dropIfExists($this->table);
    }
}
