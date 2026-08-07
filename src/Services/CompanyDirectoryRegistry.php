<?php

namespace Platform\Customer\Services;

use Platform\Customer\Contracts\CompanyDirectoryProvider;

/**
 * CompanyDirectoryRegistry — hält den (optionalen) Firmen-Verzeichnis-Provider (CRM).
 * Kein Provider registriert → CRM-Verknüpfung ist im UI schlicht nicht angeboten (graceful).
 */
class CompanyDirectoryRegistry
{
    protected ?CompanyDirectoryProvider $provider = null;

    public function register(CompanyDirectoryProvider $provider): void
    {
        $this->provider = $provider;
    }

    public function provider(): ?CompanyDirectoryProvider
    {
        return $this->provider;
    }

    public function available(): bool
    {
        return $this->provider !== null;
    }
}
