{{--
    Customer · Gefährdungsbeurteilung-Detail — nx-Design-System.
--}}

<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar :title="$assessment->title ?? 'Gefährdungsbeurteilung'" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="array_values(array_filter([
            ['label' => 'Kundenbetriebe', 'route' => 'customer.companies.index', 'icon' => 'building-office-2'],
            $assessment->organizationEntity
                ? ['label' => $assessment->organizationEntity->name, 'route' => 'customer.companies.show', 'params' => [$assessment->organizationEntity->id]]
                : null,
            ['label' => $assessment->title ?? 'Gefährdungsbeurteilung'],
        ]))" />
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        <x-nx-card>
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    @svg('heroicon-o-shield-exclamation', 'w-6 h-6 text-[color:var(--nx-muted)]')
                    <div class="text-sm font-medium text-[color:var(--nx-text)]">{{ $assessment->title ?? 'Ohne Titel' }}</div>
                    @if($assessment->status)
                        <x-nx-badge dot>{{ $assessment->status->label() }}</x-nx-badge>
                    @endif
                </div>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm">
                    <dt class="text-[color:var(--nx-muted)]">Arbeitsbereich</dt>
                    <dd class="text-[color:var(--nx-text)]">{{ $assessment->work_area ?? '—' }}</dd>
                    <dt class="text-[color:var(--nx-muted)]">Stand</dt>
                    <dd class="text-[color:var(--nx-text)]">{{ $assessment->assessed_on?->format('d.m.Y') ?? '—' }}</dd>
                    <dt class="text-[color:var(--nx-muted)]">Nächste Überprüfung</dt>
                    <dd class="text-[color:var(--nx-text)]">{{ $assessment->next_review?->format('d.m.Y') ?? '—' }}</dd>
                </dl>
            </div>
        </x-nx-card>

        <x-nx-section icon="heroicon-o-exclamation-triangle" title="Gefährdungen" :hint="$assessment->hazards->count()">
            @if($assessment->hazards->isEmpty())
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-exclamation-triangle">
                        Noch keine einzelnen Gefährdungen erfasst.
                    </x-nx-empty>
                </x-nx-card>
            @else
                <x-nx-card flush>
                    <x-nx-table>
                        <x-nx-table-header>
                            <x-nx-table-header-cell>Kategorie</x-nx-table-header-cell>
                            <x-nx-table-header-cell>Beschreibung</x-nx-table-header-cell>
                            <x-nx-table-header-cell>Status</x-nx-table-header-cell>
                        </x-nx-table-header>
                        <x-nx-table-body>
                            @foreach($assessment->hazards as $hazard)
                                <x-nx-table-row wire:key="hazard-{{ $hazard->id }}">
                                    <x-nx-table-cell>{{ $hazard->category?->label() ?? '—' }}</x-nx-table-cell>
                                    <x-nx-table-cell>{{ $hazard->description ?? '—' }}</x-nx-table-cell>
                                    <x-nx-table-cell>{{ $hazard->status?->label() ?? '—' }}</x-nx-table-cell>
                                </x-nx-table-row>
                            @endforeach
                        </x-nx-table-body>
                    </x-nx-table>
                </x-nx-card>
            @endif
        </x-nx-section>
    </x-ui-page-container>

    <x-slot name="sidebar">
        <x-ui-page-sidebar title="Übersicht" width="w-80" :defaultOpen="true">
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)] mb-3">Betrieb</h3>
                    <div class="text-sm text-[color:var(--nx-text)]">{{ $assessment->organizationEntity?->name ?? '—' }}</div>
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
