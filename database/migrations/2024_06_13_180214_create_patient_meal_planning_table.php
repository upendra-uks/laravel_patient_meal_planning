<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_meal_planning', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->date('planned_date');
            $table->decimal('total_calories', 10, 2);
            $table->decimal('total_fats', 10, 2);
            $table->decimal('total_carbs', 10, 2);
            $table->decimal('total_proteins', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable()->onUpdate(now());
            //unique constraint - (patient_id, planned_date)
            $table->unique(['patient_id', 'planned_date'], 'patient_planned_unique');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_meal_planning');
    }
};
