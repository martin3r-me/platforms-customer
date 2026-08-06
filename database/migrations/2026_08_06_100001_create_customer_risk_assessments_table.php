<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * customer_risk_assessments — Gefährdungsbeurteilung je Betrieb/Arbeitsbereich (§5/6 ArbSchG).
 * organization_entity_id ist eine LOSE Referenz auf die Betrieb-Org-Entity (Anker-Prinzip).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('organization_entity_id')->index(); // lose → Betrieb-Entity

            $table->string('title')->nullable();
            $table->string('work_area')->nullable();      // Arbeitsbereich
            $table->date('assessed_on')->nullable();      // Stand-Datum
            $table->date('next_review')->nullable();      // nächste Überprüfung
            $table->string('status', 32)->default('draft'); // AssessmentStatus
            $table->unsignedBigInteger('created_by_user_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_risk_assessments');
    }
};
