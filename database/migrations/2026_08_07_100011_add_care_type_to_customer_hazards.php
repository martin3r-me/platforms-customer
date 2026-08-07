<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GBU→Vorsorge-Brücke (Stufe 2a): Art der abgeleiteten Vorsorge je Gefährdung.
 * Der Anlass selbst hängt bereits per morphMap an catalog_type/catalog_id
 * (arbmedvv_occasion); `care_type` = Pflicht/Angebot/Wunsch/nachgehend.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_hazards', function (Blueprint $table) {
            $table->string('care_type', 16)->nullable()->after('catalog_id');
        });
    }

    public function down(): void
    {
        Schema::table('customer_hazards', function (Blueprint $table) {
            $table->dropColumn('care_type');
        });
    }
};
