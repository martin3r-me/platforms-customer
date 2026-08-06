{{--
    Customer · innere linke Sidebar (nx). Kontextabhängig: Betrieb-Drill-down vs. Default.
    Nur var(--nx-*) Tokens.
--}}

<div>
    @if($inCompany)
        {{-- ═══ Im Betrieb: Drill-down mit Funktions-Reitern (eigene Routen) ═══ --}}
        <div x-show="!collapsed" class="px-2 pt-2">
            <a href="{{ route('customer.companies.index') }}" wire:navigate
               class="flex items-center gap-2 px-1 py-1 text-xs text-[color:var(--nx-muted)] hover:text-[color:var(--nx-text)]">
                @svg('heroicon-o-chevron-left', 'w-3.5 h-3.5')
                <span>Alle Betriebe</span>
            </a>
            <div class="px-1 pt-2 pb-1">
                <div class="text-sm font-medium text-[color:var(--nx-text)] truncate">{{ $company->name }}</div>
                <div class="text-xs text-[color:var(--nx-faint)] truncate">
                    {{ $parent ? $parent->name . ' · ' : '' }}{{ $company->type?->name }}
                </div>
            </div>
        </div>

        <x-ui-sidebar-list>
            @foreach($sections as $s)
                <x-ui-sidebar-item :href="route($s['route'], $company->id)" :active="$activeKey === $s['key']">
                    @svg($s['icon'], 'w-4 h-4 text-[var(--nx-text)]')
                    <span class="ml-2 text-sm">{{ $s['label'] }}</span>
                </x-ui-sidebar-item>
            @endforeach
        </x-ui-sidebar-list>

        {{-- collapsed: Reiter-Icons --}}
        <div x-show="collapsed" class="px-2 py-2 border-b border-[color:var(--nx-line)]">
            <div class="flex flex-col gap-2">
                <a href="{{ route('customer.companies.index') }}" wire:navigate
                   class="flex items-center justify-center p-2 rounded-md text-[var(--nx-text)] hover:bg-[var(--nx-bg)]">
                    @svg('heroicon-o-chevron-left', 'w-5 h-5')
                </a>
                @foreach($sections as $s)
                    <a href="{{ route($s['route'], $company->id) }}" wire:navigate
                       class="flex items-center justify-center p-2 rounded-md hover:bg-[var(--nx-bg)] {{ $activeKey === $s['key'] ? 'text-[color:var(--nx-accent)]' : 'text-[var(--nx-text)]' }}">
                        @svg($s['icon'], 'w-5 h-5')
                    </a>
                @endforeach
            </div>
        </div>
    @else
        {{-- ═══ Default: Modul-Navigation ═══ --}}
        <div x-show="!collapsed" class="p-3 text-sm italic text-[var(--nx-text)] uppercase border-b border-[color:var(--nx-line)] mb-2">
            Betriebe
        </div>

        <x-ui-sidebar-list label="Betriebe">
            <x-ui-sidebar-item :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')">
                @svg('heroicon-o-home', 'w-4 h-4 text-[var(--nx-text)]')
                <span class="ml-2 text-sm">Dashboard</span>
            </x-ui-sidebar-item>
            <x-ui-sidebar-item :href="route('customer.companies.index')" :active="request()->routeIs('customer.companies.index')">
                @svg('heroicon-o-building-office-2', 'w-4 h-4 text-[var(--nx-text)]')
                <span class="ml-2 text-sm">Betriebe</span>
            </x-ui-sidebar-item>
        </x-ui-sidebar-list>

        @if($companies->isNotEmpty())
            <x-ui-sidebar-list label="Betriebe">
                @foreach($companies as $company)
                    <x-ui-sidebar-item :href="route('customer.companies.show', $company->id)">
                        @svg('heroicon-o-building-office-2', 'w-4 h-4 text-[var(--nx-text)]')
                        <span class="ml-2 text-sm truncate">{{ $company->name }}</span>
                    </x-ui-sidebar-item>
                @endforeach
            </x-ui-sidebar-list>
        @endif

        <div x-show="collapsed" class="px-2 py-2 border-b border-[color:var(--nx-line)]">
            <div class="flex flex-col gap-2">
                <a href="{{ route('customer.dashboard') }}" wire:navigate class="flex items-center justify-center p-2 rounded-md text-[var(--nx-text)] hover:bg-[var(--nx-bg)]">
                    @svg('heroicon-o-home', 'w-5 h-5')
                </a>
                <a href="{{ route('customer.companies.index') }}" wire:navigate class="flex items-center justify-center p-2 rounded-md text-[var(--nx-text)] hover:bg-[var(--nx-bg)]">
                    @svg('heroicon-o-building-office-2', 'w-5 h-5')
                </a>
            </div>
        </div>
    @endif
</div>
