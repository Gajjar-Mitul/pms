<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->nullable()->unsigned();
            $table->string('name')->nullable();
            $table->string('amount')->nullable();
            $table->text('description')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('payment_status', ['pending','complate'])->default('pending');
            $table->enum('status', ['pending','w.i.p.','complate'])->default('pending');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_milestones');
    }
}
