<?php

namespace Platform\Customer\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Support\Companies;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $team = $user?->currentTeam;

        $stats = ['companies' => 0, 'departments' => 0];

        if ($team) {
            $customerTypeId = Companies::customerTypeId();
            $departmentTypeId = Companies::departmentTypeId();

            if ($customerTypeId) {
                $stats['companies'] = OrganizationEntity::query()
                    ->forTeam($team->id)
                    ->where('entity_type_id', (int) $customerTypeId)
                    ->count();
            }
            if ($departmentTypeId) {
                $stats['departments'] = OrganizationEntity::query()
                    ->forTeam($team->id)
                    ->where('entity_type_id', (int) $departmentTypeId)
                    ->count();
            }
        }

        return view('customer::livewire.dashboard', [
            'stats'       => $stats,
            'currentDate' => now()->format('d.m.Y'),
        ])->layout('platform::layouts.app');
    }
}
