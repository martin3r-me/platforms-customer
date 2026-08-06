<?php

use Platform\Customer\Livewire\Dashboard;
use Platform\Customer\Livewire\Company\Index as CompanyIndex;
use Platform\Customer\Livewire\Company\Show as CompanyShow;
use Platform\Customer\Livewire\RiskAssessment\Show as RiskAssessmentShow;

/*
 * Customer — Web-Routes (Prefix 'customer' aus config).
 *
 * Jede Betrieb-Funktion hat eine EIGENE Route (deep-linkbar); die Navigation
 * läuft über die innere linke Sidebar (kontextabhängig im Betrieb). Alle
 * /companies/{company}/* zeigen auf CompanyShow, das den Abschnitt aus dem
 * Route-Namen ableitet (CompanySections).
 */

Route::get('/', Dashboard::class)->name('customer.dashboard');
Route::get('/companies', CompanyIndex::class)->name('customer.companies.index');

Route::get('/companies/{company}', CompanyShow::class)->name('customer.companies.show');
Route::get('/companies/{company}/gefaehrdungsbeurteilungen', CompanyShow::class)->name('customer.companies.risk-assessments');
Route::get('/companies/{company}/beschaeftigte', CompanyShow::class)->name('customer.companies.staff');
Route::get('/companies/{company}/betreuung', CompanyShow::class)->name('customer.companies.care');
Route::get('/companies/{company}/preise', CompanyShow::class)->name('customer.companies.pricing');
Route::get('/companies/{company}/begehungen', CompanyShow::class)->name('customer.companies.inspections');

Route::get('/risk-assessments/{riskAssessment}', RiskAssessmentShow::class)->name('customer.risk-assessments.show');
