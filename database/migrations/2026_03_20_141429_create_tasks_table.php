<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();//mieux que id
            $table->string('nom');
            $table->string('task');
            $table->date('date');
            $table->boolean('done')->default(false);
            $table->timestamps();//toujours laisser car ca permet d'avoir une trace de quand est ce qu'on a créé la table
            //ca va rajouter 2 champs à notre table : created_at et updated at 
        });

        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
