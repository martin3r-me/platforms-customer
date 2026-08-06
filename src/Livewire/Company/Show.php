<?php

namespace Platform\Customer\Livewire\Company;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Support\Companies;

/**
 * Betrieb/Abteilung-Detail — Abteilungen (Kinder) + Anlage neuer Abteilungen.
 * Abteilung = customer_department-Entity mit parent_entity_id auf den Betrieb.
 */
class Show extends Component
{
    #[Locked]
    public int $entityId;

    public bool $showCreate = false;
    public string $newDepartmentName = '';

    public function mount(int $company): void
    {
        $this->entityId = $this->resolve($company)->id;
    }

    protected function resolve(int $id): OrganizationEntity
    {
        $team = (int) Auth::user()->currentTeam->id;

        return OrganizationEntity::query()->forTeam($team)->findOrFail($id);
    }

    public function createDepartment(): void
    {
        $name = trim($this->newDepartmentName);
        if ($name === '') {
            return;
        }

        $user = Auth::user();
        Companies::createDepartment($name, $this->entityId, (int) $user->currentTeam->id, (int) $user->id);

        $this->newDepartmentName = '';
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

        return view('customer::livewire.company.show', [
            'entity'   => $entity,
            'parent'   => $parent,
            'children' => $children,
        ])->layout('platform::layouts.app');
    }
}
