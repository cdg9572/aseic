<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgrammePageRequest;
use App\Models\Category;
use App\Models\ProgrammePage;
use App\Services\Backoffice\ProgrammePageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgrammePageController extends Controller
{
    public function __construct(private readonly ProgrammePageService $programmePageService) {}

    public function index(Request $request): View
    {
        $context = $this->context($request);
        $categories = isset($context['category_group_code'])
            ? $this->programmePageService->categoriesForGroup($context['category_group_code'])
            : collect();
        $filters = [
            'is_linked' => $request->query('is_linked', ''),
            'created_from' => $request->query('created_from'),
            'created_to' => $request->query('created_to'),
            'keyword' => trim((string) $request->query('keyword', '')),
            'category_id' => $this->selectedCategoryId($request, $categories),
        ];
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;
        $pages = $this->programmePageService->getPages($context['type'], $filters, $perPage);

        return view('backoffice.programme-pages.index', compact('pages', 'filters', 'perPage', 'context', 'categories'));
    }

    public function create(Request $request): View
    {
        $context = $this->context($request);
        $mainPages = $this->programmePageService->mainPageOptions();

        return view('backoffice.programme-pages.create', [
            'context' => $context,
            'mainPages' => $mainPages,
            'selectedMainPageId' => null,
            'categories' => isset($context['category_group_code'])
                ? $this->programmePageService->categoriesForGroup($context['category_group_code'])
                : collect(),
            'selectedCategoryId' => $request->query('category_id'),
            'speakers' => in_array($context['type'], [ProgrammePage::TYPE_SPEAKERS, ProgrammePage::TYPE_ARCHIVE_SPEAKERS], true)
                ? $this->programmePageService->speakerOptions()
                : collect(),
        ]);
    }

    public function store(ProgrammePageRequest $request): RedirectResponse
    {
        $context = $this->context($request);
        $this->programmePageService->createPage(
            $context['type'],
            $request->safe()->except(['sessions', 'books']),
            (array) $request->validated('sessions', []),
            (array) $request->validated('books', []),
            $request->user()?->id,
        );

        return redirect()->route($context['route'].'.index')
            ->with('success', $context['entity_name'].'이 등록되었습니다.');
    }

    public function edit(Request $request, ProgrammePage $programmePage): View
    {
        $context = $this->context($request);
        $this->ensureType($programmePage, $context['type']);
        $programmePage->load(['sessions.speakers', 'books', 'mainPageLink']);

        return view('backoffice.programme-pages.edit', [
            'context' => $context,
            'programmePage' => $programmePage,
            'mainPages' => $this->programmePageService->mainPageOptions(),
            'selectedMainPageId' => $this->programmePageService->selectedMainPageId($programmePage),
            'categories' => isset($context['category_group_code'])
                ? $this->programmePageService->categoriesForGroup($context['category_group_code'])
                : collect(),
            'selectedCategoryId' => $programmePage->category_id,
            'speakers' => in_array($context['type'], [ProgrammePage::TYPE_SPEAKERS, ProgrammePage::TYPE_ARCHIVE_SPEAKERS], true)
                ? $this->programmePageService->speakerOptions()
                : collect(),
            'returnUrl' => $this->returnUrl($request, $context['route']),
        ]);
    }

    public function update(ProgrammePageRequest $request, ProgrammePage $programmePage): RedirectResponse
    {
        $context = $this->context($request);
        $this->ensureType($programmePage, $context['type']);
        $this->programmePageService->updatePage(
            $programmePage,
            $request->safe()->except(['sessions', 'books']),
            (array) $request->validated('sessions', []),
            (array) $request->validated('books', []),
            $request->user()?->id,
        );

        return redirect()->to($this->returnUrl($request, $context['route']))
            ->with('success', $context['entity_name'].' 정보가 수정되었습니다.');
    }

    public function destroy(Request $request, ProgrammePage $programmePage): RedirectResponse|JsonResponse
    {
        $context = $this->context($request);
        $this->ensureType($programmePage, $context['type']);
        $this->programmePageService->deletePage($programmePage);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $context['entity_name'].'이 삭제되었습니다.']);
        }

        return redirect()->to($this->returnUrl($request, $context['route']))
            ->with('success', $context['entity_name'].'이 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $context = $this->context($request);
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:programme_pages,id'],
        ]);
        $deleted = $this->programmePageService->deletePages($context['type'], $validated['ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'개의 '.$context['entity_name'].' 항목이 삭제되었습니다.',
        ]);
    }

    /** @return array{type: string, menu_name: string, entity_name: string, route: string, form: string, show_event_fields?: bool, content_label?: string, category_group_code?: string, category_group_name?: string} */
    private function context(Request $request): array
    {
        $type = (string) $request->route('programme_type');

        return match ($type) {
            ProgrammePage::TYPE_THEME => ['type' => $type, 'menu_name' => 'Theme 관리', 'entity_name' => 'Theme', 'route' => 'backoffice.programme-theme', 'form' => 'content'],
            ProgrammePage::TYPE_PROGRAMME => ['type' => $type, 'menu_name' => 'Programme 관리', 'entity_name' => 'Programme', 'route' => 'backoffice.programme', 'form' => 'content', 'show_event_fields' => false],
            ProgrammePage::TYPE_SPEAKERS => ['type' => $type, 'menu_name' => 'Speakers', 'entity_name' => 'Speakers', 'route' => 'backoffice.programme-speakers', 'form' => 'speakers'],
            ProgrammePage::TYPE_BOOK => ['type' => $type, 'menu_name' => 'Programme Book 관리', 'entity_name' => 'Programme Book', 'route' => 'backoffice.programme-book', 'form' => 'book'],
            ProgrammePage::TYPE_ARCHIVE_THEME => ['type' => $type, 'menu_name' => 'Past Forums (2025~) - Theme', 'entity_name' => 'Past Forums (2025~) - Theme', 'route' => 'backoffice.archive-theme', 'form' => 'content', 'content_label' => 'Theme', 'category_group_code' => Category::GROUP_CODE_ARCHIVE_THEME, 'category_group_name' => 'Theme'],
            ProgrammePage::TYPE_ARCHIVE_PROGRAMME => ['type' => $type, 'menu_name' => 'Past Forums (2025~) - Programme', 'entity_name' => 'Past Forums (2025~) - Programme', 'route' => 'backoffice.archive-programme', 'form' => 'content', 'content_label' => 'Theme', 'category_group_code' => Category::GROUP_CODE_ARCHIVE_PROGRAMME, 'category_group_name' => 'Programme'],
            ProgrammePage::TYPE_ARCHIVE_SPEAKERS => ['type' => $type, 'menu_name' => 'Past Forums (2025~) - Speakers', 'entity_name' => 'Past Forums (2025~) - Speakers', 'route' => 'backoffice.archive-speakers', 'form' => 'speakers'],
            ProgrammePage::TYPE_ARCHIVE_LEGACY => ['type' => $type, 'menu_name' => 'Past Forums (2015~2024)', 'entity_name' => 'Past Forums (2015~2024)', 'route' => 'backoffice.archive-legacy', 'form' => 'content', 'show_event_fields' => false, 'content_label' => '내용'],
            default => abort(404),
        };
    }

    private function ensureType(ProgrammePage $page, string $type): void
    {
        abort_unless($page->type === $type, 404);
    }

    /** @param \Illuminate\Support\Collection<int, Category> $categories */
    private function selectedCategoryId(Request $request, \Illuminate\Support\Collection $categories): string
    {
        $requestedCategoryId = $request->query('category_id', '');
        if (! is_scalar($requestedCategoryId) || ! ctype_digit((string) $requestedCategoryId)) {
            return '';
        }

        return $categories->contains('id', (int) $requestedCategoryId)
            ? (string) $requestedCategoryId
            : '';
    }

    private function returnUrl(Request $request, string $route): string
    {
        $returnUrl = $request->input('return_url', $request->query('return_url'));
        $indexUrl = route($route.'.index');

        return is_string($returnUrl) && str_starts_with($returnUrl, $indexUrl)
            ? $returnUrl
            : $indexUrl;
    }
}
