<?php

use App\Models\Counter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('prefix');
            $table->string('value');
            $table->timestamps();
        });

        Counter::create([
            'key' => 'invoice',
            'prefix' => 'INV-',
            'value' => 10000
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counters');
    }
}
