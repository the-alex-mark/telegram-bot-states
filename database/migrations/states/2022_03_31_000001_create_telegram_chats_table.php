<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateTelegramChatsTable extends Migration {

    #region Properties

    /**
     * @var string Наименование таблицы
     */
    protected $table = 'telegram_chats';

    #endregion

    /**
     * Применяет изменения в базе данных.
     *
     * @return void
     */
    public function up() {

        // Создание таблицы
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('Идентификатор чата');
            $table->string('username', 255)->nullable()->comment('Имя');
            $table->string('type', 255)->nullable(false)->comment('Тип');
            $table->longText('extra')->nullable(true)->comment('Дополнительная информация');

            // Добавление временных меток
            $table->timestamps();
        });
    }

    /**
     * Выполняет откат изменений.
     *
     * @return void
     */
    public function down() {

        // Удаление таблицы
        Schema::dropIfExists($this->table);
    }
}
