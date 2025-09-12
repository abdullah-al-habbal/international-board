<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel, HasColor
{
    case TrainingOfTrainers = 'training_of_trainers';
    case AccreditationCenter = 'accreditation_center';
    case ExperienceCertificate = 'experience_certificate';
    case AdviserCertificate = 'adviser_certificate';
    case ConsultantTraining = 'consultant_training';
    case SpecializationCertificate = 'specialization_certificate';
    case IcdlCertificate = 'icdl_certificate';
    case BasicCertificate = 'basic_certificate';

    case Certificate = 'certificate';
    case Diploma = 'diploma';
    case License = 'license';
    case Accreditation = 'accreditation';

    public function getLabel(): string
    {
        return match ($this) {
            self::TrainingOfTrainers => 'Training of Trainers (TOT)',
            self::AccreditationCenter => 'Accreditation Center',
            self::ExperienceCertificate => 'Experience Certificate',
            self::AdviserCertificate => 'Adviser Certificate',
            self::ConsultantTraining => 'Consultant Training',
            self::SpecializationCertificate => 'Specialization Certificate',
            self::IcdlCertificate => 'ICDL Certificate',
            self::BasicCertificate => 'Basic Certificate',
            self::Certificate => 'Certificate',
            self::Diploma => 'Diploma',
            self::License => 'License',
            self::Accreditation => 'Accreditation',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TrainingOfTrainers => 'success',
            self::AccreditationCenter => 'primary',
            self::ExperienceCertificate => 'info',
            self::AdviserCertificate => 'warning',
            self::ConsultantTraining => 'warning',
            self::SpecializationCertificate => 'secondary',
            self::IcdlCertificate => 'purple',
            self::BasicCertificate => 'gray',
            self::Certificate => 'gray',
            self::Diploma => 'gray',
            self::License => 'gray',
            self::Accreditation => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::TrainingOfTrainers => 'heroicon-o-academic-cap',
            self::AccreditationCenter => 'heroicon-o-building-office',
            self::ExperienceCertificate => 'heroicon-o-briefcase',
            self::SpecializationCertificate => 'heroicon-o-star',
            self::ConsultantTraining => 'heroicon-o-light-bulb',
            self::AdviserCertificate => 'heroicon-o-user-circle',
            self::IcdlCertificate => 'heroicon-o-computer-desktop',
            self::BasicCertificate => 'heroicon-o-document',
            default => 'heroicon-o-document',
        };
    }

    public static function getImportMapping(): array
    {
        return [
            'Training of Trainers (TOT)' => self::TrainingOfTrainers->value,
            'accreditation center' => self::AccreditationCenter->value,
            'Experience certificate' => self::ExperienceCertificate->value,
            'Adviser' => self::AdviserCertificate->value,
            'Consultant training' => self::ConsultantTraining->value,
            'Chef of Arabic Sweets' => self::SpecializationCertificate->value,
            'ICDL' => self::IcdlCertificate->value,
        ];
    }

    public static function normalize(?string $type): ?string
    {
        if (empty($type)) {
            return self::BasicCertificate->value;
        }

        $type = trim($type);
        $mapping = self::getImportMapping();

        if (isset($mapping[$type])) {
            return $mapping[$type];
        }

        $lowerType = strtolower($type);
        foreach ($mapping as $original => $normalized) {
            if (strtolower($original) === $lowerType) {
                return $normalized;
            }
        }

        if (str_contains($lowerType, 'training of trainers') || str_contains($lowerType, 'tot')) {
            return self::TrainingOfTrainers->value;
        }

        if (str_contains($lowerType, 'accreditation')) {
            return self::AccreditationCenter->value;
        }

        if (str_contains($lowerType, 'experience')) {
            return self::ExperienceCertificate->value;
        }

        if (str_contains($lowerType, 'adviser') || str_contains($lowerType, 'advisor')) {
            return self::AdviserCertificate->value;
        }

        if (str_contains($lowerType, 'consultant')) {
            return self::ConsultantTraining->value;
        }

        if (str_contains($lowerType, 'icdl')) {
            return self::IcdlCertificate->value;
        }

        return strtolower(str_replace([' ', '(', ')', '-'], ['_', '', '', '_'], $type));
    }

    public static function getOptionsArray(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }

    public static function tryFromLabel(string $label): ?self
    {
        $normalizedLabel = strtolower(trim($label));

        $labelMappings = [
            'training of trainers (tot)' => self::TrainingOfTrainers,
            'accreditation center' => self::AccreditationCenter,
            'experience certificate' => self::ExperienceCertificate,
            'chef of arabic sweets' => self::SpecializationCertificate,
            'consultant training' => self::ConsultantTraining,
            'adviser' => self::AdviserCertificate,
            'icdl' => self::IcdlCertificate,
            'certificate' => self::Certificate,
            'diploma' => self::Diploma,
            'license' => self::License,
            'accreditation' => self::Accreditation,
        ];

        return $labelMappings[$normalizedLabel] ?? null;
    }
}
