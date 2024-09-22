<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('icalUID')->nullable()->after('hours');
            $table->renameColumn('hours', 'minutes');
        });

        // Update existing records to convert hours to minutes
        \DB::table('tasks')->update(['minutes' => \DB::raw('minutes * 60')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert minutes back to hours
        \DB::table('tasks')->update(['minutes' => \DB::raw('minutes / 60')]);

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('icalUID');
            $table->renameColumn('minutes', 'hours');
        });
    }
}
