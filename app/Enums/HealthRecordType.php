<?php

namespace App\Enums;

enum HealthRecordType: string
{
    case Vaccination = 'vaccination';
    case Deworming = 'deworming';
    case Weight = 'weight';
    case Checkup = 'checkup';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.health_record_type.'.$this->value);
    }
}
