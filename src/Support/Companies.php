<?php

namespace Platform\Customer\Support;

use Platform\Organization\Models\OrganizationEntity;
use Platform\Organization\Models\OrganizationEntityType;

/**
 * Companies — Firmen als Org-Entities. Liest den Umwelt-Baum (Kunden + Abteilungen)
 * und LEGT Entities an (Projektion in den Graphen). external_customer = Firma,
 * customer_department = Abteilung. Der dimension_value wird vom OrganizationEntity-
 * Observer automatisch mitgezogen (link-fähig).
 */
class Companies
{
    public static function customerTypeId(): ?int
    {
        return OrganizationEntityType::query()->where('code', 'external_customer')->value('id');
    }

    public static function departmentTypeId(): ?int
    {
        return OrganizationEntityType::query()->where('code', 'customer_department')->value('id');
    }

    /**
     * Umwelt-Baum: Kunden-Wurzeln + Nachkommen, depth-annotiert.
     *
     * @return array<int,array{id:int,name:string,type:?string,depth:int}>
     */
    public static function tree(int $teamId): array
    {
        $customerTypeId = self::customerTypeId();
        if (!$customerTypeId) {
            return [];
        }

        $entities = OrganizationEntity::query()
            ->forTeam($teamId)
            ->with('type')
            ->get(['id', 'name', 'parent_entity_id', 'entity_type_id']);

        $byParent = $entities->groupBy('parent_entity_id');
        $roots = $entities->where('entity_type_id', (int) $customerTypeId)->sortBy('name')->values();

        $rows = [];
        $walk = function ($node, int $depth) use (&$walk, $byParent, &$rows): void {
            $rows[] = ['id' => (int) $node->id, 'name' => $node->name, 'type' => $node->type?->name, 'depth' => $depth];
            foreach (($byParent[$node->id] ?? collect())->sortBy('name') as $child) {
                $walk($child, $depth + 1);
            }
        };
        foreach ($roots as $root) {
            $walk($root, 0);
        }

        return $rows;
    }

    public static function createCompany(string $name, int $teamId, ?int $userId): OrganizationEntity
    {
        return OrganizationEntity::create([
            'team_id'        => $teamId,
            'user_id'        => $userId,
            'name'           => $name,
            'entity_type_id' => self::customerTypeId(),
            'is_active'      => true,
        ]);
    }

    public static function createDepartment(string $name, int $parentId, int $teamId, ?int $userId): OrganizationEntity
    {
        return OrganizationEntity::create([
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'name'             => $name,
            'entity_type_id'   => self::departmentTypeId(),
            'parent_entity_id' => $parentId,
            'is_active'        => true,
        ]);
    }
}
