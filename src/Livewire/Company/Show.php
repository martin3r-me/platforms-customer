<?php

namespace Platform\Customer\Livewire\Company;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Models\RiskAssessment;

/**
 * Betrieb/Abteilung-Detail — Betriebs-Cockpit. Abteilungen (Kinder, nur lesend)
 * + Betrieb-verankerte Fachdaten. Erste eigene Entität: Gefährdungsbeurteilungen,
 * die beim Anlegen automatisch per dimension_link an diesen Knoten hängen (Anker-Prinzip).
 */
class Show extends Component
{
    #[Locked]
    public int $entityId;

    public bool $showCreate = false;
    public string $newTitle = '';
    public string $newWorkArea = '';

    public function mount(int $company): void
    {
        $this->entityId = $this->resolve($company)->id;
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

        $riskAssessments = RiskAssessment::query()
            ->forTeam($team)
            ->where('organization_entity_id', $entity->id)
            ->orderByDesc('id')
            ->get();

        return view('customer::livewire.company.show', [
            'entity'          => $entity,
            'parent'          => $parent,
            'children'        => $children,
            'riskAssessments' => $riskAssessments,
        ])->layout('platform::layouts.app');
    }
}
