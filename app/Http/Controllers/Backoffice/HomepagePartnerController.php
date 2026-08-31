<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomepagePartnerRequest;
use App\Models\HomepagePartner;
use App\Services\Backoffice\HomepagePartnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepagePartnerController extends Controller
{
    private const CONTEXTS = [
        'organized' => [
            'type' => HomepagePartner::TYPE_ORGANIZED,
            'menu_name' => 'Organized 관리',
            'entity_name' => 'Organized',
            'route' => 'backoffice.organized',
        ],
        'partnerships' => [
            'type' => HomepagePartner::TYPE_PARTNERSHIP,
            'menu_name' => 'Partnership 관리',
            'entity_name' => 'Partnership',
            'route' => 'backoffice.partnerships',
        ],
    ];

    public function __construct(private readonly HomepagePartnerService $partnerService) {}

    public function index(Request $request): View
    {
        $context = $this->context($request);
        $filters = [
            'is_active' => $request->query('is_active', ''),
            'created_from' => $request->query('created_from'),
            'created_to' => $request->query('created_to'),
            'keyword' => trim((string) $request->query('keyword', '')),
        ];
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;
        $partners = $this->partnerService->getPartners($context['type'], $filters, $perPage);

        return view('backoffice.homepage-partners.index', compact('partners', 'filters', 'perPage', 'context'));
    }

    public function create(Request $request): View
    {
        return view('backoffice.homepage-partners.create', ['context' => $this->context($request)]);
    }

    public function store(HomepagePartnerRequest $request): RedirectResponse
    {
        $context = $this->context($request);
        $this->partnerService->createPartner(
            $context['type'],
            $this->partnerData($request),
            $request->file('profile_image'),
            $request->user()?->id,
        );

        return redirect()->route($context['route'].'.index')
            ->with('success', $context['entity_name'].'가 등록되었습니다.');
    }

    public function edit(Request $request, HomepagePartner $homepagePartner): View
    {
        $context = $this->context($request);
        $this->ensureType($homepagePartner, $context['type']);

        return view('backoffice.homepage-partners.edit', [
            'partner' => $homepagePartner,
            'context' => $context,
        ]);
    }

    public function update(
        HomepagePartnerRequest $request,
        HomepagePartner $homepagePartner,
    ): RedirectResponse {
        $context = $this->context($request);
        $this->ensureType($homepagePartner, $context['type']);
        $this->partnerService->updatePartner(
            $homepagePartner,
            $this->partnerData($request),
            $request->file('profile_image'),
            $request->boolean('remove_profile_image'),
            $request->user()?->id,
        );

        return $this->redirectAfterMutation($request, $context, $context['entity_name'].' 정보가 수정되었습니다.');
    }

    public function destroy(
        Request $request,
        HomepagePartner $homepagePartner,
    ): RedirectResponse|JsonResponse {
        $context = $this->context($request);
        $this->ensureType($homepagePartner, $context['type']);
        $this->partnerService->deletePartner($homepagePartner);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $context['entity_name'].'가 삭제되었습니다.']);
        }

        return $this->redirectAfterMutation($request, $context, $context['entity_name'].'가 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $context = $this->context($request);
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:homepage_partners,id'],
        ]);
        $deleted = $this->partnerService->deletePartners($context['type'], $validated['ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'개의 '.$context['entity_name'].' 항목이 삭제되었습니다.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function partnerData(HomepagePartnerRequest $request): array
    {
        return $request->safe()->except([
            'profile_image',
            'remove_profile_image',
            'return_url',
        ]);
    }

    /**
     * @return array{type: string, menu_name: string, entity_name: string, route: string}
     */
    private function context(Request $request): array
    {
        $routeName = (string) $request->route()?->getName();

        foreach (self::CONTEXTS as $prefix => $context) {
            if (str_starts_with($routeName, 'backoffice.'.$prefix.'.')) {
                return $context;
            }
        }

        abort(404);
    }

    private function ensureType(HomepagePartner $partner, string $type): void
    {
        abort_unless($partner->type === $type, 404);
    }

    /**
     * @param  array{route: string}  $context
     */
    private function redirectAfterMutation(
        Request $request,
        array $context,
        string $message,
    ): RedirectResponse {
        $returnUrl = $request->input('return_url');
        $indexUrl = route($context['route'].'.index');

        if (is_string($returnUrl) && str_starts_with($returnUrl, $indexUrl)) {
            return redirect()->to($returnUrl)->with('success', $message);
        }

        return redirect()->route($context['route'].'.index')->with('success', $message);
    }
}
