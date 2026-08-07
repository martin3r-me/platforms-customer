<?php

namespace Platform\Customer\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Uid\UuidV7;
use Platform\Customer\Enums\AssessmentStatus;

/**
 * RiskAssessment — Gefährdungsbeurteilung je Betrieb/Arbeitsbereich (§5/6 ArbSchG).
 *
 * Betrieb-verankert (organization_entity_id, lose): der Anker des customer-Moduls.
 * Hängt beim Speichern automatisch per dimension_link am external_customer/an einer
 * Abteilung (Anker-Prinzip). occupational referenziert die Gefährdung lose.
 *
 * @ai.description Gefährdungsbeurteilung eines Betriebs/Arbeitsbereichs.
 */
class RiskAssessment extends Model
{
    protected $table = 'customer_risk_assessments';

    protected $fillable = [
        'uuid',
        'team_id',
        'organization_entity_id',
        'title',
        'work_area',
        'assessed_on',
        'next_review',
        'status',
        'version',
        'closed_at',
        'content',
        'created_by_user_id',
    ];

    protected $casts = [
        'assessed_on' => 'date',
        'next_review' => 'date',
        'status'      => AssessmentStatus::class,
        'closed_at'   => 'datetime',
        'content'     => 'array',
    ];

    /** Abgeschlossen = revisionssicher, read-only. */
    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                do {
                    $uuid = (string) UuidV7::generate();
                } while (self::where('uuid', $uuid)->exists());
                $model->uuid = $uuid;
            }

            if (empty($model->team_id) && auth()->check()) {
                $model->team_id = auth()->user()->currentTeam?->id;
            }

            if (empty($model->status)) {
                $model->status = AssessmentStatus::Draft->value;
            }
        });

        static::saved(function (self $model) {
            if ($model->wasRecentlyCreated || $model->wasChanged('organization_entity_id')) {
                \Platform\Customer\Support\OrganizationLink::sync(
                    'customer_risk_assessment',
                    (int) $model->id,
                    $model->organization_entity_id ? (int) $model->organization_entity_id : null,
                    $model->team_id ? (int) $model->team_id : null,
                    auth()->id(),
                );
            }
        });
    }

    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where($this->getTable() . '.team_id', $teamId);
    }

    public function hazards(): HasMany
    {
        return $this->hasMany(Hazard::class, 'risk_assessment_id');
    }

    /**
     * Betrieb = Org-Entity (lose, kein DB-FK).
     */
    public function organizationEntity(): BelongsTo
    {
        return $this->belongsTo(\Platform\Organization\Models\OrganizationEntity::class, 'organization_entity_id');
    }
}
