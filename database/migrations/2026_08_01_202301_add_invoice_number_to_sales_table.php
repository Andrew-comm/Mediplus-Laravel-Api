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
    Schema::table('sales', function (Blueprint $table) {

        $table->string('invoice_number')
              ->nullable()
              ->unique()
              ->after('id');

    });


    // Populate existing sales
    $sales = \App\Models\Sale::all();


    foreach($sales as $index=>$sale){

        $sale->update([

            'invoice_number'=>
            'INV-'
            .str_pad(
                $index + 1,
                5,
                '0',
                STR_PAD_LEFT
            )

        ]);

    }


    Schema::table('sales', function (Blueprint $table) {

        $table->string('invoice_number')
              ->nullable(false)
              ->change();

    });
}


public function down(): void
{

    Schema::table('sales', function (Blueprint $table) {

        $table->dropColumn('invoice_number');

    });

}
};
