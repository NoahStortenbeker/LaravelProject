<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_line')->nullable()->after('date_of_birth');
            $table->string('zip_code', 16)->nullable()->after('address_line');
            $table->string('city', 128)->nullable()->after('zip_code');
            $table->string('country', 128)->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address_line', 'zip_code', 'city', 'country']);
        });
    }
};
