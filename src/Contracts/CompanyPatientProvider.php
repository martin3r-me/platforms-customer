<?php

namespace Platform\Customer\Contracts;

/**
 * CompanyPatientProvider — ein Fachmodul (z. B. occupational) liefert die Patienten
 * bei, die an einem Betrieb (+ Teilbaum) hängen. customer kennt die Fachmodule NICHT;
 * sie docken additiv an (wie PatientPanelProvider, nur andersrum: Betrieb → Person).
 *
 * WICHTIG (Schweigepflicht/Isolation): customer speichert KEINE Patientendaten. Der
 * Provider liefert nur eine Navigations-Liste (Name + fertige URL zur Patienten-Akte).
 */
interface CompanyPatientProvider
{
    /**
     * Patienten für die gegebenen Org-Entities (Betrieb + Abteilungen im Teilbaum).
     *
     * @param  array<int,int> $entityIds
     * @return array<int,array{patient_id:int,name:string,subtitle:?string,meta:?string,url:string}>
     */
    public function patientsFor(array $entityIds, int $teamId): array;
}
