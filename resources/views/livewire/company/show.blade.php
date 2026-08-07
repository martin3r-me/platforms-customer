{{--
    Customer · Betrieb-Cockpit — eigene Routen je Funktion, Navigation in der inneren
    Sidebar. Abschnitt kommt aus $section. Real: Übersicht + Gefährdungsbeurteilungen.
    Platzhalter: Beschäftigte, Betreuung, Preise, Begehungen.
--}}

<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar :title="$entity->name" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="array_values(array_filter([
            ['label' => 'Kundenbetriebe', 'route' => 'customer.companies.index', 'icon' => 'building-office-2'],
            $parent ? ['label' => $parent->name, 'route' => 'customer.companies.show', 'params' => [$parent->id]] : null,
            ['label' => $entity->name],
        ]))">
            @if($section === 'risk')
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

        {{-- ═══ Übersicht ═══ --}}
        @if($section === 'overview')
            <x-nx-section icon="heroicon-o-identification" title="Steckbrief"
                          description="Basis aus dem Org-Graphen, angereichert aus dem CRM (falls verknüpft).">
                <x-nx-card>
                    <div class="space-y-3">
                        {{-- Basis (immer, aus organization) --}}
                        <div>
                            <div class="text-sm font-medium text-[color:var(--nx-text)]">{{ $entity->name }}</div>
                            <div class="text-xs text-[color:var(--nx-muted)]">{{ $entity->type?->name }}</div>
                        </div>

                        @if($crmProfile)
                            {{-- CRM-Anreicherung (crm_company am selben Knoten, graph-nativ) --}}
                            @php
                                $labels = ['legal_name' => 'Rechtsname', 'status' => 'Status', 'industry' => 'Branche', 'address' => 'Anschrift', 'phone' => 'Telefon', 'email' => 'E-Mail', 'website' => 'Website', 'vat_number' => 'USt-IdNr'];
                            @endphp
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-sm border-t border-[color:var(--nx-line)] pt-3">
                                @foreach($labels as $key => $label)
                                    @if(!empty($crmProfile[$key]))
                                        <div>
                                            <dt class="text-[color:var(--nx-muted)]">{{ $label }}</dt>
                                            <dd class="text-[color:var(--nx-text)]">{{ $crmProfile[$key] }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>
                            <div class="inline-flex items-center gap-1 text-xs text-[color:var(--nx-faint)]">
                                @svg('heroicon-o-link', 'w-3 h-3') Quelle: CRM (am Knoten verknüpft)
                            </div>
                        @else
                            <div class="border-t border-[color:var(--nx-line)] pt-3 space-y-2">
                                <div class="text-xs text-[color:var(--nx-faint)]">
                                    Keine CRM-Firma verknüpft. Stammdaten & Kontakt erscheinen hier, sobald der Betrieb im CRM gepflegt und am Knoten verknüpft ist.
                                </div>
                                @if($crmDirectoryAvailable)
                                    <x-nx-button variant="secondary" size="sm" wire:click="openCrmLink">
                                        @svg('heroicon-o-link', 'w-4 h-4')
                                        <span>Mit CRM verknüpfen</span>
                                    </x-nx-button>
                                @endif
                            </div>
                        @endif
                    </div>
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
        @if($section === 'risk')
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

        {{-- ═══ Patienten (real — aus Fachmodulen aggregiert, Sprung in die Akte) ═══ --}}
        @if($section === 'patients')
            <x-nx-section icon="heroicon-o-user-group" title="Patienten"
                          description="Patienten dieses Betriebs (inkl. Abteilungen) aus den Beschäftigungen — Klick springt in die Akte."
                          :hint="count($patients)">
                @if(empty($patients))
                    <x-nx-card>
                        <x-nx-empty icon="heroicon-o-user-group">
                            Keine Patienten an diesem Betrieb. Beschäftigte werden im Arbeitsmedizin-Modul angelegt.
                        </x-nx-empty>
                    </x-nx-card>
                @else
                    <x-nx-card flush class="divide-y divide-[color:var(--nx-line)]">
                        @foreach($patients as $p)
                            <a href="{{ $p['url'] }}" wire:navigate
                               class="flex items-center justify-between px-4 py-2.5 hover:bg-[color:var(--nx-hover)]">
                                <span class="flex items-center gap-2 min-w-0">
                                    @svg('heroicon-o-user', 'w-4 h-4 text-[color:var(--nx-muted)]')
                                    <span class="truncate text-[color:var(--nx-text)]">{{ $p['name'] }}</span>
                                    @if(!empty($p['subtitle']))
                                        <span class="text-xs text-[color:var(--nx-faint)]">{{ $p['subtitle'] }}</span>
                                    @endif
                                </span>
                                <span class="flex items-center gap-2 shrink-0">
                                    @if(!empty($p['meta']))
                                        <span class="text-xs text-[color:var(--nx-faint)]">{{ $p['meta'] }}</span>
                                    @endif
                                    @svg('heroicon-o-chevron-right', 'w-4 h-4 text-[color:var(--nx-faint)]')
                                </span>
                            </a>
                        @endforeach
                    </x-nx-card>
                @endif
            </x-nx-section>
        @endif

        {{-- ═══ Betreuung (Platzhalter) ═══ --}}
        @if($section === 'care')
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
        @if($section === 'pricing')
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
        @if($section === 'inspections')
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
        <x-ui-page-sidebar :title="$entity->name" icon="heroicon-o-building-office-2" width="w-64" :defaultOpen="true">
            <nav class="p-2 space-y-0.5">
                <a href="{{ route('customer.companies.index') }}" wire:navigate
                   class="flex items-center gap-2 px-2 py-1.5 text-xs text-[color:var(--nx-muted)] hover:text-[color:var(--nx-text)]">
                    @svg('heroicon-o-chevron-left', 'w-3.5 h-3.5')
                    <span>Alle Betriebe</span>
                </a>

                @if($parent)
                    <a href="{{ route('customer.companies.show', $parent->id) }}" wire:navigate
                       class="flex items-center gap-2 px-2 pb-1 text-xs text-[color:var(--nx-faint)] hover:text-[color:var(--nx-text)] truncate">
                        @svg('heroicon-o-building-office-2', 'w-3.5 h-3.5')
                        <span class="truncate">{{ $parent->name }}</span>
                    </a>
                @endif

                <div class="pt-1">
                    @foreach($sections as $s)
                        <a href="{{ route($s['route'], $entity->id) }}" wire:navigate
                           @class([
                               'flex items-center gap-2 px-2 py-1.5 rounded-md text-sm transition',
                               'bg-[color:var(--nx-active)] text-[color:var(--nx-text)] font-semibold' => $section === $s['key'],
                               'text-[color:var(--nx-text)] hover:bg-[color:var(--nx-hover)]' => $section !== $s['key'],
                           ])>
                            @svg($s['icon'], 'w-4 h-4')
                            <span>{{ $s['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>
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

    {{-- Mit CRM verknüpfen (Firma anlegen oder bestehende wählen → dimension_link am Knoten) --}}
    <x-nx-modal wire:model="showCrmLink" size="md">
        <x-slot name="header">Mit CRM verknüpfen</x-slot>
        <div class="space-y-5">
            {{-- Neue Firma anlegen --}}
            <div class="space-y-2">
                <div class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)]">Neue CRM-Firma anlegen</div>
                <x-nx-input-text name="crmNewName" label="Name" wire:model="crmNewName" />
                <x-nx-button variant="primary" size="sm" wire:click="createAndLinkCrm">
                    @svg('heroicon-o-plus', 'w-4 h-4') <span>Anlegen &amp; verknüpfen</span>
                </x-nx-button>
            </div>

            {{-- Bestehende suchen --}}
            <div class="space-y-2 border-t border-[color:var(--nx-line)] pt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)]">Bestehende Firma verknüpfen</div>
                <div class="flex gap-2">
                    <x-nx-input-text name="crmSearch" label="" wire:model="crmSearch" placeholder="Firma suchen…" wire:keydown.enter="searchCrm" />
                    <x-nx-button variant="secondary" size="sm" wire:click="searchCrm">Suchen</x-nx-button>
                </div>
                @if(!empty($crmResults))
                    <div class="divide-y divide-[color:var(--nx-line)] border border-[color:var(--nx-line)] rounded-md">
                        @foreach($crmResults as $r)
                            <button type="button" wire:click="linkExistingCrm({{ $r['id'] }})"
                                    class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-[color:var(--nx-hover)]">
                                <span class="min-w-0">
                                    <span class="block truncate text-sm text-[color:var(--nx-text)]">{{ $r['label'] }}</span>
                                    @if($r['subtitle'])<span class="block truncate text-xs text-[color:var(--nx-muted)]">{{ $r['subtitle'] }}</span>@endif
                                </span>
                                @svg('heroicon-o-link', 'w-4 h-4 text-[color:var(--nx-faint)] shrink-0')
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <x-slot name="footer">
            <x-nx-button variant="ghost" wire:click="$set('showCrmLink', false)">Schließen</x-nx-button>
        </x-slot>
    </x-nx-modal>
</x-ui-page>
