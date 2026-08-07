<?php

namespace Platform\Customer\Contracts;

/**
 * CompanyDirectoryProvider — ein Firmen-Verzeichnis (i.d.R. CRM), das customer nutzt, um
 * eine Firma zu suchen/anzulegen und sie am Betrieb-Knoten zu verknüpfen. Inversion:
 * customer kennt CRMs Model NICHT; CRM registriert einen Provider (guarded).
 */
interface CompanyDirectoryProvider
{
    /** @return array<int,array{id:int,label:string,subtitle:?string}> */
    public function search(int $teamId, string $query): array;

    /** Neue Firma anlegen (Name) → deren id (context_id für den Alias 'crm_company'), oder null. */
    public function createCompany(int $teamId, string $name): ?int;
}
