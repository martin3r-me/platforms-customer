<?php

namespace Platform\Customer\Organization;

use Illuminate\Database\Eloquent\Builder;
use Platform\Organization\Contracts\EntityLinkProvider;
use Platform\Customer\Models\RiskAssessment;

/**
 * Rendert Gefährdungsbeurteilungen reich am Betrieb-Org-Entity (dimension_link-Alias
 * "customer_risk_assessment"). Vorbild: OccupationalEntityLinkProvider.
 */
class CustomerEntityLinkProvider implements EntityLinkProvider
{
    public function morphAliases(): array
    {
        return ['customer_risk_assessment'];
    }

    public function linkTypeConfig(): array
    {
        return [
            'customer_risk_assessment' => [
                'label'    => 'Gefährdungsbeurteilungen',
                'singular' => 'Gefährdungsbeurteilung',
                'icon'     => 'shield-exclamation',
                'route'    => 'customer.risk-assessments.show',
            ],
        ];
    }

    public function applyEagerLoading(Builder $query, string $morphAlias, string $fqcn): void
    {
        // keine Eager-Loads nötig
    }

    public function extractMetadata(string $morphAlias, mixed $model): array
    {
        if ($morphAlias !== 'customer_risk_assessment' || !$model instanceof RiskAssessment) {
            return [];
        }

        return [
            'work_area' => $model->work_area,
            'status'    => $model->status?->label(),
        ];
    }

    public function metadataDisplayRules(): array
    {
        return [
            'customer_risk_assessment' => [
                ['field' => 'work_area', 'format' => 'text'],
                ['field' => 'status', 'format' => 'badge'],
            ],
        ];
    }

    public function timeTrackableCascades(): array
    {
        return [];
    }

    public function metrics(string $morphAlias, array $linksByEntity): array
    {
        if ($morphAlias !== 'customer_risk_assessment') {
            return [];
        }

        $result = [];
        foreach ($linksByEntity as $entityId => $ids) {
            $result[$entityId] = [
                'customer_risk_assessments_count' => is_countable($ids) ? count($ids) : 0,
            ];
        }
        return $result;
    }

    public function activityChildren(string $morphAlias, array $linkableIds): array
    {
        return [];
    }
}
