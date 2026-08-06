{{--
    Customer · Betrieb-Cockpit mit Tab-Navigation (deep-linkable ?tab=) — nx-Design-System.
    Real: Übersicht + Gefährdungsbeurteilungen. Platzhalter: Beschäftigte, Betreuung, Preise, Begehungen.
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
            @if($tab === 'risk')
                <x-nx-button variant="primary" size="sm" wire:click="$set('showCreate', true)">
                    @svg('heroicon-o-plus', 'w-4 h-4')
                    <span>Neue Gefährdungsbeurteilung</span>
                </x-nx-button>
            @endif
        </x-ui-page-actionbar>
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        {{-- Betrieb-Kopf --}}
        <x-nx-card>
            <div class="flex items-center gap-3">
                @svg($parent ? 'heroicon-o-building-office' : 'heroicon-o-building-office-2', 'w-6 h-6 text-[color:var(--nx-muted)]')
                <div>
                    <div class="text-sm font-medium text-[color:var(--nx-text)]">{{ $entity->name }}</div>
                    <div class="text-xs text-[color:var(--nx-muted)]">{{ $entity->type?->name }}</div>
                </div>
            </div>
        </x-nx-card>

        {{-- Reiter (Klickroute) --}}
        <x-nx-tabs>
            @foreach($tabs as $key => [$label, $icon])
                <x-nx-tab :active="$tab === $key" wire:click="$set('tab', '{{ $key }}')"
                          class="inline-flex items-center gap-1.5 whitespace-nowrap">
                    @svg($icon, 'w-4 h-4')
                    <span>{{ $label }}</span>
                </x-nx-tab>
            @endforeach
        </x-nx-tabs>

        {{-- ═══ Übersicht ═══ --}}
        @if($tab === 'overview')
            <x-nx-section icon="heroicon-o-identification" title="Steckbrief"
                          description="Stammdaten & Kontakt des Betriebs (später aus CRM verheiratet).">
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-identification">
                        Adresse, Ansprechpartner & Vertragsdaten kommen später aus dem CRM (loser Link).
                    </x-nx-empty>
                </x-nx-card>
            </x-nx-section>

            <x-nx-section icon="heroicon-o-building-office" title="Abteilungen" :hint="$children->count()">
                @if($children->isEmpty())
                    <x-nx-card>
                        <x-nx-empty icon="heroicon-o-building-office">
                            Keine Abteilungen. Struktur wird im Organization-Modul gepflegt.
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
        @endif

        {{-- ═══ Gefährdungsbeurteilungen (real) ═══ --}}
        @if($tab === 'risk')
            <x-nx-section icon="heroicon-o-shield-exclamation" title="Gefährdungsbeurteilungen" :hint="$riskAssessments->count()">
                @if($riskAssessments->isEmpty())
                    <x-nx-card>
                        <x-nx-empty icon="heroicon-o-shield-exclamation">
                            Noch keine Gefährdungsbeurteilung für diesen Betrieb/Bereich.
                            <x-slot name="action">
                                <x-nx-button variant="secondary" size="sm" wire:click="$set('showCreate', true)">
                                    Neue Gefährdungsbeurteilung
                                </x-nx-button>
                            </x-slot>
                        </x-nx-empty>
                    </x-nx-card>
                @else
                    <x-nx-card flush class="divide-y divide-[color:var(--nx-line)]">
                        @foreach($riskAssessments as $ra)
                            <a href="{{ route('customer.risk-assessments.show', $ra->id) }}" wire:navigate
                               class="flex items-center justify-between px-4 py-2.5 hover:bg-[color:var(--nx-hover)]">
                                <span class="flex items-center gap-2 min-w-0">
                                    @svg('heroicon-o-shield-exclamation', 'w-4 h-4 text-[color:var(--nx-muted)]')
                                    <span class="truncate text-[color:var(--nx-text)]">{{ $ra->title ?? 'Ohne Titel' }}</span>
                                    @if($ra->work_area)
                                        <span class="text-xs text-[color:var(--nx-faint)]">{{ $ra->work_area }}</span>
                                    @endif
                                </span>
                                <span class="shrink-0">
                                    @if($ra->status)
                                        <x-nx-badge dot>{{ $ra->status->label() }}</x-nx-badge>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </x-nx-card>
                @endif
            </x-nx-section>
        @endif

        {{-- ═══ Beschäftigte (Platzhalter — später aggregiert aus occupational) ═══ --}}
        @if($tab === 'staff')
            <x-nx-section icon="heroicon-o-users" title="Beschäftigte"
                          description="Beschäftigte dieses Betriebs samt Abteilungen — später aggregiert aus dem Betriebsmedizin-Modul.">
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-users">
                        Kommt: Beschäftigte werden über den Org-Teilbaum aus occupational eingesammelt (Panel-Registry).
                    </x-nx-empty>
                </x-nx-card>
            </x-nx-section>
        @endif

        {{-- ═══ Betreuung (Platzhalter) ═══ --}}
        @if($tab === 'care')
            <x-nx-section icon="heroicon-o-clipboard-document-check" title="Betreuung"
                          description="Betreuungsvertrag & arbeitsmedizinischer Betreuungsstatus (ArbMedVV).">
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-clipboard-document-check">
                        Kommt: Betreuungsvertrag, Betreuungsart, Einsatzzeiten und Status je Betrieb.
                    </x-nx-empty>
                </x-nx-card>
            </x-nx-section>
        @endif

        {{-- ═══ Preise (Platzhalter) ═══ --}}
        @if($tab === 'pricing')
            <x-nx-section icon="heroicon-o-banknotes" title="Preise & Konditionen"
                          description="Preisschienen und Konditionen je Betrieb.">
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-banknotes">
                        Kommt: Preisschienen (Leistungspreise/Pauschalen) und Vertragskonditionen dieses Betriebs.
                    </x-nx-empty>
                </x-nx-card>
            </x-nx-section>
        @endif

        {{-- ═══ Begehungen (Platzhalter) ═══ --}}
        @if($tab === 'inspections')
            <x-nx-section icon="heroicon-o-clipboard-document-list" title="Begehungen"
                          description="Betriebsbegehungen mit Protokoll und Fristen.">
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-clipboard-document-list">
                        Kommt: Betriebsbegehungen, Protokolle, Feststellungen und Wiedervorlage-Fristen.
                    </x-nx-empty>
                </x-nx-card>
            </x-nx-section>
        @endif
    </x-ui-page-container>

    {{-- Anlegen-Modal: Gefährdungsbeurteilung (hängt automatisch an diesen Knoten) --}}
    <x-nx-modal wire:model="showCreate" size="md">
        <x-slot name="header">Neue Gefährdungsbeurteilung</x-slot>
        <div class="space-y-4">
            <x-nx-input-text name="newTitle" label="Titel" wire:model="newTitle" required />
            <x-nx-input-text name="newWorkArea" label="Arbeitsbereich (optional)" wire:model="newWorkArea" />
            <p class="text-xs text-[color:var(--nx-muted)]">
                Wird automatisch an „{{ $entity->name }}" im Organization-Graphen verknüpft.
            </p>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-nx-button variant="ghost" wire:click="$set('showCreate', false)">Abbrechen</x-nx-button>
                <x-nx-button variant="primary" wire:click="createRiskAssessment">Anlegen</x-nx-button>
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
