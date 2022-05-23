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
     * @inheritDoc
     */
    public function up() {

        // Создание таблицы
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('Идентификатор чата');
            $table->string('username', 255)->nullable()->comment('Имя пользователя');
            $table->string('type', 255)->nullable(false)->comment('Тип');

            // Добавление временных меток
            $table->timestamps();
        });
    }

    /**
     * @inheritDoc
     */
    public function down() {

        // Удаление таблицы
        Schema::dropIfExists($this->table);
    }
}
