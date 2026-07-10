<?php

namespace App\Enums;

enum RiskLevel: string
{
    case LOW = 'low';
    case MODERATE = 'moderate';
    case HIGH = 'high';
}