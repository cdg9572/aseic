<?php

namespace App\Services\Backoffice;

use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;
use ZipArchive;

class ContactSpreadsheetImporter
{
    /** @return array<int, array{name: string, email: string, is_subscribed: bool, created_at?: string}> */
    public function import(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = $extension === 'xlsx' ? $this->readXlsx($file->getRealPath()) : $this->readCsv($file->getRealPath());
        if ($rows === []) {
            throw ValidationException::withMessages(['import_file' => '불러올 연락처가 없습니다.']);
        }

        $header = array_map(fn ($value) => strtolower(trim((string) $value)), array_shift($rows));
        $nameIndex = $this->headerIndex($header, ['name', '이름']);
        $emailIndex = $this->headerIndex($header, ['email', '이메일']);
        $subscribeIndex = $this->headerIndex($header, ['is_subscribed', 'subscribed', '수신여부']);
        $registeredAtIndex = $this->headerIndex($header, ['등록일', 'registered_at', 'created_at']);

        if ($nameIndex === null || $emailIndex === null) {
            throw ValidationException::withMessages(['import_file' => '첫 행에 이름과 이메일 열이 필요합니다.']);
        }

        $contacts = [];
        foreach ($rows as $rowNumber => $row) {
            $name = trim((string) ($row[$nameIndex] ?? ''));
            $email = strtolower(trim((string) ($row[$emailIndex] ?? '')));
            if ($name === '' && $email === '') {
                continue;
            }
            if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages(['import_file' => ($rowNumber + 2).'행의 이름 또는 이메일 형식이 올바르지 않습니다.']);
            }
            $subscribeValue = strtolower(trim((string) ($subscribeIndex === null ? 'y' : ($row[$subscribeIndex] ?? 'y'))));
            $contact = ['name' => $name, 'email' => $email, 'is_subscribed' => ! in_array($subscribeValue, ['n', 'no', '0', '미수신'], true)];
            $registeredAt = trim((string) ($registeredAtIndex === null ? '' : ($row[$registeredAtIndex] ?? '')));
            if ($registeredAt !== '') {
                $contact['created_at'] = $this->parseRegisteredAt($registeredAt, $rowNumber + 2);
            }
            $contacts[$email] = $contact;
        }

        return array_values($contacts);
    }

    /** @return array<int, array<int, string>> */
    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            return [];
        }
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = array_map(fn ($value) => preg_replace('/^\xEF\xBB\xBF/', '', (string) $value), $row);
        }
        fclose($handle);

        return $rows;
    }

    /** @return array<int, array<int, string>> */
    private function readXlsx(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages(['import_file' => 'XLSX 파일을 열 수 없습니다.']);
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $xml = new SimpleXMLElement($sharedXml);
            $xml->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            foreach ($xml->xpath('//a:si') ?: [] as $item) {
                $item->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $sharedStrings[] = implode('', array_map('strval', $item->xpath('.//a:t') ?: []));
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) {
            return [];
        }

        $sheet = new SimpleXMLElement($sheetXml);
        $sheet->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];
        foreach ($sheet->xpath('//a:sheetData/a:row') ?: [] as $row) {
            $row->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $values = [];
            foreach ($row->xpath('./a:c') ?: [] as $cell) {
                $reference = (string) $cell['r'];
                preg_match('/^[A-Z]+/', $reference, $matches);
                $column = $this->columnIndex($matches[0] ?? 'A');
                $cell->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $value = (string) (($cell->xpath('./a:v')[0] ?? null));
                if ((string) $cell['t'] === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ((string) $cell['t'] === 'inlineStr') {
                    $value = implode('', array_map('strval', $cell->xpath('.//a:t') ?: []));
                }
                $values[$column] = $value;
            }
            if ($values !== []) {
                $max = max(array_keys($values));
                $rows[] = array_map(fn ($index) => (string) ($values[$index] ?? ''), range(0, $max));
            }
        }

        return $rows;
    }

    /** @param array<int, string> $headers */
    private function headerIndex(array $headers, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $index = array_search($candidate, $headers, true);
            if ($index !== false) {
                return $index;
            }
        }

        return null;
    }

    private function columnIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = $index * 26 + ord($letter) - 64;
        }

        return $index - 1;
    }

    private function parseRegisteredAt(string $value, int $rowNumber): string
    {
        if (is_numeric($value)) {
            return CarbonImmutable::create(1899, 12, 30)->addDays((int) floor((float) $value))->startOfDay()->toDateTimeString();
        }

        foreach (['Y-m-d', 'Y.m.d', 'Y/m/d'] as $format) {
            try {
                $date = CarbonImmutable::createFromFormat('!'.$format, $value);
                if ($date !== false && $date->format($format) === $value) {
                    return $date->toDateTimeString();
                }
            } catch (\Throwable) {
                // 다음 지원 형식으로 확인합니다.
            }
        }

        throw ValidationException::withMessages(['import_file' => $rowNumber.'행의 등록일은 YYYY-MM-DD 형식으로 입력해주세요.']);
    }
}
