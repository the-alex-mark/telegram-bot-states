<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use ProgLib\Telegram\Database\Migrations\Migration;

class CreateTelegramChatsTable extends Migration {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $table = 'telegram_chats';

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
            $table->longText('chat_cache')->nullable()->comment('Буфер');

            // Добавление временных меток
            $table->timestamps();
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
