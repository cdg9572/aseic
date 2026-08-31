<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressBookContactRequest;
use App\Http\Requests\AddressBookRequest;
use App\Models\AddressBook;
use App\Models\AddressBookContact;
use App\Services\Backoffice\AddressBookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AddressBookController extends Controller
{
    public function __construct(private readonly AddressBookService $service) {}

    public function index(Request $request): View
    {
        $filters = ['created_from' => $request->query('created_from'), 'created_to' => $request->query('created_to'), 'keyword' => trim((string) $request->query('keyword', ''))];
        $perPage = in_array((int) $request->query('per_page', 10), [10, 20, 50], true) ? (int) $request->query('per_page', 10) : 10;
        $addressBooks = $this->service->getAddressBooks($filters, $perPage);

        return view('backoffice.address-books.index', compact('addressBooks', 'filters', 'perPage'));
    }

    public function create(): View
    {
        return view('backoffice.address-books.create');
    }

    public function store(AddressBookRequest $request): RedirectResponse
    {
        $addressBook = $this->service->create($request->safe()->except(['import_file']), $request->file('import_file'), $request->user()?->id);

        if ($request->boolean('continue_contacts')) {
            return redirect()
                ->route('backoffice.address-books.edit', $addressBook)
                ->with('success', '주소록과 연락처가 등록되었습니다.');
        }

        return redirect()->route('backoffice.address-books.index')->with('success', '주소록이 등록되었습니다.');
    }

    public function edit(Request $request, AddressBook $addressBook): View
    {
        $addressBook->load('contacts');

        return view('backoffice.address-books.edit', ['addressBook' => $addressBook, 'returnUrl' => $this->returnUrl($request)]);
    }

    public function update(AddressBookRequest $request, AddressBook $addressBook): RedirectResponse
    {
        $this->service->update($addressBook, $request->safe()->except(['import_file']), $request->file('import_file'), $request->user()?->id);

        return redirect()->to($this->returnUrl($request))->with('success', '주소록이 수정되었습니다.');
    }

    public function destroy(Request $request, AddressBook $addressBook): JsonResponse|RedirectResponse
    {
        $this->service->delete($addressBook);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '주소록이 삭제되었습니다.']);
        }

        return redirect()->route('backoffice.address-books.index')->with('success', '주소록이 삭제되었습니다.');
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'exists:address_books,id']]);
        $count = $this->service->deleteMany($validated['ids']);

        return response()->json(['success' => true, 'message' => $count.'개의 주소록이 삭제되었습니다.']);
    }

    public function storeContact(AddressBookContactRequest $request, AddressBook $addressBook): RedirectResponse
    {
        $this->service->createContact($addressBook, $request->validated());

        return $this->contactRedirect($request, $addressBook)->with('success', '연락처가 등록되었습니다.');
    }

    public function updateContact(AddressBookContactRequest $request, AddressBook $addressBook, AddressBookContact $contact): RedirectResponse
    {
        $this->ensureContactBelongsToAddressBook($addressBook, $contact);
        $this->service->updateContact($contact, $request->validated());

        return $this->contactRedirect($request, $addressBook)->with('success', '연락처가 수정되었습니다.');
    }

    public function destroyContact(Request $request, AddressBook $addressBook, AddressBookContact $contact): RedirectResponse
    {
        $this->ensureContactBelongsToAddressBook($addressBook, $contact);
        $this->service->deleteContact($contact);

        return $this->contactRedirect($request, $addressBook)->with('success', '연락처가 삭제되었습니다.');
    }

    public function sample(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['이름', '이메일', '등록일']);
            fputcsv($output, ['홍길동', 'hong@example.com', now()->format('Y-m-d')]);
            fclose($output);
        }, 'address-book-sample.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function returnUrl(Request $request): string
    {
        $indexUrl = route('backoffice.address-books.index');
        $url = $request->input('return_url', $request->query('return_url'));

        return is_string($url) && str_starts_with($url, $indexUrl) ? $url : $indexUrl;
    }

    private function contactRedirect(Request $request, AddressBook $addressBook): RedirectResponse
    {
        $parameters = ['addressBook' => $addressBook];
        $returnUrl = $request->input('return_url');
        if (is_string($returnUrl) && str_starts_with($returnUrl, route('backoffice.address-books.index'))) {
            $parameters['return_url'] = $returnUrl;
        }

        return redirect()->route('backoffice.address-books.edit', $parameters);
    }

    private function ensureContactBelongsToAddressBook(AddressBook $addressBook, AddressBookContact $contact): void
    {
        abort_unless($contact->address_book_id === $addressBook->id, 404);
    }
}
