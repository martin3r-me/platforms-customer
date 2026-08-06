<?php

namespace Platform\Customer\Services;

use Platform\Customer\Contracts\CompanyPatientProvider;

/**
 * CompanyPatientRegistry — sammelt die von Fachmodulen registrierten Patienten-Provider.
 * Singleton; Fachmodule rufen ->register(...) in ihrem boot(). Aggregiert + dedupliziert
 * je Betrieb-Teilbaum (ein Patient nur einmal, aktive Beschäftigung gewinnt).
 */
class CompanyPatientRegistry
{
    /** @var array<int,CompanyPatientProvider> */
    protected array $providers = [];

    public function register(CompanyPatientProvider $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @param  array<int,int> $entityIds  Betrieb + Abteilungen (Teilbaum)
     * @return array<int,array{patient_id:int,name:string,subtitle:?string,meta:?string,url:string}>
     */
    public function patientsFor(array $entityIds, int $teamId): array
    {
        if (empty($entityIds)) {
            return [];
        }

        $byPatient = [];

        foreach ($this->providers as $provider) {
            try {
                foreach ($provider->patientsFor($entityIds, $teamId) as $row) {
                    $pid = $row['patient_id'] ?? null;
                    if ($pid && !isset($byPatient[$pid])) {
                        $byPatient[$pid] = $row;
                    }
                }
            } catch (\Throwable $e) {
                // Ein defekter/abwesender Provider darf die Liste nicht brechen.
            }
        }

        $rows = array_values($byPatient);
        usort($rows, fn ($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

        return $rows;
    }
}
