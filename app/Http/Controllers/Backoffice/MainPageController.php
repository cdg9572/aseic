<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainPageRequest;
use App\Models\Banner;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\Popup;
use App\Models\Speaker;
use App\Services\Backoffice\MainPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class MainPageController extends Controller
{
    public function __construct(private readonly MainPageService $mainPageService) {}

    public function index(Request $request): View
    {
        $filters = [
            'is_visible' => $request->query('is_visible', ''),
            'created_from' => $request->query('created_from'),
            'created_to' => $request->query('created_to'),
            'keyword' => trim((string) $request->query('keyword', '')),
        ];
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;
        $mainPages = $this->mainPageService->getMainPages($filters, $perPage);

        return view('backoffice.main-pages.index', compact('mainPages', 'filters', 'perPage'));
    }

    public function create(): View
    {
        return view('backoffice.main-pages.create', $this->formData(new MainPage([
            'is_visible' => false,
            'use_custom_event_date' => false,
            'programme_items' => array_fill(0, 4, [
                'time' => null,
                'subject' => null,
                'content' => null,
            ]),
        ])));
    }

    public function store(MainPageRequest $request): RedirectResponse
    {
        try {
            $this->mainPageService->createMainPage(
                $this->mainPageData($request),
                $this->uploads($request),
                (array) $request->input('speaker_ids', []),
                (array) $request->input('links', []),
                $request->user()?->id,
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['folder_name' => $exception->getMessage()]);
        }

        return redirect()->route('backoffice.main-pages.index')
            ->with('success', 'Main Page가 등록되었습니다.');
    }

    public function edit(Request $request, MainPage $mainPage): View
    {
        $mainPage->load(['speakers', 'links']);

        return view('backoffice.main-pages.edit', [
            ...$this->formData($mainPage),
            'returnUrl' => $this->returnUrl($request),
        ]);
    }

    public function update(MainPageRequest $request, MainPage $mainPage): RedirectResponse
    {
        $this->mainPageService->updateMainPage(
            $mainPage,
            $this->mainPageData($request),
            $this->uploads($request),
            $this->removals($request),
            (array) $request->input('speaker_ids', []),
            (array) $request->input('links', []),
            $request->user()?->id,
        );

        return $this->redirectAfterMutation($request, 'Main Page가 수정되었습니다.');
    }

    public function destroy(Request $request, MainPage $mainPage): RedirectResponse|JsonResponse
    {
        $this->mainPageService->deleteMainPage($mainPage);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Main Page가 삭제되었습니다.']);
        }

        return $this->redirectAfterMutation($request, 'Main Page가 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:main_pages,id'],
        ]);

        $deleted = $this->mainPageService->deleteMainPages($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'개의 Main Page가 삭제되었습니다.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(MainPage $mainPage): array
    {
        return [
            'mainPage' => $mainPage,
            'banners' => Banner::query()->latest('id')->get(['id', 'title']),
            'popups' => Popup::query()->latest('id')->get(['id', 'title']),
            'speakers' => Speaker::query()->orderBy('first_name')->orderBy('last_name')->get([
                'id',
                'first_name',
                'last_name',
                'position',
                'affiliation',
                'role',
            ]),
            'linkLabels' => MainPageLink::labels(),
            'linkOptions' => $this->mainPageService->linkOptions(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mainPageData(MainPageRequest $request): array
    {
        $data = $request->safe()->except([
            'speaker_ids',
            'links',
            'programme_background',
            'register_background',
            'host_images',
            'organizer_images',
            'co_organizer_images',
            'remove_programme_background',
            'remove_register_background',
            'remove_host_images',
            'remove_organizer_images',
            'remove_co_organizer_images',
            'return_url',
        ]);

        if ($request->boolean('use_custom_event_date')) {
            $data['event_start_date'] = null;
            $data['event_end_date'] = null;
        } else {
            $data['event_date_text'] = null;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function uploads(MainPageRequest $request): array
    {
        return [
            'programme_background' => $request->file('programme_background'),
            'register_background' => $request->file('register_background'),
            'host_images' => (array) $request->file('host_images', []),
            'organizer_images' => (array) $request->file('organizer_images', []),
            'co_organizer_images' => (array) $request->file('co_organizer_images', []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function removals(MainPageRequest $request): array
    {
        return [
            'programme_background' => $request->boolean('remove_programme_background'),
            'register_background' => $request->boolean('remove_register_background'),
            'host_images' => (array) $request->input('remove_host_images', []),
            'organizer_images' => (array) $request->input('remove_organizer_images', []),
            'co_organizer_images' => (array) $request->input('remove_co_organizer_images', []),
        ];
    }

    private function redirectAfterMutation(Request $request, string $message): RedirectResponse
    {
        return redirect()->to($this->returnUrl($request))->with('success', $message);
    }

    private function returnUrl(Request $request): string
    {
        $returnUrl = $request->input('return_url', $request->query('return_url'));
        $indexUrl = route('backoffice.main-pages.index');

        return is_string($returnUrl) && str_starts_with($returnUrl, $indexUrl)
            ? $returnUrl
            : $indexUrl;
    }
}
