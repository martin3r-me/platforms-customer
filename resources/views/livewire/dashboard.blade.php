{{--
    Customer · Dashboard — nx-Design-System.
    Shell bleibt x-ui-page*, Inhalt ausschließlich x-nx-* + var(--nx-*).
--}}

<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar title="Betriebe" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="[
            ['label' => 'Betriebe', 'icon' => 'building-office-2'],
        ]">
            <x-nx-button variant="primary" size="sm" :href="route('customer.companies.index')" wire:navigate>
                @svg('heroicon-o-building-office-2', 'w-4 h-4')
                <span>Zu den Betrieben</span>
            </x-nx-button>
        </x-ui-page-actionbar>
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        <x-nx-stat-grid :cols="2">
            <a href="{{ route('customer.companies.index') }}" wire:navigate>
                <x-nx-stat label="Betriebe" :value="$stats['companies']" icon="heroicon-o-building-office-2" hint="Kunden" />
            </a>
            <x-nx-stat label="Abteilungen" :value="$stats['departments']" icon="heroicon-o-building-office" hint="gesamt" />
        </x-nx-stat-grid>

        @if($stats['companies'] === 0)
            <x-nx-card>
                <x-nx-empty icon="heroicon-o-building-office-2">
                    Noch keine Betriebe. Lege den ersten in der Betriebe-Liste an.
                    <x-slot name="action">
                        <x-nx-button variant="secondary" size="sm" :href="route('customer.companies.index')" wire:navigate>
                            Zu den Betrieben
                        </x-nx-button>
                    </x-slot>
                </x-nx-empty>
            </x-nx-card>
        @endif
    </x-ui-page-container>

    <x-slot name="sidebar">
        <x-ui-page-sidebar title="Übersicht" width="w-80" :defaultOpen="true">
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)] mb-3">Betriebe</h3>
                    <div class="text-sm text-[color:var(--nx-muted)]">Kunden + Abteilungen aus dem Graphen.</div>
                </div>
            </div>
        </x-ui-page-sidebar>
    </x-slot>

    <x-slot name="activity">
        <x-ui-page-sidebar title="Aktivitäten" width="w-80" :defaultOpen="false" storeKey="activityOpen" side="right">
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)] mb-3">Letzte Aktivitäten</h3>
                    <div class="text-sm text-[color:var(--nx-muted)]">Keine Aktivitäten verfügbar.</div>
                </div>
            </div>
        </x-ui-page-sidebar>
    </x-slot>
</x-ui-page>
