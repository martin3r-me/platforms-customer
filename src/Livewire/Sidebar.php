<?php

namespace Platform\Customer\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Customer\Support\Companies;

/**
 * Modul-Sidebar (Haupt-Sidebar links) — zeigt den Betrieb-Baum (externe Kunden +
 * Standorte + Abteilungen, beliebig tief) als führende Navigation. Klick auf einen
 * Knoten öffnet dessen Cockpit; die Funktions-Reiter (Gefährdung …) liegen in der
 * inneren Seiten-Sidebar und gelten immer im Kontext des gewählten Knotens.
 */
class Sidebar extends Component
{
    public function render()
    {
        $user = Auth::user();
        $team = $user?->currentTeam?->id;

        $nodes = [];
        if ($team) {
            foreach (Companies::tree((int) $team) as $n) {
                $nodes[] = [
                    'id'    => $n['id'],
                    'label' => $n['name'],
                    'depth' => $n['depth'],
                    'url'   => route('customer.companies.show', $n['id']),
                ];
            }
        }

        $route    = request()->route();
        $activeId = $route ? $route->parameter('company') : null;

        return view('customer::livewire.sidebar', [
            'nodes'    => $nodes,
            'activeId' => $activeId,
        ]);
    }
}
