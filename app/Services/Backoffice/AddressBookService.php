<?php

namespace App\Services\Backoffice;

use App\Models\AddressBook;
use App\Models\AddressBookContact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AddressBookService
{
    public function __construct(private readonly ContactSpreadsheetImporter $importer) {}

    /** @param array<string, mixed> $filters */
    public function getAddressBooks(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = AddressBook::query()->withCount('contacts')->with('creator:id,name');
        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(fn ($builder) => $builder->where('name', 'like', '%'.$keyword.'%')->orWhereHas('contacts', fn ($contact) => $contact->where('name', 'like', '%'.$keyword.'%')->orWhere('email', 'like', '%'.$keyword.'%')));
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, ?UploadedFile $importFile, ?int $adminId): AddressBook
    {
        return DB::transaction(function () use ($data, $importFile, $adminId): AddressBook {
            $contacts = $this->collectContacts((array) ($data['contacts'] ?? []), $importFile);
            $book = AddressBook::query()->create(['name' => $data['name'], 'created_by' => $adminId, 'updated_by' => $adminId]);
            $book->contacts()->createMany($contacts);

            return $book->fresh(['contacts', 'creator']);
        });
    }

    /** @param array<string, mixed> $data */
    public function update(AddressBook $book, array $data, ?UploadedFile $importFile, ?int $adminId): AddressBook
    {
        DB::transaction(function () use ($book, $data, $importFile, $adminId): void {
            $book->update(['name' => $data['name'], 'updated_by' => $adminId]);
            if ($importFile) {
                foreach ($this->collectContacts([], $importFile) as $contact) {
                    $createdAt = $contact['created_at'] ?? null;
                    unset($contact['created_at']);
                    $storedContact = $book->contacts()->updateOrCreate(
                        ['email' => $contact['email']],
                        $contact,
                    );
                    if ($createdAt !== null && $storedContact->wasRecentlyCreated) {
                        $storedContact->forceFill(['created_at' => $createdAt])->save();
                    }
                }
            }
        });

        return $book->fresh(['contacts', 'creator']);
    }

    public function delete(AddressBook $book): void
    {
        $book->delete();
    }

    /** @param array<int, int|string> $ids */
    public function deleteMany(array $ids): int
    {
        return AddressBook::query()->whereIn('id', $ids)->delete();
    }

    /** @param array<string, mixed> $data */
    public function createContact(AddressBook $book, array $data): AddressBookContact
    {
        return $book->contacts()->create([
            'name' => trim((string) $data['contact_name']),
            'email' => strtolower(trim((string) $data['contact_email'])),
            'is_subscribed' => true,
        ]);
    }

    /** @param array<string, mixed> $data */
    public function updateContact(AddressBookContact $contact, array $data): AddressBookContact
    {
        $contact->update([
            'name' => trim((string) $data['contact_name']),
            'email' => strtolower(trim((string) $data['contact_email'])),
        ]);

        return $contact->refresh();
    }

    public function deleteContact(AddressBookContact $contact): void
    {
        $contact->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $contacts
     * @return array<int, array{name: string, email: string, is_subscribed: bool, created_at?: string}>
     */
    private function collectContacts(array $contacts, ?UploadedFile $importFile): array
    {
        $merged = [];
        foreach ($contacts as $contact) {
            $name = trim((string) ($contact['name'] ?? ''));
            $email = strtolower(trim((string) ($contact['email'] ?? '')));
            if ($name === '' && $email === '') {
                continue;
            }
            $merged[$email] = ['name' => $name, 'email' => $email, 'is_subscribed' => (bool) ($contact['is_subscribed'] ?? false)];
        }
        if ($importFile) {
            foreach ($this->importer->import($importFile) as $contact) {
                $merged[$contact['email']] = $contact;
            }
        }

        return array_values($merged);
    }
}
