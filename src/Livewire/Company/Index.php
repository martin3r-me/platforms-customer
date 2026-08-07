<?php

namespace Platform\Customer\Livewire\Company;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Customer\Support\Companies;
use Platform\Organization\Models\OrganizationEntity;

/**
 * Betriebe — Umwelt-Baum (Kunden + Abteilungen). Onboarding: „Neuer Betrieb" legt den
 * external_customer-KNOTEN im Org-Graphen an (die Identität). CRM/Fachdaten hängen später
 * lose dran (Anker-Prinzip); der Knoten-Name ist die Wahrheit.
 */
class Index extends Component
{
    public bool $showCreate = false;
    public array $newCompany = ['name' => ''];

    /** Neuen Betrieb (external_customer-Entity) im Graphen anlegen. */
    public function createCompany(): void
    {
        $data = $this->validate([
            'newCompany.name' => ['required', 'string', 'max:191'],
        ])['newCompany'];

        $team   = (int) Auth::user()->currentTeam->id;
        $typeId = Companies::customerTypeId();

        if (!$typeId) {
            $this->dispatch('toast', message: 'Entity-Typ „external_customer" fehlt.', type: 'error');
            return;
        }

        $entity = OrganizationEntity::create([
            'team_id'          => $team,
            'user_id'          => (int) Auth::id(),
            'name'             => $data['name'],
            'entity_type_id'   => (int) $typeId,
            'parent_entity_id' => null,
            'is_active'        => true,
        ]);

        $this->reset('newCompany');
        $this->showCreate = false;

        $this->redirectRoute('customer.companies.show', ['company' => $entity->id], navigate: true);
    }

    public function render()
    {
        $team = (int) Auth::user()->currentTeam->id;

        return view('customer::livewire.company.index', [
            'rows' => Companies::tree($team),
        ])->layout('platform::layouts.app');
    }
}
