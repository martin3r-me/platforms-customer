<?php

namespace Platform\Customer\Livewire\RiskAssessment;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Customer\Models\RiskAssessment;

/**
 * Gefährdungsbeurteilung-Detail — Stammdaten + einzelne Gefährdungen (Hazards).
 */
class Show extends Component
{
    #[Locked]
    public int $assessmentId;

    public function mount(int $riskAssessment): void
    {
        $this->assessmentId = $this->resolve($riskAssessment)->id;
    }

    protected function resolve(int $id): RiskAssessment
    {
        $team = (int) Auth::user()->currentTeam->id;

        return RiskAssessment::query()->forTeam($team)->findOrFail($id);
    }

    public function render()
    {
        $assessment = $this->resolve($this->assessmentId)
            ->load(['organizationEntity', 'hazards']);

        return view('customer::livewire.risk-assessment.show', [
            'assessment' => $assessment,
        ])->layout('platform::layouts.app');
    }
}
