<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaContentRequest;
use App\Models\Category;
use App\Models\MediaContent;
use App\Services\Backoffice\MediaContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaContentController extends Controller
{
    public function __construct(private readonly MediaContentService $service) {}

    public function index(Request $request): View
    {
        return $this->renderIndex($request, null);
    }

    public function nestedIndex(Request $request, MediaContent $mediaParent): View
    {
        $this->ensureParent($request, $mediaParent);

        return $this->renderIndex($request, $mediaParent);
    }

    public function create(Request $request): View
    {
        return $this->renderCreate($request, null);
    }

    public function nestedCreate(Request $request, MediaContent $mediaParent): View
    {
        $this->ensureParent($request, $mediaParent);

        return $this->renderCreate($request, $mediaParent);
    }

    public function store(MediaContentRequest $request): RedirectResponse
    {
        return $this->storeContent($request, null);
    }

    public function nestedStore(MediaContentRequest $request, MediaContent $mediaParent): RedirectResponse
    {
        $this->ensureParent($request, $mediaParent);

        return $this->storeContent($request, $mediaParent);
    }

    public function edit(Request $request, MediaContent $mediaContent): View
    {
        return $this->renderEdit($request, null, $mediaContent);
    }

    public function nestedEdit(Request $request, MediaContent $mediaParent, MediaContent $mediaContent): View
    {
        $this->ensureParent($request, $mediaParent);
        abort_unless((int) $mediaContent->parent_id === $mediaParent->id, 404);

        return $this->renderEdit($request, $mediaParent, $mediaContent);
    }

    public function update(MediaContentRequest $request, MediaContent $mediaContent): RedirectResponse
    {
        return $this->updateContent($request, null, $mediaContent);
    }

    public function nestedUpdate(MediaContentRequest $request, MediaContent $mediaParent, MediaContent $mediaContent): RedirectResponse
    {
        $this->ensureParent($request, $mediaParent);
        abort_unless((int) $mediaContent->parent_id === $mediaParent->id, 404);

        return $this->updateContent($request, $mediaParent, $mediaContent);
    }

    public function destroy(Request $request, MediaContent $mediaContent): JsonResponse|RedirectResponse
    {
        return $this->destroyContent($request, null, $mediaContent);
    }

    public function nestedDestroy(Request $request, MediaContent $mediaParent, MediaContent $mediaContent): JsonResponse|RedirectResponse
    {
        $this->ensureParent($request, $mediaParent);
        abort_unless((int) $mediaContent->parent_id === $mediaParent->id, 404);

        return $this->destroyContent($request, $mediaParent, $mediaContent);
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        return $this->destroyMany($request, null);
    }

    public function nestedDestroyMultiple(Request $request, MediaContent $mediaParent): JsonResponse
    {
        $this->ensureParent($request, $mediaParent);

        return $this->destroyMany($request, $mediaParent);
    }

    private function renderIndex(Request $request, ?MediaContent $parent): View
    {
        $context = $this->context($request);
        $filters = $this->filters($request);
        $categories = isset($context['category_group_code'])
            ? $this->service->categoriesForGroup($context['category_group_code'])
            : collect();
        if (isset($context['category_group_code'])) {
            $selectedCategoryId = (int) ($filters['category_id'] ?? 0);
            $filters['category_id'] = $categories->contains('id', $selectedCategoryId)
                ? (string) $selectedCategoryId
                : '';
        }
        $perPage = in_array((int) $request->query('per_page', 10), [10, 20, 50], true)
            ? (int) $request->query('per_page', 10)
            : 10;
        $contents = $this->service->getContents($context['type'], $parent?->id, $filters, $perPage);

        return view('backoffice.media-contents.index', compact('context', 'parent', 'filters', 'perPage', 'contents', 'categories'));
    }

    private function renderCreate(Request $request, ?MediaContent $parent): View
    {
        $context = $this->context($request);

        return view('backoffice.media-contents.create', [
            'context' => $context,
            'parent' => $parent,
            'categories' => isset($context['category_group_code'])
                ? $this->service->categoriesForGroup($context['category_group_code'])
                : collect(),
        ]);
    }

    private function storeContent(MediaContentRequest $request, ?MediaContent $parent): RedirectResponse
    {
        $context = $this->context($request);
        $data = $request->safe()->except(['image']);
        if (in_array($context['type'], [MediaContent::TYPE_PHOTO_ITEM, MediaContent::TYPE_NEWS_ITEM], true)) {
            $data['page_title'] = $data['title'];
        }
        $this->service->create($context['type'], $parent, $data, $request->file('image'), $request->user()?->id);

        return redirect()->to($this->indexUrl($context, $parent))->with('success', $context['entity_name'].'이 등록되었습니다.');
    }

    private function renderEdit(Request $request, ?MediaContent $parent, MediaContent $content): View
    {
        $context = $this->context($request);
        $this->ensureType($content, $context['type']);

        return view('backoffice.media-contents.edit', [
            'context' => $context,
            'parent' => $parent,
            'mediaContent' => $content,
            'categories' => isset($context['category_group_code'])
                ? $this->service->categoriesForGroup($context['category_group_code'])
                : collect(),
            'returnUrl' => $this->returnUrl($request, $this->indexUrl($context, $parent)),
        ]);
    }

    private function updateContent(MediaContentRequest $request, ?MediaContent $parent, MediaContent $content): RedirectResponse
    {
        $context = $this->context($request);
        $this->ensureType($content, $context['type']);
        $data = $request->safe()->except(['image']);
        if (in_array($context['type'], [MediaContent::TYPE_PHOTO_ITEM, MediaContent::TYPE_NEWS_ITEM], true)) {
            $data['page_title'] = $data['title'];
        }
        $this->service->update($content, $data, $request->file('image'), $request->boolean('remove_image'), $request->user()?->id);

        return redirect()->to($this->returnUrl($request, $this->indexUrl($context, $parent)))
            ->with('success', $context['entity_name'].' 정보가 수정되었습니다.');
    }

    private function destroyContent(Request $request, ?MediaContent $parent, MediaContent $content): JsonResponse|RedirectResponse
    {
        $context = $this->context($request);
        $this->ensureType($content, $context['type']);
        $this->service->delete($content);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $context['entity_name'].'이 삭제되었습니다.']);
        }

        return redirect()->to($this->indexUrl($context, $parent))->with('success', $context['entity_name'].'이 삭제되었습니다.');
    }

    private function destroyMany(Request $request, ?MediaContent $parent): JsonResponse
    {
        $context = $this->context($request);
        $validated = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'exists:media_contents,id']]);
        $count = $this->service->deleteMany($context['type'], $parent?->id, $validated['ids']);

        return response()->json(['success' => true, 'message' => $count.'개의 '.$context['entity_name'].' 항목이 삭제되었습니다.']);
    }

    /** @return array<string, mixed> */
    private function context(Request $request): array
    {
        return match ((string) $request->route('media_type')) {
            MediaContent::TYPE_PHOTO_FOLDER => ['type' => MediaContent::TYPE_PHOTO_FOLDER, 'menu_name' => 'Photo Gallery', 'entity_name' => 'Photo Gallery 폴더', 'route' => 'backoffice.media-photo', 'form' => 'folder', 'child_route' => 'backoffice.media-photo-items'],
            MediaContent::TYPE_PHOTO_ITEM => ['type' => MediaContent::TYPE_PHOTO_ITEM, 'menu_name' => 'Photo Gallery', 'entity_name' => 'Photo', 'route' => 'backoffice.media-photo', 'form' => 'photo', 'category_group_code' => Category::GROUP_CODE_PHOTO_GALLERY, 'category_group_name' => 'Photo Gallery'],
            MediaContent::TYPE_NEWS_ITEM => ['type' => MediaContent::TYPE_NEWS_ITEM, 'menu_name' => 'News Clippings', 'entity_name' => 'News Clipping', 'route' => 'backoffice.media-news', 'form' => 'news', 'category_group_code' => Category::GROUP_CODE_NEWS_CLIPPINGS, 'category_group_name' => 'News Clippings'],
            MediaContent::TYPE_YOUTUBE => ['type' => MediaContent::TYPE_YOUTUBE, 'menu_name' => 'YouTube Channel', 'entity_name' => 'YouTube Channel', 'route' => 'backoffice.media-youtube', 'form' => 'youtube'],
            default => abort(404),
        };
    }

    private function ensureParent(Request $request, MediaContent $parent): void
    {
        $expectedType = (string) $request->route('media_parent_type');
        abort_unless($parent->type === $expectedType, 404);
    }

    private function ensureType(MediaContent $content, string $type): void
    {
        abort_unless($content->type === $type, 404);
    }

    /** @return array<string, string|null> */
    private function filters(Request $request): array
    {
        return [
            'is_visible' => $request->query('is_visible', ''),
            'created_from' => $request->query('created_from'),
            'created_to' => $request->query('created_to'),
            'keyword' => trim((string) $request->query('keyword', '')),
            'category_id' => $request->query('category_id', ''),
        ];
    }

    /** @param array<string, mixed> $context */
    private function indexUrl(array $context, ?MediaContent $parent): string
    {
        return $parent
            ? route($context['route'].'.index', $parent)
            : route($context['route'].'.index');
    }

    private function returnUrl(Request $request, string $indexUrl): string
    {
        $returnUrl = $request->input('return_url', $request->query('return_url'));

        return is_string($returnUrl) && str_starts_with($returnUrl, $indexUrl) ? $returnUrl : $indexUrl;
    }
}
