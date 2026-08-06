<?php

use Platform\Customer\Livewire\Dashboard;
use Platform\Customer\Livewire\Company\Index as CompanyIndex;
use Platform\Customer\Livewire\Company\Show as CompanyShow;

/*
 * Customer — Web-Routes (Prefix 'customer' aus config).
 * Betriebe (external_customer) + Abteilungen (customer_department) als
 * Projektion in den Organization-Graphen.
 */

Route::get('/', Dashboard::class)->name('customer.dashboard');
Route::get('/companies', CompanyIndex::class)->name('customer.companies.index');
Route::get('/companies/{company}', CompanyShow::class)->name('customer.companies.show');
