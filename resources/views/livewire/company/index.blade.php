{{--
    Customer · Betriebe (Umwelt-Baum) — nx-Design-System.
--}}

<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar title="Kundenbetriebe" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="[
            ['label' => 'Kundenbetriebe', 'route' => 'customer.dashboard', 'icon' => 'building-office-2'],
            ['label' => 'Alle Betriebe'],
        ]" />
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        <x-nx-section icon="heroicon-o-building-office-2" title="Betriebe &amp; Abteilungen"
                      description="Kunden aus dem Organization-Graphen (Umwelt) mit ihren Abteilungen."
                      :hint="count($rows)">
            @if(empty($rows))
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-building-office-2">
                        Noch keine Betriebe. Markiere im Organization-Modul eine Umwelt-Entität
                        als „Externer Kunde" — sie erscheint dann automatisch hier.
                    </x-nx-empty>
                </x-nx-card>
            @else
                <x-nx-card flush class="divide-y divide-[color:var(--nx-line)]">
                    @foreach($rows as $row)
                        <a href="{{ route('customer.companies.show', $row['id']) }}" wire:navigate
                           class="flex items-center justify-between px-4 py-2.5 hover:bg-[color:var(--nx-hover)]">
                            <span class="flex items-center gap-2 min-w-0" style="padding-left: {{ $row['depth'] * 1.25 }}rem">
                                @svg($row['depth'] === 0 ? 'heroicon-o-building-office-2' : 'heroicon-o-building-office', 'w-4 h-4 text-[color:var(--nx-muted)]')
                                <span class="truncate {{ $row['depth'] === 0 ? 'font-medium text-[color:var(--nx-text)]' : 'text-[color:var(--nx-text)]' }}">{{ $row['name'] }}</span>
                                <span class="text-xs text-[color:var(--nx-faint)]">{{ $row['type'] }}</span>
                            </span>
                            @svg('heroicon-o-chevron-right', 'w-4 h-4 text-[color:var(--nx-faint)] shrink-0')
                        </a>
                    @endforeach
                </x-nx-card>
            @endif
        </x-nx-section>
    </x-ui-page-container>

    <x-slot name="sidebar">
        <x-ui-page-sidebar title="Übersicht" width="w-80" :defaultOpen="true">
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)] mb-3">Umwelt</h3>
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
