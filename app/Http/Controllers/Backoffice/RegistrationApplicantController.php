<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationApplicantRequest;
use App\Models\RegistrationApplicant;
use App\Models\RegistrationPage;
use App\Services\Backoffice\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationApplicantController extends Controller
{
    public function __construct(private readonly RegistrationService $service) {}

    public function index(Request $request): View
    {
        $filters = ['registration_page_id' => $request->query('registration_page_id', ''), 'status' => $request->query('status', ''), 'created_from' => $request->query('created_from'), 'created_to' => $request->query('created_to'), 'keyword' => trim((string) $request->query('keyword', ''))];
        $perPage = in_array((int) $request->query('per_page', 10), [10, 20, 50], true) ? (int) $request->query('per_page', 10) : 10;
        $applicants = $this->service->getApplicants($filters, $perPage);
        $registrationPages = RegistrationPage::query()->latest('id')->get(['id', 'page_title']);

        return view('backoffice.registration-applicants.index', compact('applicants', 'registrationPages', 'filters', 'perPage'));
    }

    public function create(): View
    {
        return view('backoffice.registration-applicants.create', ['registrationPages' => RegistrationPage::query()->latest('id')->get(['id', 'page_title'])]);
    }

    public function store(RegistrationApplicantRequest $request): RedirectResponse
    {
        $this->service->saveApplicant(null, $request->validated(), $request->user()?->id);

        return redirect()->route('backoffice.registration-applicants.index')->with('success', '신청자가 등록되었습니다.');
    }

    public function edit(Request $request, RegistrationApplicant $registrationApplicant): View
    {
        return view('backoffice.registration-applicants.edit', [
            'registrationApplicant' => $registrationApplicant,
            'registrationPages' => RegistrationPage::query()->latest('id')->get(['id', 'page_title']),
            'returnUrl' => $this->returnUrl($request),
        ]);
    }

    public function update(RegistrationApplicantRequest $request, RegistrationApplicant $registrationApplicant): RedirectResponse
    {
        $this->service->saveApplicant($registrationApplicant, $request->validated(), $request->user()?->id);

        return redirect()->to($this->returnUrl($request))->with('success', '신청자 정보가 수정되었습니다.');
    }

    public function destroy(Request $request, RegistrationApplicant $registrationApplicant): JsonResponse|RedirectResponse
    {
        $registrationApplicant->delete();
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '신청자가 삭제되었습니다.']);
        }

        return redirect()->route('backoffice.registration-applicants.index')->with('success', '신청자가 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'exists:registration_applicants,id']]);
        $count = $this->service->deleteApplicants($validated['ids']);

        return response()->json(['success' => true, 'message' => $count.'명의 신청자가 삭제되었습니다.']);
    }

    private function returnUrl(Request $request): string
    {
        $indexUrl = route('backoffice.registration-applicants.index');
        $url = $request->input('return_url', $request->query('return_url'));

        return is_string($url) && str_starts_with($url, $indexUrl) ? $url : $indexUrl;
    }
}
