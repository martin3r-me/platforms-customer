<?php

namespace Platform\Customer\Livewire\RiskAssessment;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Customer\Enums\HazardCategory;
use Platform\Customer\Enums\HazardStatus;
use Platform\Customer\Enums\MeasureType;
use Platform\Customer\Models\Hazard;
use Platform\Customer\Models\RiskAssessment;
use Platform\Customer\Support\HazardCatalog;

/**
 * Gefährdungsbeurteilung-Detail — Stammdaten + strukturierte Erfassung einzelner
 * Gefährdungen: Faktor (GDA-Katalog) → Risikomatrix (Ampel) → STOP-Maßnahme.
 */
class Show extends Component
{
    #[Locked]
    public int $assessmentId;

    public array $newHazard = [
        'category'     => 'mechanical',
        'description'  => '',
        'probability'  => 3,
        'severity'     => 3,
        'measure_type' => 'technical',
        'measures'     => '',
        'responsible'  => '',
        'deadline'     => null,
        'occasion_id'  => '',   // arbmedvv-Anlass (empfohlene Vorsorge)
        'care_type'    => '',   // Pflicht/Angebot/Wunsch
    ];

    public function mount(int $riskAssessment): void
    {
        $this->assessmentId = $this->resolve($riskAssessment)->id;
    }

    protected function resolve(int $id): RiskAssessment
    {
        $team = (int) Auth::user()->currentTeam->id;

        return RiskAssessment::query()->forTeam($team)->findOrFail($id);
    }

    public function addHazard(): void
    {
        $data = $this->validate([
            'newHazard.category'     => ['required', 'string', 'in:' . implode(',', array_column(HazardCategory::cases(), 'value'))],
            'newHazard.description'  => ['required', 'string', 'max:2000'],
            'newHazard.probability'  => ['required', 'integer', 'between:1,5'],
            'newHazard.severity'     => ['required', 'integer', 'between:1,5'],
            'newHazard.measure_type' => ['nullable', 'string', 'in:' . implode(',', array_column(MeasureType::cases(), 'value'))],
            'newHazard.measures'     => ['nullable', 'string', 'max:2000'],
            'newHazard.responsible'  => ['nullable', 'string', 'max:191'],
            'newHazard.deadline'     => ['nullable', 'date'],
            'newHazard.occasion_id'  => ['nullable', 'integer'],
            'newHazard.care_type'    => ['nullable', 'string', 'in:mandatory,offered,request,follow_up'],
        ])['newHazard'];

        $assessment = $this->resolve($this->assessmentId);

        $occasionId = $data['occasion_id'] ?: null;

        Hazard::create([
            'team_id'            => (int) $assessment->team_id,
            'risk_assessment_id' => $assessment->id,
            'category'           => $data['category'],
            'description'        => $data['description'],
            'probability'        => $data['probability'],
            'severity'           => $data['severity'],
            'measure_type'       => $data['measure_type'] ?: null,
            'measures'           => $data['measures'] ?: null,
            'responsible'        => $data['responsible'] ?: null,
            'deadline'           => $data['deadline'] ?: null,
            'status'             => HazardStatus::Open->value,
            // GBU→Vorsorge-Brücke: empfohlener Anlass (arbmedvv) + Art
            'catalog_type'       => $occasionId ? 'arbmedvv_occasion' : null,
            'catalog_id'         => $occasionId,
            'care_type'          => $occasionId ? ($data['care_type'] ?: 'mandatory') : null,
        ]);

        $this->newHazard['description'] = '';
        $this->newHazard['measures'] = '';
        $this->newHazard['responsible'] = '';
        $this->newHazard['deadline'] = null;
        $this->newHazard['occasion_id'] = '';

        $this->dispatch('toast', message: 'Gefährdung erfasst.', type: 'success');
    }

    public function removeHazard(int $hazardId): void
    {
        $team = (int) Auth::user()->currentTeam->id;
        Hazard::query()->forTeam($team)
            ->where('risk_assessment_id', $this->assessmentId)
            ->whereKey($hazardId)->delete();
    }

    /** Status weiterschalten: offen → in Umsetzung → erledigt → offen. */
    public function cycleStatus(int $hazardId): void
    {
        $team = (int) Auth::user()->currentTeam->id;
        $hazard = Hazard::query()->forTeam($team)
            ->where('risk_assessment_id', $this->assessmentId)
            ->whereKey($hazardId)->first();

        if (!$hazard) {
            return;
        }

        $hazard->status = match ($hazard->status) {
            HazardStatus::Open       => HazardStatus::InProgress,
            HazardStatus::InProgress => HazardStatus::Done,
            default                  => HazardStatus::Open,
        };
        $hazard->save();
    }

    public function render()
    {
        $team = (int) Auth::user()->currentTeam->id;

        $assessment = $this->resolve($this->assessmentId)
            ->load(['organizationEntity', 'hazards.catalog']);

        // Anlass-Katalog (arbmedvv) guarded — customer bleibt ohne harte Abhängigkeit.
        $occasionOptions = ['' => '— keine —'];
        if (class_exists(\Platform\Arbmedvv\Models\Occasion::class)) {
            foreach (\Platform\Arbmedvv\Models\Occasion::query()->where('team_id', $team)->orderBy('title')->get() as $o) {
                $occasionOptions[$o->id] = $o->title;
            }
        }

        return view('customer::livewire.risk-assessment.show', [
            'assessment'      => $assessment,
            'categoryOptions' => collect(HazardCategory::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all(),
            'measureOptions'  => collect(MeasureType::cases())->mapWithKeys(fn ($m) => [$m->value => $m->short() . ' · ' . $m->label()])->all(),
            'careTypeOptions' => ['' => '—', 'mandatory' => 'Pflichtvorsorge', 'offered' => 'Angebotsvorsorge', 'request' => 'Wunschvorsorge', 'follow_up' => 'Nachgehende Vorsorge'],
            'occasionOptions' => $occasionOptions,
            'catalog'         => HazardCatalog::all(),
        ])->layout('platform::layouts.app');
    }
}
