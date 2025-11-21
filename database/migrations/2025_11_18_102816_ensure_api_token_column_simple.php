<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Если столбец не существует - создаем его
        if (!Schema::hasColumn('users', 'api_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('api_token', 80)->unique()->nullable()->after('password');
            });
        }
        
        // Если столбец существует, но не уникальный - нам нужно удалить и создать заново
        // Но это сложно без Doctrine DBAL, поэтому просто убедимся что он существует
    }

    public function down()
    {
        // Не удаляем столбец при откате, чтобы не потерять данные
    }
};