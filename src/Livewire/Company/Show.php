<?php

namespace Platform\Customer\Livewire\Company;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Models\RiskAssessment;
use Platform\Customer\Support\Companies;
use Platform\Customer\Support\CompanySections;
use Platform\Customer\Services\CompanyPatientRegistry;

/**
 * Betrieb/Abteilung-Detail — eine Komponente, mehrere EIGENE Routen. Der Abschnitt
 * ergibt sich aus dem Route-Namen (CompanySections); die Navigation liegt in der
 * inneren linken Sidebar. Real: Übersicht + Gefährdungsbeurteilungen. Platzhalter:
 * Beschäftigte, Betreuung, Preise, Begehungen (Anker-Prinzip; Fachlogik folgt).
 */
class Show extends Component
{
    #[Locked]
    public int $entityId;

    #[Locked]
    public string $section = 'overview';

    public bool $showCreate = false;
    public string $newTitle = '';
    public string $newWorkArea = '';

    public function mount(int $company): void
    {
        $this->entityId = $this->resolve($company)->id;
        $this->section = CompanySections::keyForRoute(request()->route()?->getName() ?? '');
    }

    protected function resolve(int $id): OrganizationEntity
    {
        $team = (int) Auth::user()->currentTeam->id;

        return OrganizationEntity::query()->forTeam($team)->findOrFail($id);
    }

    public function createRiskAssessment(): void
    {
        $title = trim($this->newTitle);
        if ($title === '') {
            return;
        }

        RiskAssessment::create([
            'organization_entity_id' => $this->entityId,
            'title'                  => $title,
            'work_area'              => trim($this->newWorkArea) ?: null,
            'created_by_user_id'     => Auth::id(),
        ]);

        $this->reset(['newTitle', 'newWorkArea']);
        $this->showCreate = false;
    }

    public function render()
    {
        $team = (int) Auth::user()->currentTeam->id;

        $entity = $this->resolve($this->entityId)->load('type');

        $parent = $entity->parent_entity_id
            ? OrganizationEntity::query()->whereKey($entity->parent_entity_id)->first(['id', 'name'])
            : null;

        $children = OrganizationEntity::query()
            ->forTeam($team)
            ->where('parent_entity_id', $entity->id)
            ->with('type')
            ->orderBy('name')
            ->get();

        $riskAssessments = $this->section === 'risk'
            ? RiskAssessment::query()
                ->forTeam($team)
                ->where('organization_entity_id', $entity->id)
                ->orderByDesc('id')
                ->get()
            : collect();

        // Patienten: aus den Fachmodulen (Registry), über den Betrieb-Teilbaum. Nur Navigation.
        $patients = [];
        if ($this->section === 'patients') {
            $entityIds = Companies::subtreeIds($entity->id, $team);
            $patients = resolve(CompanyPatientRegistry::class)->patientsFor($entityIds, $team);
        }

        return view('customer::livewire.company.show', [
            'entity'          => $entity,
            'parent'          => $parent,
            'children'        => $children,
            'riskAssessments' => $riskAssessments,
            'patients'        => $patients,
            'sections'        => CompanySections::all(),
            'crmProfile'      => \Platform\Customer\Support\CompanyProfile::crmForEntity($this->entityId),
        ])->layout('platform::layouts.app');
    }
}
