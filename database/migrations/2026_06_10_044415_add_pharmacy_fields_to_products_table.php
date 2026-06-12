<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('generic_name')->nullable()->after('name');
            $table->string('strength')->nullable()->after('generic_name');
            $table->string('dosage_form')->nullable()->after('strength');
            $table->string('registration_no')->nullable()->after('dosage_form');
            $table->text('drap_details')->nullable()->after('registration_no');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['generic_name', 'strength', 'dosage_form', 'registration_no', 'drap_details']);
        });
    }
};
