<?php

namespace Platform\Customer\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Platform\Customer\Enums\HazardCategory;
use Platform\Customer\Enums\HazardRisk;
use Platform\Customer\Enums\HazardStatus;
use Platform\Customer\Enums\MeasureType;

/**
 * Hazard — einzelne Gefährdung einer Beurteilung.
 *
 * `catalog()` verweist optional per morphMap auf die empfohlene Vorsorge (Katalog-Eintrag).
 *
 * @ai.description Einzelne Gefährdung mit Maßnahme/Frist und optionaler Katalog-Empfehlung.
 */
class Hazard extends Model
{
    protected $table = 'customer_hazards';

    protected $fillable = [
        'team_id',
        'risk_assessment_id',
        'category',
        'description',
        'risk',
        'probability',
        'severity',
        'measures',
        'measure_type',
        'responsible',
        'deadline',
        'status',
        'effectiveness_checked_at',
        'effective',
        'catalog_type',
        'catalog_id',
        'care_type',
    ];

    protected $casts = [
        'category'                 => HazardCategory::class,
        'risk'                     => HazardRisk::class,
        'probability'              => 'integer',
        'severity'                 => 'integer',
        'measure_type'             => MeasureType::class,
        'status'                   => HazardStatus::class,
        'deadline'                 => 'date',
        'effectiveness_checked_at' => 'date',
        'effective'                => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->team_id) && auth()->check()) {
                $model->team_id = auth()->user()->currentTeam?->id;
            }
        });

        // Ampel/Risikoklasse aus der Matrix ableiten (Nohl 5×5 → Gering/Mittel/Hoch).
        static::saving(function (self $model) {
            $class = $model->riskClassFromMatrix();
            if ($class) {
                $model->risk = $class->value;
            }
        });
    }

    /** Risikoprioritätszahl (RPZ) = Wahrscheinlichkeit × Schwere (1..25), sonst null. */
    public function riskPriorityNumber(): ?int
    {
        return ($this->probability && $this->severity)
            ? (int) $this->probability * (int) $this->severity
            : null;
    }

    /** Ampel-Klasse aus der Matrix: 1–4 gering · 5–12 mittel · 13–25 hoch. */
    public function riskClassFromMatrix(): ?HazardRisk
    {
        $rpz = $this->riskPriorityNumber();
        if ($rpz === null) {
            return null;
        }

        return match (true) {
            $rpz <= 4  => HazardRisk::Low,
            $rpz <= 12 => HazardRisk::Medium,
            default    => HazardRisk::High,
        };
    }

    /** Label der abgeleiteten Vorsorgeart (lokal, ohne occupational-Abhängigkeit). */
    public function careTypeLabel(): ?string
    {
        return match ($this->care_type) {
            'mandatory' => 'Pflichtvorsorge',
            'offered'   => 'Angebotsvorsorge',
            'request'   => 'Wunschvorsorge',
            'follow_up' => 'Nachgehende Vorsorge',
            default     => null,
        };
    }

    /** Anzeige-Meta für die Ampel: Klasse, Label, Farbe (hex), RPZ. */
    public function riskMeta(): ?array
    {
        $class = $this->riskClassFromMatrix() ?? $this->risk;
        if (!$class) {
            return null;
        }

        $color = match ($class) {
            HazardRisk::Low    => '#16a34a',
            HazardRisk::Medium => '#d97706',
            HazardRisk::High   => '#dc2626',
        };

        return [
            'class' => $class->value,
            'label' => $class->label(),
            'color' => $color,
            'rpz'   => $this->riskPriorityNumber(),
        ];
    }

    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where($this->getTable() . '.team_id', $teamId);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', HazardStatus::Done->value);
    }

    public function riskAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class, 'risk_assessment_id');
    }

    public function catalog(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'catalog_type', 'catalog_id');
    }

    public function isOverdue(): bool
    {
        return $this->deadline
            && $this->status !== HazardStatus::Done
            && $this->deadline->lt(Carbon::now()->startOfDay());
    }
}
