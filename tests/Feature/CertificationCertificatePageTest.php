<?php

declare(strict_types=1);

use App\Models\Certification;
use App\Models\DocumentType;
use App\Models\Trainer;
use App\Services\Certification\CertificationService;
use App\Support\LocaleConfig;
use Illuminate\Support\Facades\View;

beforeEach(function () {
    app()->setLocale('en');

    View::share('navigationPages', []);
    View::share('appSettings', collect());
    View::share('socialLinks', []);
    View::share('currentLocale', 'en');
    View::share('availableLocales', LocaleConfig::availableLocales());
});

it('renders the certificate verification page as a digital certificate', function () {
    $documentType = DocumentType::factory()->create([
        'name' => ['en' => 'Advanced Pilates Training', 'ar' => 'تدريب البيلاتس المتقدم'],
    ]);

    $certification = Certification::factory()->create([
        'accreditation_number' => 'IBVTQ2026072490434',
        'documentable_type' => DocumentType::class,
        'documentable_id' => $documentType->id,
        'assigned_trainer_id' => Trainer::factory(),
        'notes' => 'Completed all advanced modules.',
    ]);

    $response = $this->get(route('web.certifications.show', $certification->accreditation_number));

    $response->assertOk();

    $response->assertSee('certificate-card')
        ->assertSee($certification->trainee->name)
        ->assertSee('Advanced Pilates Training')
        ->assertSee($certification->accreditation_number)
        ->assertSee(__('web.certificate.verified'))
        ->assertSee(__('web.certificate.certifies_that'))
        ->assertSee('assets/website/css/certificate.css')
        ->assertSee('js-cert-print')
        ->assertSee('certificate-qr');

    $response->assertSee('qr-svg', false);
});

it('omits the QR code when the accreditation number is missing', function () {
    $certification = new Certification(['accreditation_number' => null]);

    $qrSvg = app(CertificationService::class)->getVerificationQrSvg($certification);

    expect($qrSvg)->toBeNull();
});
