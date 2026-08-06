<?php

use Platform\Customer\Livewire\Dashboard;
use Platform\Customer\Livewire\Company\Index as CompanyIndex;
use Platform\Customer\Livewire\Company\Show as CompanyShow;
use Platform\Customer\Livewire\RiskAssessment\Show as RiskAssessmentShow;

/*
 * Customer — Web-Routes (Prefix 'customer' aus config).
 * Betriebe (external_customer) + Abteilungen (customer_department) als Projektion
 * aus dem Organization-Graphen (nur lesend) + Betrieb-verankerte Fachdaten
 * (Gefährdungsbeurteilungen …) nach dem Anker-Prinzip.
 */

Route::get('/', Dashboard::class)->name('customer.dashboard');
Route::get('/companies', CompanyIndex::class)->name('customer.companies.index');
Route::get('/companies/{company}', CompanyShow::class)->name('customer.companies.show');
Route::get('/risk-assessments/{riskAssessment}', RiskAssessmentShow::class)->name('customer.risk-assessments.show');
