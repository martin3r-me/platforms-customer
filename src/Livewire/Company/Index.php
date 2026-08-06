<?php

namespace Platform\Customer\Livewire\Company;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Customer\Support\Companies;

/**
 * Betriebe — Umwelt-Baum (Kunden + Abteilungen), NUR lesend.
 * Anlegen/Typisieren passiert im organization-Modul (external_customer = Trigger);
 * customer projiziert und pflegt die Betrieb-Ebenen-Fachdaten (Anker-Prinzip).
 */
class Index extends Component
{
    public function render()
    {
        $team = (int) Auth::user()->currentTeam->id;

        return view('customer::livewire.company.index', [
            'rows' => Companies::tree($team),
        ])->layout('platform::layouts.app');
    }
}
