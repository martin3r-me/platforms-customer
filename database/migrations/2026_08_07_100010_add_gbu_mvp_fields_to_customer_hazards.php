<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GBU-MVP: Risikomatrix (Wahrscheinlichkeit × Schwere) + STOP-Maßnahmenkategorie
 * an der einzelnen Gefährdung. Das bisherige `risk` (Gering/Mittel/Hoch) wird aus
 * der Matrix abgeleitet (Nohl, 5×5 → Ampel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_hazards', function (Blueprint $table) {
            $table->unsignedTinyInteger('probability')->nullable()->after('risk'); // 1..5 Eintrittswahrscheinlichkeit
            $table->unsignedTinyInteger('severity')->nullable()->after('probability'); // 1..5 Schadensschwere
            $table->string('measure_type', 24)->nullable()->after('measures'); // STOP: substitution|technical|organizational|personal
        });
    }

    public function down(): void
    {
        Schema::table('customer_hazards', function (Blueprint $table) {
            $table->dropColumn(['probability', 'severity', 'measure_type']);
        });
    }
};
