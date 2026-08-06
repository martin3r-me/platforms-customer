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

    <x-ui-tree-nav label="Betriebe" :nodes="$nodes" :activeId="$activeId" />

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
