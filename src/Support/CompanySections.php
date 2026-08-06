<?php

namespace Platform\Customer\Support;

/**
 * CompanySections — die Funktions-Reiter eines Betriebs als EIGENE Routen.
 * Zentrale Wahrheit für die innere Sidebar (Navigation) UND die Show-Komponente
 * (welcher Abschnitt gerendert wird). Reihenfolge = Klickroute.
 */
class CompanySections
{
    /**
     * @return array<int,array{key:string,label:string,icon:string,route:string}>
     */
    public static function all(): array
    {
        return [
            ['key' => 'overview',     'label' => 'Übersicht',                'icon' => 'heroicon-o-squares-2x2',              'route' => 'customer.companies.show'],
            ['key' => 'patients',     'label' => 'Patienten',                'icon' => 'heroicon-o-user-group',               'route' => 'customer.companies.patients'],
            ['key' => 'risk',         'label' => 'Gefährdungsbeurteilungen', 'icon' => 'heroicon-o-shield-exclamation',       'route' => 'customer.companies.risk-assessments'],
            ['key' => 'care',         'label' => 'Betreuung',                'icon' => 'heroicon-o-clipboard-document-check', 'route' => 'customer.companies.care'],
            ['key' => 'pricing',      'label' => 'Preise',                   'icon' => 'heroicon-o-banknotes',                'route' => 'customer.companies.pricing'],
            ['key' => 'inspections',  'label' => 'Begehungen',               'icon' => 'heroicon-o-clipboard-document-list',  'route' => 'customer.companies.inspections'],
        ];
    }

    /** Route-Name → Section-Key (default 'overview'). */
    public static function keyForRoute(string $routeName): string
    {
        foreach (self::all() as $section) {
            if ($section['route'] === $routeName) {
                return $section['key'];
            }
        }
        return 'overview';
    }
}
