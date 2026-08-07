<?php

/**
 * Customer (Betriebe/Kunden) — verwaltet die Firmen + projiziert sie in den
 * Organization-Graphen (external_customer + customer_department Entities).
 * Der Graph ist der strukturelle Spiegel; hier findet die operative Pflege statt.
 * occupational (Betriebsmedizin) referenziert die Betriebe loose.
 */

return [
    'routing' => [
        'mode'   => env('CUSTOMER_MODE', 'path'),
        'prefix' => 'customer',
    ],

    'guard' => 'web',

    'navigation' => [
        'route' => 'customer.dashboard',
        'icon'  => 'heroicon-o-building-office-2',
        'order' => 33,
    ],

    'sidebar' => [
        [
            'group' => 'Kundenbetriebe',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'customer.dashboard',
                    'icon'  => 'heroicon-o-home',
                ],
                [
                    'label' => 'Betriebe',
                    'route' => 'customer.companies.index',
                    'icon'  => 'heroicon-o-building-office-2',
                ],
            ],
        ],
    ],
];
