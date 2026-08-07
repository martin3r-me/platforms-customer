<?php

namespace Platform\Customer\Support;

use Platform\Customer\Enums\HazardCategory;

/**
 * HazardCatalog — Beispiel-Gefährdungen je GDA-Faktorengruppe (Anhang 2 GDA-Leitlinie /
 * DGUV Information 203-063). Nicht abschließend — dient als Typeahead/Vorschlag bei der
 * strukturierten Erfassung. Freitext bleibt möglich.
 */
class HazardCatalog
{
    /** @return array<string,array<int,string>> keyed by HazardCategory->value */
    public static function all(): array
    {
        return [
            HazardCategory::Mechanical->value => [
                'Ungeschützt bewegte Maschinenteile',
                'Scharfe Kanten / raue Oberflächen',
                'Sturz, Ausrutschen, Stolpern',
                'Absturz (Höhe)',
                'Teile mit gefährlichen Oberflächen',
            ],
            HazardCategory::Electrical->value => [
                'Elektrischer Schlag / Körperdurchströmung',
                'Störlichtbogen',
                'Elektrostatische Aufladung',
            ],
            HazardCategory::HazardousSubstances->value => [
                'Hautkontakt mit Gefahrstoffen (inkl. Feuchtarbeit)',
                'Einatmen von Stäuben / Rauchen / Dämpfen',
                'Verschlucken von Gefahrstoffen',
            ],
            HazardCategory::Biological->value => [
                'Infektionsgefährdung (Bakterien / Viren / Pilze)',
                'Sensibilisierende / toxische Wirkung',
                'Endotoxine',
            ],
            HazardCategory::FireExplosion->value => [
                'Brennbare Feststoffe / Flüssigkeiten / Gase',
                'Explosionsfähige Atmosphäre',
                'Explosivstoffe',
            ],
            HazardCategory::Thermal->value => [
                'Heiße Medien / Oberflächen',
                'Kalte Medien / Oberflächen',
            ],
            HazardCategory::Physical->value => [
                'Lärm',
                'Hand-Arm-Vibration',
                'Ganzkörper-Vibration',
                'Optische / ionisierende Strahlung',
                'Über- / Unterdruck',
            ],
            HazardCategory::WorkEnvironment->value => [
                'Beleuchtung / Lichtverhältnisse',
                'Klima / Lüftung',
                'Flucht- und Verkehrswege',
                'Bewegungsfläche am Arbeitsplatz',
                'Bildschirmarbeitsplatz (Ergonomie)',
            ],
            HazardCategory::PhysicalStrain->value => [
                'Schwere dynamische Arbeit (Lastenhandhabung)',
                'Einseitige / repetitive Belastung',
                'Zwangshaltung',
            ],
            HazardCategory::MentalStrain->value => [
                'Hohes Arbeitspensum / Zeitdruck',
                'Geringer Handlungsspielraum',
                'Soziale Konflikte / Mobbing',
                'Ungünstige Arbeitszeiten (Nacht / Schicht)',
            ],
            HazardCategory::Other->value => [
                'Gefährdung durch Menschen (Überfall)',
                'Gefährdung durch Tiere (Biss / Zeckenstich)',
                'Gefährdung durch Pflanzen (toxisch / sensibilisierend)',
            ],
        ];
    }

    /** Beispiele einer Kategorie. */
    public static function for(HazardCategory|string $category): array
    {
        $key = $category instanceof HazardCategory ? $category->value : $category;

        return self::all()[$key] ?? [];
    }
}
