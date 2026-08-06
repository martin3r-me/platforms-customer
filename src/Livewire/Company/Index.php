<?php

namespace Platform\Customer\Livewire\Company;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Customer\Support\Companies;

/**
 * Betriebe — Umwelt-Baum (Kunden + Abteilungen) + Anlage neuer Betriebe.
 * Betrieb = external_customer-Entity im Organization-Graphen.
 */
class Index extends Component
{
    public bool $showCreate = false;
    public string $newCompanyName = '';

    public function createCompany(): void
    {
        $name = trim($this->newCompanyName);
        if ($name === '') {
            return;
        }

        $user = Auth::user();
        Companies::createCompany($name, (int) $user->currentTeam->id, (int) $user->id);

        $this->newCompanyName = '';
        $this->showCreate = false;
    }

    public function render()
    {
        $team = (int) Auth::user()->currentTeam->id;

        return view('customer::livewire.company.index', [
            'rows' => Companies::tree($team),
        ])->layout('platform::layouts.app');
    }
}
