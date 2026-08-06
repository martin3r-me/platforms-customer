{{--
    Customer · Modul-Sidebar (Haupt-Sidebar links). Führende Navigation = Betrieb-Baum
    (Kunden + Standorte + Abteilungen, beliebig tief). Nur var(--nx-*) Tokens.
--}}

<div>
    <div x-show="!collapsed" class="p-3 text-sm italic text-[var(--nx-text)] uppercase border-b border-[color:var(--nx-line)] mb-2">
        Betriebe
    </div>

    <x-ui-sidebar-list>
        <x-ui-sidebar-item :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')">
            @svg('heroicon-o-home', 'w-4 h-4 text-[var(--nx-text)]')
            <span class="ml-2 text-sm">Dashboard</span>
        </x-ui-sidebar-item>
        <x-ui-sidebar-item :href="route('customer.companies.index')" :active="request()->routeIs('customer.companies.index')">
            @svg('heroicon-o-list-bullet', 'w-4 h-4 text-[var(--nx-text)]')
            <span class="ml-2 text-sm">Alle Betriebe</span>
        </x-ui-sidebar-item>
    </x-ui-sidebar-list>

    @if(!empty($tree))
        <x-ui-sidebar-list label="Betriebe">
            @foreach($tree as $node)
                <x-ui-sidebar-item
                    :href="route('customer.companies.show', $node['id'])"
                    :active="(string) $activeId === (string) $node['id']">
                    <span class="flex items-center gap-2 min-w-0" style="padding-left: {{ ($node['depth'] ?? 0) * 0.75 }}rem">
                        @svg(($node['depth'] ?? 0) === 0 ? 'heroicon-o-building-office-2' : 'heroicon-o-building-office', 'w-4 h-4 text-[var(--nx-text)] shrink-0')
                        <span class="text-sm truncate">{{ $node['name'] }}</span>
                    </span>
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
</div>
