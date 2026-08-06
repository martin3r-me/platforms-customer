<?php

namespace Platform\Customer\Navigation;

use Platform\Patient\Contracts\PatientNavigationLens;
use Platform\Customer\Support\Companies;
use Platform\Customer\Services\CompanyPatientRegistry;

/**
 * Betrieb-Linse für die Patienten-Navigation: bringt den Umwelt-Baum (Betriebe +
 * Abteilungen) als führende Dimension ins patient-Modul und liefert je Knoten die
 * Patienten des Teilbaums (über CompanyPatientRegistry). Betriebsmedizin-Perspektive.
 */
class BetriebNavigationLens implements PatientNavigationLens
{
    public function key(): string
    {
        return 'betrieb';
    }

    public function label(): string
    {
        return 'Betrieb';
    }

    public function icon(): string
    {
        return 'heroicon-o-building-office-2';
    }

    public function order(): int
    {
        return 10;
    }

    public function tree(int $teamId): array
    {
        return array_map(fn (array $r) => [
            'id'    => $r['id'],
            'label' => $r['name'],
            'depth' => $r['depth'],
            'meta'  => $r['type'] ?? null,
        ], Companies::tree($teamId));
    }

    public function patientsFor(string $nodeId, int $teamId): array
    {
        $entityIds = Companies::subtreeIds((int) $nodeId, $teamId);
        $rows = resolve(CompanyPatientRegistry::class)->patientsFor($entityIds, $teamId);

        // URL mit Navigations-Kontext anreichern, damit die innere Sidebar erhalten bleibt.
        return array_map(function (array $r) use ($nodeId) {
            $r['url'] = route('patient.patients.show', [
                'patient' => $r['patient_id'],
                'lens'    => 'betrieb',
                'node'    => $nodeId,
            ]);
            return $r;
        }, $rows);
    }
}
