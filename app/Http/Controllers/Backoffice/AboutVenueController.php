<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutVenueRequest;
use App\Models\AboutPage;
use App\Models\AboutVenueDetail;
use App\Services\Backoffice\AboutPageService;
use App\Services\Backoffice\AboutVenueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutVenueController extends Controller
{
    public function __construct(private readonly AboutVenueService $service, private readonly AboutPageService $aboutPageService) {}

    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $perPage = $this->perPage($request);
        $pages = $this->service->getPages($filters, $perPage);

        return view('backoffice.about-pages.index', [...compact('pages', 'filters', 'perPage'), 'context' => $this->context()]);
    }

    public function create(): View
    {
        return view('backoffice.venue.create', $this->formData(new AboutPage));
    }

    public function store(AboutVenueRequest $request): RedirectResponse
    {
        $this->service->createPage($request->safe()->except('return_url'), $request->user()?->id);

        return redirect()->route('backoffice.venue.index')->with('success', 'Venue가 등록되었습니다.');
    }

    public function edit(Request $request, AboutPage $aboutPage): View
    {
        $this->ensureType($aboutPage);
        $aboutPage->load('venueDetail');

        return view('backoffice.venue.edit', [...$this->formData($aboutPage), 'returnUrl' => $this->returnUrl($request)]);
    }

    public function update(AboutVenueRequest $request, AboutPage $aboutPage): RedirectResponse
    {
        $this->ensureType($aboutPage);
        $this->service->updatePage($aboutPage, $request->safe()->except('return_url'), $request->user()?->id);

        return redirect()->to($this->returnUrl($request))->with('success', 'Venue가 수정되었습니다.');
    }

    public function destroy(Request $request, AboutPage $aboutPage): RedirectResponse|JsonResponse
    {
        $this->ensureType($aboutPage);
        $this->service->deletePage($aboutPage);
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->to($this->returnUrl($request))->with('success', 'Venue가 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $data = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'exists:about_pages,id']]);
        $deleted = $this->service->deletePages($data['ids']);

        return response()->json(['success' => true, 'message' => $deleted.'개의 Venue가 삭제되었습니다.']);
    }

    /** @return array<string, mixed> */
    private function formData(AboutPage $page): array
    {
        return ['aboutPage' => $page, 'mainPages' => $this->aboutPageService->mainPageOptions(), 'selectedMainPageId' => $page->exists ? $this->aboutPageService->selectedMainPageId($page) : null, 'formatOptions' => AboutVenueDetail::formatOptions()];
    }

    private function ensureType(AboutPage $page): void
    {
        abort_unless($page->type === AboutPage::TYPE_VENUE, 404);
    }

    private function perPage(Request $request): int
    {
        $value = (int) $request->query('per_page', 10);

        return in_array($value, [10, 20, 50], true) ? $value : 10;
    }

    /** @return array<string, mixed> */
    private function filters(Request $request): array
    {
        return ['is_linked' => $request->query('is_linked', ''), 'created_from' => $request->query('created_from'), 'created_to' => $request->query('created_to'), 'keyword' => trim((string) $request->query('keyword', ''))];
    }

    /** @return array<string, string> */
    private function context(): array
    {
        return ['menu_name' => 'Venue', 'entity_name' => 'Venue', 'route' => 'backoffice.venue'];
    }

    private function returnUrl(Request $request): string
    {
        $url = $request->input('return_url', $request->query('return_url'));
        $index = route('backoffice.venue.index');

        return is_string($url) && str_starts_with($url, $index) ? $url : $index;
    }
}
