<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Registered = 'registered';
    case WaitingForDoctor = 'waiting_for_doctor';
    case WithDoctor = 'with_doctor';
    case WaitingForLab = 'waiting_for_lab';
    case InLab = 'in_lab';
    case LabCompleted = 'lab_completed';
    case WaitingForPharmacy = 'waiting_for_pharmacy';
    case InPharmacy = 'in_pharmacy';
    case PharmacyCompleted = 'pharmacy_completed';
    case WaitingForPayment = 'waiting_for_payment';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Registered => [self::WaitingForDoctor, self::Cancelled],
            self::WaitingForDoctor => [self::WithDoctor, self::Cancelled],
            self::WithDoctor => [self::WaitingForLab, self::WaitingForPharmacy, self::WaitingForPayment, self::Cancelled],
            self::WaitingForLab => [self::InLab],
            self::InLab => [self::LabCompleted],
            self::LabCompleted => [self::WithDoctor],
            self::WaitingForPharmacy => [self::InPharmacy],
            self::InPharmacy => [self::PharmacyCompleted],
            self::PharmacyCompleted => [self::WaitingForPayment],
            self::WaitingForPayment => [self::Completed],
            default => [],
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Registered',
            self::WaitingForDoctor => 'Waiting for Doctor',
            self::WithDoctor => 'With Doctor',
            self::WaitingForLab => 'Waiting for Lab',
            self::InLab => 'In Lab',
            self::LabCompleted => 'Lab Completed',
            self::WaitingForPharmacy => 'Waiting for Pharmacy',
            self::InPharmacy => 'In Pharmacy',
            self::PharmacyCompleted => 'Pharmacy Completed',
            self::WaitingForPayment => 'Waiting for Payment',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
