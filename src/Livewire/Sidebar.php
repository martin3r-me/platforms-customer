<?php

namespace Platform\Customer\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Support\Companies;

/**
 * Modul-Sidebar (Haupt-Sidebar links) — schlanke Modul-Navigation.
 * Die betriebs-spezifische Funktions-Navigation liegt in der INNEREN
 * Seiten-Sidebar (x-ui-page-sidebar) der Company/Show-Seite, nicht hier.
 */
class Sidebar extends Component
{
    public function render()
    {
        $user = Auth::user();
        $companies = collect();

        if ($user && $user->currentTeam) {
            $customerTypeId = Companies::customerTypeId();
            if ($customerTypeId) {
                $companies = OrganizationEntity::query()
                    ->forTeam($user->currentTeam->id)
                    ->where('entity_type_id', (int) $customerTypeId)
                    ->orderBy('name')
                    ->limit(15)
                    ->get(['id', 'name']);
            }
        }

        return view('customer::livewire.sidebar', [
            'companies' => $companies,
        ]);
    }
}
