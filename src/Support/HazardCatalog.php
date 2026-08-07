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
            // Psychische Belastung — 5 GDA-Merkmalsbereiche (verpflichtend seit 2013).
            HazardCategory::MentalStrain->value => [
                'Arbeitsinhalt: hohes Pensum / Über- oder Unterforderung',
                'Arbeitsinhalt: geringer Handlungs-/Entscheidungsspielraum',
                'Arbeitsorganisation: Zeit-/Termindruck, häufige Unterbrechungen',
                'Arbeitsorganisation: ungünstige Arbeitszeiten (Nacht / Schicht)',
                'Soziale Beziehungen: Konflikte / Mobbing, fehlende Unterstützung',
                'Soziale Beziehungen: mangelndes Feedback / Führungsqualität',
                'Arbeitsumgebung: Lärm / Klima / räumliche Enge (psych. wirksam)',
                'Neue Arbeitsformen: mobiles Arbeiten / ständige Erreichbarkeit',
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
