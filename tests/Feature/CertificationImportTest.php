<?php

declare(strict_types=1);

use App\Jobs\ImportCertificationsJob;
use App\Models\User;
use App\Services\Certification\CertificationImportService;
use Illuminate\Support\Facades\DB;

function makeImportCsv(array $rows, array $headers = ['trainee_name', 'document_type', 'country_name', 'trainer_name', 'accreditation_date']): string
{
    $path = sys_get_temp_dir().'/import_'.uniqid().'.csv';

    $handle = fopen($path, 'w');
    fputcsv($handle, $headers);

    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }

    fclose($handle);

    return $path;
}

function makeArabicImportCsv(
    array $rows,
    bool $withBom = true,
    string $nationalityHeader = 'الحنسية',
): string {
    $headers = [
        'اسم المتدرب ',
        'الرقم المتسلسل المعتمد ',
        'الرمز ',
        'رقم الاعتماد ',
        'نوع الوثيقة ',
        'تاريخ الاعتماد',
        'اسم المدرب ',
        $nationalityHeader,
        'الحصول على الوثيقة ورقيا ',
        'ملاحظات  ',
    ];

    $path = sys_get_temp_dir().'/import_ar_'.uniqid().'.csv';

    $handle = fopen('php://temp', 'r+');
    fputcsv($handle, $headers);

    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }

    rewind($handle);
    $content = stream_get_contents($handle);
    fclose($handle);

    file_put_contents($path, ($withBom ? "\xEF\xBB\xBF" : '').$content);

    return $path;
}

function storedNotificationTitles(int $userId): array
{
    return DB::table('notifications')
        ->where('notifiable_id', $userId)
        ->where('notifiable_type', User::class)
        ->get()
        ->map(function (object $row): string {
            return data_get(json_decode($row->data, true), 'title', '');
        })
        ->all();
}

it('imports certifications, creating related records with the given creator id', function () {
    $path = makeImportCsv([
        ['John Doe', 'Training Certificate', 'Egypt', 'Jane Trainer', '2025-01-15'],
        ['John Doe', 'Training Certificate', 'Egypt', 'Jane Trainer', '2025-01-15'],
        ['Sarah Smith', 'Accreditation Certificate', 'Jordan', '', '2025-02-01'],
    ]);

    $stats = app(CertificationImportService::class)->importCertifications($path, 42);

    expect($stats)->toMatchArray(['total' => 3, 'success' => 3, 'failed' => 0]);

    expect(DB::table('certifications')->count())->toBe(3)
        ->and(DB::table('certifications')->where('creator_id', 42)->count())->toBe(3)
        ->and(DB::table('trainees')->count())->toBe(2)
        ->and(DB::table('trainers')->count())->toBe(1)
        ->and(DB::table('countries')->count())->toBe(2)
        ->and(DB::table('board_document_types')->count())->toBe(2);

    expect(DB::table('certifications')->first()->accredited_serial_number)->not->toBeNull()
        ->and(DB::table('certifications')->first()->document_code)->not->toBeNull()
        ->and(DB::table('certifications')->first()->accreditation_number)->toBeNull();

    @unlink($path);
});

it('rejects rows missing a trainee name without creating placeholder records', function () {
    $path = makeImportCsv([
        ['', 'Training Certificate', '', '', ''],
        ['', 'Training Certificate', '', '', ''],
    ]);

    $stats = app(CertificationImportService::class)->importCertifications($path, 1);

    expect($stats)->toMatchArray(['total' => 2, 'success' => 0, 'failed' => 2])
        ->and(DB::table('trainees')->count())->toBe(0)
        ->and(DB::table('certifications')->count())->toBe(0);

    @unlink($path);
});

it('imports Arabic headers with a UTF-8 BOM and trailing spaces', function () {
    $path = makeArabicImportCsv([
        ['Ahmed Ali', 'IB100', '5', 'IB1005', 'Training Certificate', '2/14/2022', 'Maen Al Shammari', 'Syria', 'YAS', 'ملاحظة أولى'],
        ['Sara Omar', 'IB101', '7', 'IB1017', 'Training Certificate', '13/1/2025', 'Maen Al Shammari', 'Jordan', 'YAS', ''],
    ]);

    $stats = app(CertificationImportService::class)->importCertifications($path, 7);

    expect($stats)->toMatchArray(['total' => 2, 'success' => 2, 'failed' => 0]);

    expect(DB::table('certifications')->count())->toBe(2)
        ->and(DB::table('trainees')->count())->toBe(2)
        ->and(DB::table('board_document_types')->count())->toBe(1)
        ->and(DB::table('countries')->count())->toBe(2);

    $first = DB::table('certifications')->where('accredited_serial_number', 'IB100')->first();

    expect($first)->not->toBeNull()
        ->and($first->document_code)->toBe('5')
        ->and($first->accreditation_number)->toBe('IB1005')
        ->and($first->accreditation_date)->toBe('2022-02-14')
        ->and($first->notes)->toBe('ملاحظة أولى');

    $second = DB::table('certifications')->where('accredited_serial_number', 'IB101')->first();

    expect($second->accreditation_date)->toBe('2025-01-13');

    @unlink($path);
});

it('supports the الجنسية spelling as a country header alias', function () {
    $path = makeArabicImportCsv([
        ['Noor Ali', 'IB200', '9', 'IB2009', 'ICDL', '3/12/2022', 'Ahmad Alkoud', 'Syria', 'YAS', ''],
    ], nationalityHeader: 'الجنسية');

    $stats = app(CertificationImportService::class)->importCertifications($path, 1);

    expect($stats)->toMatchArray(['total' => 1, 'success' => 1, 'failed' => 0])
        ->and(DB::table('countries')->where('name->en', 'Syria')->count())->toBe(1);

    @unlink($path);
});

it('handles an embedded newline inside a quoted name field', function () {
    $path = makeImportCsv([
        ["MOHAMMAD IBRAHIM\nTABASHAH", 'Training Certificate', 'Syria', 'Ahmad Alkoud', '4/21/2022'],
    ], ['trainee_name', 'document_type', 'country_name', 'trainer_name', 'accreditation_date']);

    $stats = app(CertificationImportService::class)->importCertifications($path, 1);

    expect($stats)->toMatchArray(['total' => 1, 'success' => 1, 'failed' => 0])
        ->and(DB::table('trainees')->count())->toBe(1)
        ->and(DB::table('certifications')->count())->toBe(1);

    @unlink($path);
});

it('rejects rows with an invalid accreditation date', function () {
    $path = makeArabicImportCsv([
        ['Ahmed Ali', 'IB300', '5', 'IB3005', 'Training Certificate', '109/2022', 'Maen Al Shammari', 'Syria', 'YAS', ''],
    ]);

    $stats = app(CertificationImportService::class)->importCertifications($path, 1);

    expect($stats)->toMatchArray(['total' => 1, 'success' => 0, 'failed' => 1])
        ->and(DB::table('certifications')->count())->toBe(0);

    @unlink($path);
});

it('rejects rows with more columns than the header row', function () {
    $path = makeImportCsv([
        ['John Doe', 'Training Certificate', 'Egypt', 'Jane Trainer', '2025-01-15', 'EXTRA'],
    ]);

    $stats = app(CertificationImportService::class)->importCertifications($path, 1);

    expect($stats)->toMatchArray(['total' => 1, 'success' => 0, 'failed' => 1])
        ->and(DB::table('certifications')->count())->toBe(0);

    @unlink($path);
});

it('updates existing certifications on re-import with the same accreditation number', function () {
    $path = makeArabicImportCsv([
        ['Ahmed Ali', 'IB100', '5', 'IB1005', 'Training Certificate', '2/14/2022', 'Maen Al Shammari', 'Syria', 'YAS', 'ملاحظة أولى'],
    ]);

    $firstStats = app(CertificationImportService::class)->importCertifications($path, 7);
    $secondStats = app(CertificationImportService::class)->importCertifications($path, 7);

    expect($firstStats)->toMatchArray(['total' => 1, 'success' => 1, 'failed' => 0])
        ->and($secondStats)->toMatchArray(['total' => 1, 'success' => 1, 'failed' => 0])
        ->and(DB::table('certifications')->count())->toBe(1);

    @unlink($path);
});

it('sends a success database notification and deletes the file after a successful import', function () {
    $user = User::factory()->create();
    $path = makeImportCsv([
        ['John Doe', 'Training Certificate', '', '', ''],
    ]);

    (new ImportCertificationsJob($path, $user->id))->handle(app(CertificationImportService::class));

    expect(storedNotificationTitles($user->id))->toBe([
        __('app.import.notifications.chunk_success_title'),
        __('app.import.notifications.success_title'),
    ])
        ->and(file_exists($path))->toBeFalse();
});

it('dispatches the import batch through the queue without serialization recursion', function () {
    $user = User::factory()->create();
    $path = makeImportCsv([
        ['John Doe', 'Training Certificate', '', '', ''],
    ]);

    ImportCertificationsJob::dispatch($path, $user->id);

    expect(DB::table('job_batches')->count())->toBe(1)
        ->and(file_exists($path))->toBeFalse()
        ->and(storedNotificationTitles($user->id))->toBe([
            __('app.import.notifications.chunk_success_title'),
            __('app.import.notifications.success_title'),
        ]);
});

it('fails fast on a directory path, sending a danger notification and keeping the file', function () {
    $user = User::factory()->create();
    $path = sys_get_temp_dir().'/import_fail_'.uniqid();
    mkdir($path);

    (new ImportCertificationsJob($path, $user->id))->handle(app(CertificationImportService::class));

    expect(storedNotificationTitles($user->id))->toBe([__('app.import.notifications.failed_title')])
        ->and(file_exists($path))->toBeTrue();
});
