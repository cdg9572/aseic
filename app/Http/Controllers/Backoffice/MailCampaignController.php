<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailCampaignRequest;
use App\Models\MailCampaign;
use App\Services\Backoffice\MailCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailCampaignController extends Controller
{
    public function __construct(private readonly MailCampaignService $service) {}

    public function index(Request $request): View
    {
        $filters = ['status' => $request->query('status', ''), 'created_from' => $request->query('created_from'), 'created_to' => $request->query('created_to'), 'keyword' => trim((string) $request->query('keyword', ''))];
        $perPage = in_array((int) $request->query('per_page', 10), [10, 20, 50], true) ? (int) $request->query('per_page', 10) : 10;
        $campaigns = $this->service->getCampaigns($filters, $perPage);

        return view('backoffice.mail-campaigns.index', compact('campaigns', 'filters', 'perPage'));
    }

    public function create(): View
    {
        return view('backoffice.mail-campaigns.create', ['addressBooks' => $this->service->addressBooks()]);
    }

    public function store(MailCampaignRequest $request): RedirectResponse
    {
        $this->service->create($request->safe()->except(['attachments']), (array) $request->file('attachments', []), $request->user()?->id);

        return redirect()->route('backoffice.mail-campaigns.index')->with('success', '메일이 임시 저장되었습니다.');
    }

    public function edit(Request $request, MailCampaign $mailCampaign): View
    {
        $mailCampaign->load('addressBooks');

        return view('backoffice.mail-campaigns.edit', [
            'mailCampaign' => $mailCampaign,
            'addressBooks' => $this->service->addressBooks(),
            'returnUrl' => $this->returnUrl($request),
        ]);
    }

    public function update(MailCampaignRequest $request, MailCampaign $mailCampaign): RedirectResponse
    {
        abort_unless(in_array($mailCampaign->status, [MailCampaign::STATUS_DRAFT, MailCampaign::STATUS_FAILED, MailCampaign::STATUS_PARTIAL], true), 422, '발송 중이거나 완료된 메일은 수정할 수 없습니다.');
        $this->service->update($mailCampaign, $request->safe()->except(['attachments']), (array) $request->file('attachments', []), (array) $request->input('remove_attachments', []), $request->user()?->id);

        return redirect()->to($this->returnUrl($request))->with('success', '메일 정보가 수정되었습니다.');
    }

    public function send(MailCampaign $mailCampaign): RedirectResponse
    {
        $count = $this->service->queue($mailCampaign);

        return redirect()->route('backoffice.mail-campaigns.index')->with('success', $count.'명에게 보낼 메일이 발송 대기열에 등록되었습니다.');
    }

    public function destroy(Request $request, MailCampaign $mailCampaign): JsonResponse|RedirectResponse
    {
        $this->service->delete($mailCampaign);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '메일이 삭제되었습니다.']);
        }

        return redirect()->route('backoffice.mail-campaigns.index')->with('success', '메일이 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'exists:mail_campaigns,id']]);
        $count = $this->service->deleteMany($validated['ids']);

        return response()->json(['success' => true, 'message' => $count.'개의 메일이 삭제되었습니다.']);
    }

    private function returnUrl(Request $request): string
    {
        $indexUrl = route('backoffice.mail-campaigns.index');
        $url = $request->input('return_url', $request->query('return_url'));

        return is_string($url) && str_starts_with($url, $indexUrl) ? $url : $indexUrl;
    }
}
