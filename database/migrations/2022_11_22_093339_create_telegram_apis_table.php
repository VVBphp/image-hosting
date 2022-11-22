<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('telegram_apis', function (Blueprint $table) {
            $table->string('username')->default(null)->nullable();
            $table->string('api_token')->primary();
        });
    }

    public function down()
    {
        Schema::dropIfExists('telegram_apis');
    }
};
