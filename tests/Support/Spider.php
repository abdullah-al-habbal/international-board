<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Enums\UserType;
use App\Models\ApplicationSetting;
use App\Models\BlogPost;
use App\Models\CenterAccreditationRequest;
use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterDocumentType;
use App\Models\CertifiedCenterFinancialRequest;
use App\Models\CertifiedCenterPaymentAgentPerson;
use App\Models\ContactMessage;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Membership;
use App\Models\StaticPage;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\TrainerAccreditationRequest;
use App\Models\TrainerDocumentType;
use App\Models\TrainerFinancialRequest;
use App\Models\User;
use App\Services\StaticPage\StaticPageService;
use App\Support\LocaleConfig;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

/**
 * Crawl support: seeds the three guard principals + public domain records,
 * and enumerates crawlable GET routes partitioned by panel/guard.
 *
 * The harness only ever performs read-only GET requests. The single hard
 * failure condition is an HTTP status >= 500 (a real crash / lazy-load /
 * boot-strictness violation). 4xx responses are recorded as warnings, since
 * tenant-scoped record routes legitimately return 403/404 for foreign ids.
 */
final class Spider
{
    /**
     * Route-name segment (after `resources.`) => model class, used to seed a
     * record so `{record}` routes execute real edit/view logic.
     */
    private const RESOURCE_MODELS = [
        'application-settings' => ApplicationSetting::class,
        'blog-posts' => BlogPost::class,
        'center-accreditation-requests' => CenterAccreditationRequest::class,
        'certifications' => Certification::class,
        'certified-center-document-types' => CertifiedCenterDocumentType::class,
        'certified-center-financial-requests' => CertifiedCenterFinancialRequest::class,
        'center-financial-requests' => CertifiedCenterFinancialRequest::class,
        'certified-centers' => CertifiedCenter::class,
        'contact-messages' => ContactMessage::class,
        'countries' => Country::class,
        'document-types' => DocumentType::class,
        'memberships' => Membership::class,
        'payment-agent-persons.payment-agent-people' => CertifiedCenterPaymentAgentPerson::class,
        'static-pages' => StaticPage::class,
        'trainees' => Trainee::class,
        'trainer-accreditation-requests' => TrainerAccreditationRequest::class,
        'trainer-document-types' => TrainerDocumentType::class,
        'trainer-financial-requests' => TrainerFinancialRequest::class,
        'trainers' => Trainer::class,
        'users' => User::class,
    ];

    /** @var array<class-string, int|string|null> cache of one seeded record key per model */
    private array $recordKeys = [];

    public User $admin;

    public CertifiedCenter $center;

    public Trainer $trainer;

    /** @var array<string, mixed> resolver values for public route params */
    public array $publicRecords = [];

    public array $ownedRecordKeys = [];

    public function seed(): void
    {
        Country::factory()->create();

        $this->admin = User::factory()->create(['type' => UserType::Admin->value]);

        $this->center = CertifiedCenter::factory()->create([
            'status' => 'active',
        ]);
        $centerRequest = CenterAccreditationRequest::factory()->create([
            'certified_center_id' => $this->center->getKey(),
            'status' => 'approved',
            'accreditation_end_date' => now()->addYear(),
        ]);

        $this->trainer = Trainer::factory()->create([
            'center_id' => null,
        ]);
        $trainerRequest = TrainerAccreditationRequest::factory()->create([
            'trainer_id' => $this->trainer->getKey(),
            'status' => 'approved',
            'accreditation_end_date' => now()->addYear(),
        ]);

        $this->publicRecords = [
            'certification' => Certification::factory()->create(),
            'center' => $this->center,
            'trainer' => $this->trainer,
            'blog' => BlogPost::factory()->create(),
            'page' => StaticPage::factory()->create(),
            'membership' => Membership::factory()->create(),
        ];

        $this->seedOwnedRecords($centerRequest->getKey(), $trainerRequest->getKey());
    }

    /**
     * Seed records belonging to the center/trainer principals so their
     * tenant-scoped `{record}` routes resolve in-scope. Each is best-effort:
     * a factory that can't satisfy its dependencies leaves a null and the
     * route falls back to the generic (foreign) record.
     */
    private function seedOwnedRecords(int|string $centerRequestKey, int|string $trainerRequestKey): void
    {
        $centerId = $this->center->getKey();
        $trainerId = $this->trainer->getKey();

        $owned = [
            'center.center-accreditation-requests' => fn () => $centerRequestKey,
            'center.center-financial-requests' => fn () => CertifiedCenterFinancialRequest::factory()
                ->create(['certified_center_id' => $centerId])->getKey(),
            'center.trainers' => fn () => Trainer::factory()
                ->create(['center_id' => $centerId])->getKey(),
            'center.certifications' => fn () => Certification::factory()
                ->create(['creator_type' => CertifiedCenter::class, 'creator_id' => $centerId])->getKey(),
            'trainer.trainer-accreditation-requests' => fn () => $trainerRequestKey,
            'trainer.trainer-document-types' => fn () => TrainerDocumentType::factory()
                ->create(['trainer_id' => $trainerId])->getKey(),
            'trainer.trainer-financial-requests' => fn () => TrainerFinancialRequest::factory()
                ->create(['trainer_id' => $trainerId])->getKey(),
            'trainer.certifications' => fn () => Certification::factory()
                ->create(['creator_type' => Trainer::class, 'creator_id' => $trainerId])->getKey(),
        ];

        foreach ($owned as $key => $factory) {
            try {
                $this->ownedRecordKeys[$key] = $factory();
            } catch (\Throwable) {
                $this->ownedRecordKeys[$key] = null;
            }
        }
    }

    public function sharePublicViewGlobals(string $locale): void
    {
        app()->setLocale($locale);

        View::share('navigationPages', []);
        View::share('appSettings', collect());
        View::share('socialLinks', []);
        View::share('currentLocale', $locale);
        View::share('availableLocales', LocaleConfig::availableLocales());

        if (Schema::hasTable('static_pages')) {
            View::share('navigationPages', app(StaticPageService::class)->getActivePages());
        }

        if (Schema::hasTable('application_settings')) {
            $settings = ApplicationSetting::all()->pluck('value', 'key');
            View::share('appSettings', $settings);

            $rawSocial = $settings->get('social_links', '[]');
            if (is_string($rawSocial)) {
                $rawSocial = json_decode($rawSocial, true) ?? [];
            }
            View::share('socialLinks', is_array($rawSocial) ? $rawSocial : []);
        }
    }

    /**
     * Crawlable GET routes whose name starts with the given prefix, excluding
     * auth/login, file downloads, livewire internals and other non-page routes.
     *
     * @return array<int, RoutingRoute>
     */
    public function routes(string $prefix): array
    {
        $excluded = ['.auth.', '.logout', '.exports.', '.imports.', '.download', 'livewire', 'storage.'];

        $out = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName() ?? '';
            if ($name === '' || ! str_starts_with($name, $prefix)) {
                continue;
            }
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }
            foreach ($excluded as $needle) {
                if (str_contains($name, $needle)) {
                    continue 2;
                }
            }
            $out[] = $route;
        }

        return $out;
    }

    /**
     * Build the request path for a route, substituting any required params.
     * Returns null when a required param cannot be resolved (route is skipped).
     */
    public function pathFor(RoutingRoute $route, ?string $locale = null): ?string
    {
        $uri = $route->uri();
        $name = $route->getName() ?? '';

        foreach ($route->parameterNames() as $param) {
            $value = $this->resolveParam($param, $name, $locale);
            if ($value === null) {
                return null;
            }
            $uri = str_replace(['{'.$param.'}', '{'.$param.'?}'], (string) $value, $uri);
        }

        $uri = preg_replace('/\{[^}]+\?}/', '', $uri) ?? $uri;

        return '/'.ltrim((string) $uri, '/');
    }

    private function resolveParam(string $param, string $routeName, ?string $locale): int|string|null
    {
        switch ($param) {
            case 'locale':
                return $locale ?? 'en';
            case 'accreditationNumber':
                return $this->publicRecords['certification']->accreditation_number
                    ?? $this->publicRecords['certification']->getKey();
            case 'trainer':
                return $this->publicRecords['trainer']->getRouteKey();
            case 'slug':
                return str_contains($routeName, 'blog')
                    ? ($this->publicRecords['blog']->slug ?? $this->publicRecords['blog']->getKey())
                    : ($this->publicRecords['page']->slug ?? $this->publicRecords['page']->getKey());
            case 'id':
                return str_contains($routeName, 'membership')
                    ? $this->publicRecords['membership']->getKey()
                    : $this->publicRecords['center']->getKey();
            case 'record':
                return $this->recordKeyFor($routeName);
            default:
                return null;
        }
    }

    /**
     * Resolve `{record}` to a seeded key of the resource's model. Seeding is
     * best-effort: if a factory cannot satisfy its dependencies the route is
     * skipped (returns null) rather than failing the run.
     */
    private function recordKeyFor(string $routeName): int|string|null
    {
        if (! preg_match('/filament\.([^.]+)\.resources\.(.+)\.[^.]+$/', $routeName, $m)) {
            return null;
        }
        [$panel, $resourceKey] = [$m[1], $m[2]];

        $ownedKey = $panel.'.'.$resourceKey;
        if (! empty($this->ownedRecordKeys[$ownedKey])) {
            return $this->ownedRecordKeys[$ownedKey];
        }

        $model = self::RESOURCE_MODELS[$resourceKey] ?? null;
        if ($model === null) {
            return null;
        }

        if (! array_key_exists($model, $this->recordKeys)) {
            try {
                $this->recordKeys[$model] = $model::factory()->create()->getKey();
            } catch (\Throwable) {
                $this->recordKeys[$model] = null;
            }
        }

        return $this->recordKeys[$model];
    }
}
