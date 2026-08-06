<?php

namespace Platform\Customer\Livewire\Company;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Organization\Models\OrganizationEntity;
use Platform\Customer\Models\RiskAssessment;

/**
 * Betrieb/Abteilung-Detail — Betriebs-Cockpit mit Tab-Navigation (deep-linkable via ?tab=).
 *
 * Ein Reiter je Betrieb-Ebenen-Funktion (Anker-Prinzip). Real: Übersicht +
 * Gefährdungsbeurteilungen. Platzhalter (Klickroute/UX zuerst): Beschäftigte,
 * Betreuung, Preise, Begehungen — Logik folgt später.
 */
class Show extends Component
{
    /** Tab-Definition: key => [label, icon]. Reihenfolge = Klickroute. */
    public const TABS = [
        'overview'     => ['Übersicht',                'heroicon-o-squares-2x2'],
        'risk'         => ['Gefährdungsbeurteilungen', 'heroicon-o-shield-exclamation'],
        'staff'        => ['Beschäftigte',             'heroicon-o-users'],
        'care'         => ['Betreuung',                'heroicon-o-clipboard-document-check'],
        'pricing'      => ['Preise',                   'heroicon-o-banknotes'],
        'inspections'  => ['Begehungen',               'heroicon-o-clipboard-document-list'],
    ];

    #[Locked]
    public int $entityId;

    #[Url(as: 'tab', history: true)]
    public string $tab = 'overview';

    public bool $showCreate = false;
    public string $newTitle = '';
    public string $newWorkArea = '';

    public function mount(int $company): void
    {
        $this->entityId = $this->resolve($company)->id;

        if (!array_key_exists($this->tab, self::TABS)) {
            $this->tab = 'overview';
        }
    }

    protected function resolve(int $id): OrganizationEntity
    {
        $team = (int) Auth::user()->currentTeam->id;

        return OrganizationEntity::query()->forTeam($team)->findOrFail($id);
    }

    public function createRiskAssessment(): void
    {
        $title = trim($this->newTitle);
        if ($title === '') {
            return;
        }

        RiskAssessment::create([
            'organization_entity_id' => $this->entityId,
            'title'                  => $title,
            'work_area'              => trim($this->newWorkArea) ?: null,
            'created_by_user_id'     => Auth::id(),
        ]);

        $this->reset(['newTitle', 'newWorkArea']);
        $this->showCreate = false;
    }

    public function render()
    {
        $team = (int) Auth::user()->currentTeam->id;

        $entity = $this->resolve($this->entityId)->load('type');

        $parent = $entity->parent_entity_id
            ? OrganizationEntity::query()->whereKey($entity->parent_entity_id)->first(['id', 'name'])
            : null;

        $children = OrganizationEntity::query()
            ->forTeam($team)
            ->where('parent_entity_id', $entity->id)
            ->with('type')
            ->orderBy('name')
            ->get();

        $riskAssessments = RiskAssessment::query()
            ->forTeam($team)
            ->where('organization_entity_id', $entity->id)
            ->orderByDesc('id')
            ->get();

        return view('customer::livewire.company.show', [
            'entity'          => $entity,
            'parent'          => $parent,
            'children'        => $children,
            'riskAssessments' => $riskAssessments,
            'tabs'            => self::TABS,
        ])->layout('platform::layouts.app');
    }
}
