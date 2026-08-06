{{--
    Customer · Betrieb/Abteilung-Detail — nx-Design-System.
--}}

<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar :title="$entity->name" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="array_values(array_filter([
            ['label' => 'Betriebe', 'route' => 'customer.companies.index', 'icon' => 'building-office-2'],
            $parent ? ['label' => $parent->name, 'route' => 'customer.companies.show', 'params' => [$parent->id]] : null,
            ['label' => $entity->name],
        ]))">
            <x-nx-button variant="primary" size="sm" wire:click="$set('showCreate', true)">
                @svg('heroicon-o-plus', 'w-4 h-4')
                <span>Neue Abteilung</span>
            </x-nx-button>
        </x-ui-page-actionbar>
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        <x-nx-card>
            <div class="flex items-center gap-3">
                @svg($parent ? 'heroicon-o-building-office' : 'heroicon-o-building-office-2', 'w-6 h-6 text-[color:var(--nx-muted)]')
                <div>
                    <div class="text-sm font-medium text-[color:var(--nx-text)]">{{ $entity->name }}</div>
                    <div class="text-xs text-[color:var(--nx-muted)]">{{ $entity->type?->name }}</div>
                </div>
            </div>
        </x-nx-card>

        {{-- Abteilungen --}}
        <x-nx-section icon="heroicon-o-building-office" title="Abteilungen" :hint="$children->count()">
            @if($children->isEmpty())
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-building-office">
                        Keine Abteilungen. Lege eine über „Neue Abteilung" an.
                    </x-nx-empty>
                </x-nx-card>
            @else
                <x-nx-card flush class="divide-y divide-[color:var(--nx-line)]">
                    @foreach($children as $child)
                        <a href="{{ route('customer.companies.show', $child->id) }}" wire:navigate
                           class="flex items-center justify-between px-4 py-2.5 hover:bg-[color:var(--nx-hover)]">
                            <span class="flex items-center gap-2 min-w-0">
                                @svg('heroicon-o-building-office', 'w-4 h-4 text-[color:var(--nx-muted)]')
                                <span class="truncate text-[color:var(--nx-text)]">{{ $child->name }}</span>
                                <span class="text-xs text-[color:var(--nx-faint)]">{{ $child->type?->name }}</span>
                            </span>
                            @svg('heroicon-o-chevron-right', 'w-4 h-4 text-[color:var(--nx-faint)] shrink-0')
                        </a>
                    @endforeach
                </x-nx-card>
            @endif
        </x-nx-section>
    </x-ui-page-container>

    {{-- Anlegen-Modal --}}
    <x-nx-modal wire:model="showCreate" size="md">
        <x-slot name="header">Neue Abteilung</x-slot>
        <div class="space-y-4">
            <x-nx-input-text name="newDepartmentName" label="Name der Abteilung" wire:model="newDepartmentName" required />
            <p class="text-xs text-[color:var(--nx-muted)]">
                Wird als Abteilung (customer_department) unter „{{ $entity->name }}" im Graphen angelegt.
            </p>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-nx-button variant="ghost" wire:click="$set('showCreate', false)">Abbrechen</x-nx-button>
                <x-nx-button variant="primary" wire:click="createDepartment">Anlegen</x-nx-button>
            </div>
        </x-slot>
    </x-nx-modal>

    <x-slot name="sidebar">
        <x-ui-page-sidebar title="Übersicht" width="w-80" :defaultOpen="true">
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)] mb-3">Betrieb</h3>
                    <div class="text-sm text-[color:var(--nx-text)]">{{ $entity->name }}</div>
                    <div class="text-sm text-[color:var(--nx-muted)]">{{ $entity->type?->name }}</div>
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
