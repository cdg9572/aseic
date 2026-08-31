<?php

namespace App\Services\Backoffice;

use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\RegistrationApplicant;
use App\Models\RegistrationPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function __construct(private readonly MainPageService $mainPageService) {}

    /** @param array<string, mixed> $filters */
    public function getPages(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = RegistrationPage::query()->with(['creator:id,name', 'mainPageLink.mainPage:id,folder_name,event_name']);

        if (($filters['is_linked'] ?? '') !== '') {
            $filters['is_linked'] === '1' ? $query->whereHas('mainPageLink') : $query->whereDoesntHave('mainPageLink');
        }
        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('page_title', 'like', '%'.$keyword.'%')
                    ->orWhere('period_text', 'like', '%'.$keyword.'%')
                    ->orWhereHas('mainPageLink.mainPage', fn ($mainPage) => $mainPage->where('folder_name', 'like', '%'.$keyword.'%')->orWhere('event_name', 'like', '%'.$keyword.'%'));
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    public function mainPages(): Collection
    {
        return MainPage::query()->latest('id')->get(['id', 'folder_name', 'event_name']);
    }

    /** @param array<string, mixed> $data */
    public function createPage(array $data, ?int $adminId): RegistrationPage
    {
        return DB::transaction(function () use ($data, $adminId): RegistrationPage {
            $mainPageId = $this->pullMainPageId($data);
            $page = RegistrationPage::query()->create([...$data, 'created_by' => $adminId, 'updated_by' => $adminId]);
            $this->syncMainPage($page, $mainPageId);

            return $page->fresh(['mainPageLink.mainPage']);
        });
    }

    /** @param array<string, mixed> $data */
    public function updatePage(RegistrationPage $page, array $data, ?int $adminId): RegistrationPage
    {
        DB::transaction(function () use ($page, $data, $adminId): void {
            $mainPageId = $this->pullMainPageId($data);
            $page->update([...$data, 'updated_by' => $adminId]);
            $this->syncMainPage($page, $mainPageId);
        });

        return $page->fresh(['mainPageLink.mainPage']);
    }

    public function deletePage(RegistrationPage $page): void
    {
        DB::transaction(function () use ($page): void {
            $page->mainPageLink()->delete();
            $page->delete();
        });
    }

    /** @param array<int, int|string> $ids */
    public function deletePages(array $ids): int
    {
        $pages = RegistrationPage::query()->whereIn('id', $ids)->get();
        foreach ($pages as $page) {
            $this->deletePage($page);
        }

        return $pages->count();
    }

    /** @param array<string, mixed> $filters */
    public function getApplicants(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = RegistrationApplicant::query()->with('registrationPage:id,page_title');

        if (! empty($filters['registration_page_id'])) {
            $query->where('registration_page_id', $filters['registration_page_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(fn ($builder) => $builder->where('name', 'like', '%'.$keyword.'%')->orWhere('email', 'like', '%'.$keyword.'%')->orWhere('affiliation', 'like', '%'.$keyword.'%'));
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function saveApplicant(?RegistrationApplicant $applicant, array $data, ?int $adminId): RegistrationApplicant
    {
        unset($data['return_url']);
        $data['updated_by'] = $adminId;
        $data['submitted_at'] ??= now();

        if ($applicant) {
            $applicant->update($data);

            return $applicant->fresh('registrationPage');
        }

        return RegistrationApplicant::query()->create($data)->fresh('registrationPage');
    }

    /** @param array<int, int|string> $ids */
    public function deleteApplicants(array $ids): int
    {
        return RegistrationApplicant::query()->whereIn('id', $ids)->delete();
    }

    /** @param array<string, mixed> $data */
    private function pullMainPageId(array &$data): ?int
    {
        $mainPageId = isset($data['main_page_id']) && $data['main_page_id'] !== '' ? (int) $data['main_page_id'] : null;
        unset($data['main_page_id'], $data['return_url']);

        return $mainPageId;
    }

    private function syncMainPage(RegistrationPage $page, ?int $mainPageId): void
    {
        $page->mainPageLink()->where('slot', MainPageLink::SLOT_REGISTRATION)->delete();
        if ($mainPageId) {
            $this->mainPageService->mapContent(MainPage::query()->findOrFail($mainPageId), MainPageLink::SLOT_REGISTRATION, $page);
        }
    }
}
