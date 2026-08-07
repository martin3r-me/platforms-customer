<?php

namespace Platform\Customer\Enums;

/**
 * MeasureType — Rangfolge der Schutzmaßnahmen nach STOP (§4 ArbSchG):
 * Substitution → Technisch → Organisatorisch → Personenbezogen (nachrangig, PSA).
 */
enum MeasureType: string
{
    case Substitution   = 'substitution';
    case Technical      = 'technical';
    case Organizational = 'organizational';
    case Personal       = 'personal';

    public function label(): string
    {
        return match ($this) {
            self::Substitution   => 'Substitution',
            self::Technical      => 'Technisch',
            self::Organizational => 'Organisatorisch',
            self::Personal       => 'Personenbezogen (PSA)',
        };
    }

    /** Kurzzeichen S/T/O/P. */
    public function short(): string
    {
        return match ($this) {
            self::Substitution   => 'S',
            self::Technical      => 'T',
            self::Organizational => 'O',
            self::Personal       => 'P',
        };
    }

    /** Rang (kleiner = wirksamer/bevorzugt). */
    public function rank(): int
    {
        return match ($this) {
            self::Substitution   => 1,
            self::Technical      => 2,
            self::Organizational => 3,
            self::Personal       => 4,
        };
    }
}
