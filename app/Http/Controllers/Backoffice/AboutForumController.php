<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutForumRequest;
use App\Models\AboutPage;
use App\Services\Backoffice\AboutForumService;
use App\Services\Backoffice\AboutPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutForumController extends Controller
{
    public function __construct(
        private readonly AboutForumService $aboutForumService,
        private readonly AboutPageService $aboutPageService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'is_linked' => $request->query('is_linked', ''),
            'created_from' => $request->query('created_from'),
            'created_to' => $request->query('created_to'),
            'keyword' => trim((string) $request->query('keyword', '')),
        ];
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;
        $pages = $this->aboutForumService->getPages($filters, $perPage);

        return view('backoffice.about-pages.index', [
            ...compact('pages', 'filters', 'perPage'),
            'context' => $this->context(),
        ]);
    }

    public function create(): View
    {
        return view('backoffice.about-forum.create', [
            'mainPages' => $this->aboutPageService->mainPageOptions(),
            'selectedMainPageId' => null,
        ]);
    }

    public function store(AboutForumRequest $request): RedirectResponse
    {
        $this->aboutForumService->createPage(
            $this->pageData($request),
            $request->user()?->id,
        );

        return redirect()->route('backoffice.about-the-forum.index')
            ->with('success', 'About the Forum이 등록되었습니다.');
    }

    public function edit(Request $request, AboutPage $aboutPage): View
    {
        $this->ensureForumPage($aboutPage);

        return view('backoffice.about-forum.edit', [
            'aboutPage' => $aboutPage->load('forumDetail'),
            'mainPages' => $this->aboutPageService->mainPageOptions(),
            'selectedMainPageId' => $this->aboutPageService->selectedMainPageId($aboutPage),
            'returnUrl' => $this->returnUrl($request),
        ]);
    }

    public function update(AboutForumRequest $request, AboutPage $aboutPage): RedirectResponse
    {
        $this->ensureForumPage($aboutPage);
        $this->aboutForumService->updatePage(
            $aboutPage,
            $this->pageData($request),
            $request->user()?->id,
        );

        return $this->redirectAfterMutation($request, 'About the Forum 정보가 수정되었습니다.');
    }

    public function destroy(Request $request, AboutPage $aboutPage): RedirectResponse|JsonResponse
    {
        $this->ensureForumPage($aboutPage);
        $this->aboutForumService->deletePage($aboutPage);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'About the Forum이 삭제되었습니다.']);
        }

        return $this->redirectAfterMutation($request, 'About the Forum이 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:about_pages,id'],
        ]);
        $deleted = $this->aboutForumService->deletePages($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'개의 About the Forum 항목이 삭제되었습니다.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function pageData(AboutForumRequest $request): array
    {
        return $request->safe()->except(['return_url']);
    }

    /** @return array<string, string> */
    private function context(): array
    {
        return [
            'menu_name' => 'About the Forum',
            'entity_name' => 'About the Forum',
            'route' => 'backoffice.about-the-forum',
        ];
    }

    private function ensureForumPage(AboutPage $page): void
    {
        abort_unless($page->type === AboutPage::TYPE_FORUM, 404);
    }

    private function redirectAfterMutation(Request $request, string $message): RedirectResponse
    {
        return redirect()->to($this->returnUrl($request))->with('success', $message);
    }

    private function returnUrl(Request $request): string
    {
        $returnUrl = $request->input('return_url', $request->query('return_url'));
        $indexUrl = route('backoffice.about-the-forum.index');

        return is_string($returnUrl) && str_starts_with($returnUrl, $indexUrl)
            ? $returnUrl
            : $indexUrl;
    }
}
