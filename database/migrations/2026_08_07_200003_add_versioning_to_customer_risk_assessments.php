<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GBU Stufe 4 — Revisionssicherheit: Version + Abschluss-Zeitpunkt + eingefrorener
 * Snapshot (content). Eine abgeschlossene GBU ist read-only; der Snapshot dokumentiert
 * den Stand nach §6 ArbSchG.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_risk_assessments', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('status');
            $table->timestamp('closed_at')->nullable()->after('version');
            $table->json('content')->nullable()->after('closed_at');
        });
    }

    public function down(): void
    {
        Schema::table('customer_risk_assessments', function (Blueprint $table) {
            $table->dropColumn(['version', 'closed_at', 'content']);
        });
    }
};
