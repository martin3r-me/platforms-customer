<?php

namespace Platform\Customer\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Support\Companies;
use Platform\Customer\Support\CompanySections;

/**
 * Innere linke Sidebar — kontextabhängig:
 *  - im Betrieb (customer.companies.*): Drill-down mit den Funktions-Reitern als Links
 *    (eigene Routen) + aktivem Zustand.
 *  - sonst: Dashboard + Betriebe + zuletzt besuchte Betriebe.
 */
class Sidebar extends Component
{
    public function render()
    {
        $user = Auth::user();
        $team = $user?->currentTeam?->id;

        $route     = request()->route();
        $routeName = $route?->getName() ?? '';
        $companyId = $route ? $route->parameter('company') : null;

        $inCompany = $companyId
            && str_starts_with($routeName, 'customer.companies.')
            && $routeName !== 'customer.companies.index';

        $company = null;
        $parent  = null;

        if ($inCompany && $team) {
            $company = OrganizationEntity::query()->forTeam($team)->with('type')->find($companyId);
            if ($company && $company->parent_entity_id) {
                $parent = OrganizationEntity::query()->whereKey($company->parent_entity_id)->first(['id', 'name']);
            }
        }

        if (!$company) {
            $inCompany = false;
        }

        // Default-Ansicht: zuletzt besuchte / vorhandene Betriebe.
        $companies = collect();
        if (!$inCompany && $team) {
            $customerTypeId = Companies::customerTypeId();
            if ($customerTypeId) {
                $companies = OrganizationEntity::query()
                    ->forTeam($team)
                    ->where('entity_type_id', (int) $customerTypeId)
                    ->orderBy('name')
                    ->limit(15)
                    ->get(['id', 'name']);
            }
        }

        return view('customer::livewire.sidebar', [
            'inCompany' => $inCompany,
            'company'   => $company,
            'parent'    => $parent,
            'sections'  => CompanySections::all(),
            'activeKey' => CompanySections::keyForRoute($routeName),
            'companies' => $companies,
        ]);
    }
}
