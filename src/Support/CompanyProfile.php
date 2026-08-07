<?php

namespace Platform\Customer\Support;

use Illuminate\Database\Eloquent\Relations\Relation;
use Platform\Organization\Services\EntityDimensionBridge;
use Platform\Organization\Services\EntityLinkRegistry;

/**
 * CompanyProfile — löst die am Betrieb-Knoten hängende CRM-Firma (crm_company) GRAPH-NATIV
 * auf und liefert deren Anzeige-Metadaten. customer kennt CRMs Model NICHT — es fragt die
 * organization (Pflicht-Backbone) und rendert, was der crm_company-Provider zurückgibt.
 * Graceful: kein CRM / kein Link → null.
 */
class CompanyProfile
{
    /** @return array<string,mixed>|null CRM-Stammdaten der verknüpften crm_company, sonst null. */
    public static function crmForEntity(?int $entityId): ?array
    {
        if (!$entityId) {
            return null;
        }

        try {
            $links = EntityDimensionBridge::linksForEntities([$entityId]);
            $reverse = array_flip(Relation::morphMap());

            $companyIds = [];
            foreach ($links as $link) {
                $alias = $reverse[$link->linkable_type] ?? $link->linkable_type;
                if ($alias === 'crm_company') {
                    $companyIds[] = $link->linkable_id;
                }
            }
            if (empty($companyIds)) {
                return null;
            }

            $fqcn = Relation::getMorphedModel('crm_company');
            if (!$fqcn || !class_exists($fqcn)) {
                return null; // CRM nicht installiert
            }

            $provider = resolve(EntityLinkRegistry::class)->getProvider('crm_company');
            if (!$provider) {
                return null;
            }

            $query = $fqcn::whereIn('id', array_unique($companyIds));
            $provider->applyEagerLoading($query, 'crm_company', $fqcn);
            $model = $query->first();
            if (!$model) {
                return null;
            }

            $meta = $provider->extractMetadata('crm_company', $model);

            return !empty($meta) ? $meta : null;
        } catch (\Throwable $e) {
            // organization/CRM nicht verfügbar oder Schema-Drift — Steckbrief zeigt nur Basis.
            return null;
        }
    }
}
