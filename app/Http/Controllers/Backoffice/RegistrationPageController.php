<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationPageRequest;
use App\Models\RegistrationPage;
use App\Services\Backoffice\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationPageController extends Controller
{
    public function __construct(private readonly RegistrationService $service) {}

    public function index(Request $request): View
    {
        $filters = ['is_linked' => $request->query('is_linked', ''), 'created_from' => $request->query('created_from'), 'created_to' => $request->query('created_to'), 'keyword' => trim((string) $request->query('keyword', ''))];
        $perPage = in_array((int) $request->query('per_page', 10), [10, 20, 50], true) ? (int) $request->query('per_page', 10) : 10;
        $pages = $this->service->getPages($filters, $perPage);

        return view('backoffice.registration-pages.index', compact('pages', 'filters', 'perPage'));
    }

    public function create(): View
    {
        return view('backoffice.registration-pages.create', ['mainPages' => $this->service->mainPages(), 'selectedMainPageId' => null]);
    }

    public function store(RegistrationPageRequest $request): RedirectResponse
    {
        $this->service->createPage($request->validated(), $request->user()?->id);

        return redirect()->route('backoffice.registration.index')->with('success', 'Registration이 등록되었습니다.');
    }

    public function edit(Request $request, RegistrationPage $registrationPage): View
    {
        $registrationPage->load('mainPageLink');

        return view('backoffice.registration-pages.edit', [
            'registrationPage' => $registrationPage,
            'mainPages' => $this->service->mainPages(),
            'selectedMainPageId' => $registrationPage->mainPageLink?->main_page_id,
            'returnUrl' => $this->returnUrl($request),
        ]);
    }

    public function update(RegistrationPageRequest $request, RegistrationPage $registrationPage): RedirectResponse
    {
        $this->service->updatePage($registrationPage, $request->validated(), $request->user()?->id);

        return redirect()->to($this->returnUrl($request))->with('success', 'Registration 정보가 수정되었습니다.');
    }

    public function destroy(Request $request, RegistrationPage $registrationPage): JsonResponse|RedirectResponse
    {
        $this->service->deletePage($registrationPage);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Registration이 삭제되었습니다.']);
        }

        return redirect()->route('backoffice.registration.index')->with('success', 'Registration이 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'exists:registration_pages,id']]);
        $count = $this->service->deletePages($validated['ids']);

        return response()->json(['success' => true, 'message' => $count.'개의 Registration 항목이 삭제되었습니다.']);
    }

    private function returnUrl(Request $request): string
    {
        $indexUrl = route('backoffice.registration.index');
        $url = $request->input('return_url', $request->query('return_url'));

        return is_string($url) && str_starts_with($url, $indexUrl) ? $url : $indexUrl;
    }
}
