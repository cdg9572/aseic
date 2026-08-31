<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpeakerRequest;
use App\Models\Speaker;
use App\Services\Backoffice\SpeakerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpeakerController extends Controller
{
    public function __construct(private readonly SpeakerService $speakerService) {}

    public function index(Request $request): View
    {
        $filters = [
            'is_active' => $request->query('is_active', ''),
            'created_from' => $request->query('created_from'),
            'created_to' => $request->query('created_to'),
            'keyword' => trim((string) $request->query('keyword', '')),
        ];
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;
        $speakers = $this->speakerService->getSpeakers($filters, $perPage);

        return view('backoffice.speakers.index', compact('speakers', 'filters', 'perPage'));
    }

    public function create(): View
    {
        return view('backoffice.speakers.create', ['roleOptions' => Speaker::roleOptions()]);
    }

    public function store(SpeakerRequest $request): RedirectResponse
    {
        $data = $this->speakerData($request);
        $this->speakerService->createSpeaker(
            $data,
            $request->file('profile_image'),
            (array) $request->file('attachments', []),
            $request->user()?->id,
        );

        return redirect()->route('backoffice.speakers.index')
            ->with('success', 'Speaker가 등록되었습니다.');
    }

    public function edit(Speaker $speaker): View
    {
        return view('backoffice.speakers.edit', [
            'speaker' => $speaker,
            'roleOptions' => Speaker::roleOptions(),
        ]);
    }

    public function update(SpeakerRequest $request, Speaker $speaker): RedirectResponse
    {
        $data = $this->speakerData($request);
        $this->speakerService->updateSpeaker(
            $speaker,
            $data,
            $request->file('profile_image'),
            (array) $request->file('attachments', []),
            $request->boolean('remove_profile_image'),
            (array) $request->input('remove_attachments', []),
            $request->user()?->id,
        );

        return $this->redirectAfterMutation($request, 'Speaker 정보가 수정되었습니다.');
    }

    public function destroy(Request $request, Speaker $speaker): RedirectResponse|JsonResponse
    {
        $this->speakerService->deleteSpeaker($speaker);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Speaker가 삭제되었습니다.']);
        }

        return $this->redirectAfterMutation($request, 'Speaker가 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:speakers,id'],
        ]);

        $deleted = $this->speakerService->deleteSpeakers($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'명의 Speaker가 삭제되었습니다.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function speakerData(SpeakerRequest $request): array
    {
        return $request->safe()->except([
            'profile_image',
            'attachments',
            'remove_profile_image',
            'remove_attachments',
            'return_url',
        ]);
    }

    private function redirectAfterMutation(Request $request, string $message): RedirectResponse
    {
        $returnUrl = $request->input('return_url');
        $speakersUrl = route('backoffice.speakers.index');

        if (is_string($returnUrl) && str_starts_with($returnUrl, $speakersUrl)) {
            return redirect()->to($returnUrl)->with('success', $message);
        }

        return redirect()->route('backoffice.speakers.index')->with('success', $message);
    }
}
