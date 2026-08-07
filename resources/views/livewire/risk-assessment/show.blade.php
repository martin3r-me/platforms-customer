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

        <x-nx-section icon="heroicon-o-exclamation-triangle" title="Gefährdungen" :hint="$assessment->hazards->count()"
                      description="Je Gefährdung: Faktor (GDA-Katalog) → Risikobewertung (Ampel) → Maßnahme nach STOP.">
            <x-nx-card flush class="divide-y divide-[color:var(--nx-line)]">
                @forelse($assessment->hazards as $hazard)
                    @php $meta = $hazard->riskMeta(); @endphp
                    <div class="px-4 py-3" wire:key="hazard-{{ $hazard->id }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- Ampel --}}
                                    @if($meta)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium">
                                            <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $meta['color'] }}"></span>
                                            {{ $meta['label'] }}@if($meta['rpz']) <span class="text-[color:var(--nx-faint)]">· RPZ {{ $meta['rpz'] }}</span>@endif
                                        </span>
                                    @endif
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-[color:var(--nx-bg)] text-[color:var(--nx-muted)]">{{ $hazard->category?->label() }}</span>
                                    @if($hazard->measure_type)
                                        <span class="text-xs px-1.5 py-0.5 rounded bg-[color:var(--nx-bg)] text-[color:var(--nx-muted)]" title="STOP-Maßnahme">{{ $hazard->measure_type->short() }} · {{ $hazard->measure_type->label() }}</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-sm text-[color:var(--nx-text)]">{{ $hazard->description }}</div>
                                @if($hazard->measures)
                                    <div class="mt-1 text-sm text-[color:var(--nx-muted)]">Maßnahme: {{ $hazard->measures }}</div>
                                @endif
                                <div class="mt-1 flex items-center gap-3 text-xs text-[color:var(--nx-faint)]">
                                    @if($hazard->responsible)<span>Verantwortlich: {{ $hazard->responsible }}</span>@endif
                                    @if($hazard->deadline)<span>Frist: {{ $hazard->deadline->format('d.m.Y') }}</span>@endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="cycleStatus({{ $hazard->id }})"
                                        class="text-xs px-2 py-1 rounded-md border border-[color:var(--nx-line)] hover:bg-[color:var(--nx-hover)] text-[color:var(--nx-text)]">
                                    {{ $hazard->status?->label() }}
                                </button>
                                <button type="button" wire:click="removeHazard({{ $hazard->id }})" wire:confirm="Gefährdung entfernen?"
                                        class="text-xs text-[color:var(--nx-faint)] hover:text-[color:var(--nx-danger)]">Entfernen</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-4 text-sm text-[color:var(--nx-muted)]">Noch keine Gefährdung erfasst.</div>
                @endforelse

                {{-- Gefährdung erfassen --}}
                @php
                    $p = (int) ($newHazard['probability'] ?? 0);
                    $s = (int) ($newHazard['severity'] ?? 0);
                    $rpz = ($p && $s) ? $p * $s : null;
                    $prevColor = $rpz === null ? '#9ca3af' : ($rpz <= 4 ? '#16a34a' : ($rpz <= 12 ? '#d97706' : '#dc2626'));
                    $prevLabel = $rpz === null ? '—' : ($rpz <= 4 ? 'Gering' : ($rpz <= 12 ? 'Mittel' : 'Hoch'));
                    $scale = [1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'];
                @endphp
                <div class="px-4 py-4 bg-[color:var(--nx-bg)] space-y-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-[color:var(--nx-faint)]">Gefährdung erfassen</div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-nx-input-select name="newHazard.category" label="Faktor (GDA)" wire:model.live="newHazard.category" :options="$categoryOptions" />
                        <x-nx-input-text name="newHazard.description" label="Gefährdung" wire:model="newHazard.description" placeholder="konkrete Gefährdung…" />
                    </div>

                    {{-- Katalog-Vorschläge für den gewählten Faktor --}}
                    @if(!empty($catalog[$newHazard['category']]))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($catalog[$newHazard['category']] as $suggestion)
                                <button type="button" wire:click="$set('newHazard.description', @js($suggestion))"
                                        class="text-xs px-2 py-1 rounded-full border border-[color:var(--nx-line)] text-[color:var(--nx-muted)] hover:bg-[color:var(--nx-hover)]">{{ $suggestion }}</button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Risikomatrix --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        <x-nx-input-select name="newHazard.probability" label="Wahrscheinlichkeit (1–5)" wire:model.live="newHazard.probability" :options="$scale" />
                        <x-nx-input-select name="newHazard.severity" label="Schadensschwere (1–5)" wire:model.live="newHazard.severity" :options="$scale" />
                        <div class="flex items-center gap-2 pb-2">
                            <span class="w-3 h-3 rounded-full" style="background: {{ $prevColor }}"></span>
                            <span class="text-sm text-[color:var(--nx-text)]">{{ $prevLabel }}</span>
                            @if($rpz)<span class="text-xs text-[color:var(--nx-faint)]">RPZ {{ $rpz }}</span>@endif
                        </div>
                    </div>

                    {{-- Maßnahme (STOP) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-nx-input-select name="newHazard.measure_type" label="Maßnahmen-Typ (STOP)" wire:model="newHazard.measure_type" :options="$measureOptions" />
                        <x-nx-input-text name="newHazard.responsible" label="Verantwortlich" wire:model="newHazard.responsible" />
                        <div class="sm:col-span-2"><x-nx-input-text name="newHazard.measures" label="Maßnahme" wire:model="newHazard.measures" placeholder="konkrete Schutzmaßnahme…" /></div>
                        <x-nx-input-text name="newHazard.deadline" type="date" label="Frist" wire:model="newHazard.deadline" />
                    </div>

                    <div>
                        <x-nx-button variant="primary" size="sm" wire:click="addHazard">
                            @svg('heroicon-o-plus', 'w-4 h-4')
                            <span>Gefährdung hinzufügen</span>
                        </x-nx-button>
                    </div>
                </div>
            </x-nx-card>
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
