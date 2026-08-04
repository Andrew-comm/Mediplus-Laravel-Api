<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('stock_movements', function (Blueprint $table) {

            $table->string('direction')
                ->nullable()
                ->after('type');

        });



        // Existing records
        DB::table('stock_movements')
            ->whereNull('direction')
            ->update([
                'direction'=>'IN'
            ]);



        Schema::table('stock_movements', function (Blueprint $table) {

            $table->string('direction')
                ->nullable(false)
                ->change();

        });

    }



    public function down(): void
    {

        Schema::table('stock_movements', function (Blueprint $table) {

            $table->dropColumn('direction');

        });

    }

};
