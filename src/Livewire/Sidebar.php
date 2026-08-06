<?php

namespace Platform\Customer\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Support\Companies;

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
