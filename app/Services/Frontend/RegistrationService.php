<?php

namespace App\Services\Frontend;

use App\Models\RegistrationApplicant;
use App\Models\RegistrationPage;

class RegistrationService
{
    public function isOpen(RegistrationPage $registrationPage): bool
    {
        if ($registrationPage->participation_mode !== RegistrationPage::MODE_PARTICIPATING) {
            return false;
        }

        $today = today();

        return (! $registrationPage->registration_start_date || $today->gte($registrationPage->registration_start_date))
            && (! $registrationPage->registration_end_date || $today->lte($registrationPage->registration_end_date));
    }

    /** @param array<string, mixed> $data */
    public function createApplicant(RegistrationPage $registrationPage, array $data): RegistrationApplicant
    {
        return RegistrationApplicant::query()->create([
            'registration_page_id' => $registrationPage->id,
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'phone' => trim($data['mobile']),
            'affiliation' => $data['affiliation'],
            'position' => $data['position'],
            'participation_type' => $data['attendance_mode'],
            'status' => RegistrationApplicant::STATUS_PENDING,
            'agreed_privacy' => true,
            'submitted_at' => now(),
        ]);
    }

    public function findApplicant(RegistrationPage $registrationPage, string $email, string $mobileLastFour): ?RegistrationApplicant
    {
        return $registrationPage->applicants()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($email))])
            ->where('phone', 'like', '%'.$mobileLastFour)
            ->whereIn('status', [RegistrationApplicant::STATUS_PENDING, RegistrationApplicant::STATUS_APPROVED])
            ->latest('id')
            ->first();
    }
}
