# Statistics Dashboard for Public Pages

**Type:** Feature  
**Status:** Ready for implementation  
**Owner:** Web Engineering  
**Target Release:** v2.3.0  

---

## 1. Overview

Add animated statistics counters to the public‑facing pages for **Certifications**, **Certified Centers**, and **Trainers**.  
The counters must start at zero when scrolled into view and smoothly increase to the actual value.

---

## 2. User Story

- As a **visitor** of the International Board website  
  I want to see key metrics (number of certifications, countries, trainees, trainers, etc.) with a **dynamic number animation**  
  so that the platform looks modern, trustworthy, and data‑driven.

---

## 3. Acceptance Criteria

### 3.1 Certifications Page (`/web/certifications`)

- [ ] Display the following statistics **above the search form**:
  - Total certifications
  - Number of distinct countries
  - Number of distinct trainees
  - Number of distinct trainers
- [ ] Show a secondary block: “Certifications by Creator” with counts for:
  - Board
  - Center
  - Trainer
- [ ] All numbers animate from `0` to target when the statistics section scrolls into view.
- [ ] Animation uses a separate CSS class (`.stat-counter`) to avoid conflicts with existing counters.
- [ ] Section uses a light grey background (`.bg-gray`) and the existing `counters-item` styling for consistency.

### 3.2 Certified Centers Page (`/web/centers`)

- [ ] Display **above the filter/search area**:
  - Total active centers
  - Number of distinct countries with active centers
- [ ] Same animation behaviour as certifications.

### 3.3 Trainers Page (`/web/trainers`)

- [ ] Display **above the intro text**:
  - Total active trainers
- [ ] Same animation behaviour.

---

## 4. Technical Requirements

### 4.1 Backend

- Add a `getStatistics()` method to each repository:
  - `CertificationRepository`
  - `CertifiedCenterRepository`
  - `TrainerRepository`
- Wire the data through the corresponding services and controllers.
- Return associative arrays with scalar values – no heavy nested structures.
- Use existing scopes and count methods where possible.

### 4.2 Frontend

- Create a Blade partial for each page:
  - `web/certifications/_statistics.blade.php`
  - `web/centers/_statistics.blade.php`
  - `web/trainers/_statistics.blade.php`
- Include the partial in the respective `index.blade.php` immediately after the page header.
- Use a small vanilla JavaScript / jQuery script inside `@push('scripts')` that:
  - Detects when the statistics container enters the viewport.
  - Triggers a jQuery `.animate()` on the `.stat-counter` elements.
  - Animates only once (flag via `data-stat-animated`).
- Re‑use existing icon classes (`tf-ion-...`) and card styling (`card-glow`).

### 4.3 Translation

- All labels and titles must be translatable via `__('web.stats.*')`.
- Example keys:
  ```php
  'stats.certifications',
  'stats.countries',
  'stats.trainees',
  'stats.trainers',
  'stats.by_creator',
  'stats.board',
  'stats.center',
  'stats.trainer',
  'stats.active_centers',
  'stats.active_trainers',
  ```

---

## 5. File Changes Summary

| File | Action |
|------|--------|
| `app/Repositories/Certification/CertificationRepository.php` | Add `getStatistics()` |
| `app/Services/Certification/CertificationService.php` | Add `getStatistics()` |
| `app/Http/Controllers/Web/Certification/CertificationController.php` | Pass `$stats` to view |
| `resources/views/web/certifications/_statistics.blade.php` | New partial |
| `resources/views/web/certifications/index.blade.php` | Include statistics partial |
| `app/Repositories/CertifiedCenter/CertifiedCenterRepository.php` | Add `getStatistics()` |
| `app/Services/CertifiedCenter/CertifiedCenterService.php` | Add `getStatistics()` |
| `app/Http/Controllers/Web/CertifiedCenter/CertifiedCenterController.php` | Pass `$stats` to view |
| `resources/views/web/centers/_statistics.blade.php` | New partial |
| `resources/views/web/centers/index.blade.php` | Include statistics partial |
| `app/Repositories/Trainer/TrainerRepository.php` | Add `getStatistics()` |
| `app/Services/Trainer/TrainerService.php` | Add `getStatistics()` |
| `app/Http/Controllers/Web/Trainer/TrainerController.php` | Pass `$stats` to view |
| `resources/views/web/trainers/_statistics.blade.php` | New partial |
| `resources/views/web/trainers/index.blade.php` | Include statistics partial |
| Translation files | Add `web.stats.*` keys |

---

## 6. Design / UX Notes

- The counter animation should last about 1.2 seconds with a smooth “swing” easing.
- Statistics section must not interfere with the existing page layout; it should sit between the page header and the main content.
- Use the same `counters-item` class already styled in `style.css` to ensure visual consistency.
- The animation script must be self‑contained and triggered only when the element is near the viewport (offset ~100px).

---

## 7. Out of Scope

- Real‑time data updates.
- Admin panel statistics.
- Graphs or charts – only numeric counters.
- Any change to the existing counter on the home page (that remains untouched).

I’ve built the statistics sections with animated counters for the **Certifications**, **Centers**, and **Trainers** pages. The counters start at zero and scroll‑trigger the number‑increase animation using a small custom script (non‑conflicting with the existing counter).

Below you’ll find the required repository/service methods, controller updates, new Blade partials, and the updated views. Translation keys are used everywhere – you can add them to your language files as needed.

---

## 1. Certification Statistics

### Repository method
**File:** `app/Repositories/Certification/CertificationRepository.php`  
Add this method:

```php
public function getStatistics(): array
{
    $total = $this->model->newQuery()->count();
    $distinctCountries = $this->model->newQuery()
        ->whereNotNull('country_id')
        ->distinct('country_id')
        ->count('country_id');
    $distinctTrainees = $this->model->newQuery()
        ->whereNotNull('trainee_id')
        ->distinct('trainee_id')
        ->count('trainee_id');
    $distinctTrainers = $this->model->newQuery()
        ->whereNotNull('assigned_trainer_id')
        ->distinct('assigned_trainer_id')
        ->count('assigned_trainer_id');
    $byCreatorRaw = $this->model->newQuery()
        ->selectRaw('creator_type, count(*) as count')
        ->groupBy('creator_type')
        ->pluck('count', 'creator_type')
        ->toArray();

    return [
        'total'       => $total,
        'countries'   => $distinctCountries,
        'trainees'    => $distinctTrainees,
        'trainers'    => $distinctTrainers,
        'by_creator'  => $byCreatorRaw,
    ];
}
```

### Service method
**File:** `app/Services/Certification/CertificationService.php`  
Add:

```php
public function getStatistics(): array
{
    return $this->repo->getStatistics();
}
```

### Controller update
**File:** `app/Http/Controllers/Web/Certification/CertificationController.php`  
Change `index()`:

```php
public function index(): View
{
    $this->seoService->setMeta(
        __('web.pages.certifications.title'),
        __('web.pages.certifications.subtitle')
    );
    $stats = $this->service->getStatistics();
    return view('web.certifications.index', compact('stats'));
}
```

### New partial – statistics
**File:** `resources/views/web/certifications/_statistics.blade.php`

```blade
<section class="section bg-gray">
    <div class="container">
        {{-- Main counters --}}
        <div class="row">
            <div class="col-md-3 text-center">
                <div class="counters-item">
                    <i class="tf-ion-ios-list-outline"></i>
                    <span class="stat-counter" data-count="{{ $stats['total'] }}">0</span>
                    <h3>{{ __('web.stats.certifications') }}</h3>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="counters-item">
                    <i class="tf-ion-earth"></i>
                    <span class="stat-counter" data-count="{{ $stats['countries'] }}">0</span>
                    <h3>{{ __('web.stats.countries') }}</h3>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="counters-item">
                    <i class="tf-ion-ios-people-outline"></i>
                    <span class="stat-counter" data-count="{{ $stats['trainees'] }}">0</span>
                    <h3>{{ __('web.stats.trainees') }}</h3>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="counters-item">
                    <i class="tf-ion-ios-person-outline"></i>
                    <span class="stat-counter" data-count="{{ $stats['trainers'] }}">0</span>
                    <h3>{{ __('web.stats.trainers') }}</h3>
                </div>
            </div>
        </div>

        {{-- By creator type --}}
        <div class="row mt-5">
            <div class="col-12 text-center">
                <h4 class="mb-4">{{ __('web.stats.by_creator') }}</h4>
                <div class="row justify-content-center">
                    @foreach($stats['by_creator'] as $type => $count)
                        @php
                            $label = match($type) {
                                'App\\Models\\User'             => __('web.stats.board'),
                                'App\\Models\\CertifiedCenter'  => __('web.stats.center'),
                                'App\\Models\\Trainer'          => __('web.stats.trainer'),
                                default                        => class_basename($type),
                            };
                        @endphp
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card card-glow p-3">
                                <div class="counters-item">
                                    <span class="stat-counter" data-count="{{ $count }}">0</span>
                                    <h4>{{ $label }}</h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
  (function() {
    var animated = false;
    function animateStatCounters() {
      $('.stat-counter').each(function() {
        var $el = $(this);
        if ($el.data('stat-animated')) return;
        $el.data('stat-animated', true);
        var target = parseInt($el.attr('data-count'), 10) || 0;
        $({ count: 0 }).animate({ count: target }, {
          duration: 1200,
          easing: 'swing',
          step: function(now) { $el.text(Math.floor(now)); },
          complete: function() { $el.text(target); }
        });
      });
    }
    function checkAndAnimate() {
      if ($('.stat-counter').length) {
        var top = $('.stat-counter').first().offset().top - window.innerHeight + 100;
        if ($(window).scrollTop() > top && !animated) {
          animated = true;
          animateStatCounters();
        }
      }
    }
    $(window).on('scroll', checkAndAnimate);
    $(window).on('load', checkAndAnimate);
  })();
</script>
@endpush
```

### Update certifications index
**File:** `resources/views/web/certifications/index.blade.php`

```blade
@extends('layouts.master')
@section('title', __('web.pages.certifications.title'))
@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.certifications.title'),
        'subtitle' => __('web.pages.certifications.subtitle'),
    ])

    @include('web.certifications._statistics')    {{-- NEW --}}
    @include('web.certifications._search_section')
@endsection
```

---

## 2. Centers Statistics

### Repository method
**File:** `app/Repositories/CertifiedCenter/CertifiedCenterRepository.php`  
Add:

```php
public function getStatistics(): array
{
    return [
        'total_active_centers' => $this->countActive(),
        'active_countries' => $this->model->newQuery()
            ->where('is_active', true)
            ->whereNotNull('country_id')
            ->distinct('country_id')
            ->count('country_id'),
    ];
}
```

### Service method
**File:** `app/Services/CertifiedCenter/CertifiedCenterService.php`  
Add:

```php
public function getStatistics(): array
{
    return $this->repo->getStatistics();
}
```

### Controller update
**File:** `app/Http/Controllers/Web/CertifiedCenter/CertifiedCenterController.php`  
Change `index()`:

```php
public function index(CenterIndexRequest $request): View
{
    $this->seoService->setMeta(
        __('web.pages.centers.title'),
        __('web.pages.centers.subtitle')
    );
    $centers   = $this->service->listActive(filters: $request->filters(), perPage: 12);
    $countries = $this->service->getFilterCountries();
    $stats     = $this->service->getStatistics();                     // NEW
    return view('web.centers.index', compact('centers', 'countries', 'stats'));
}
```

### New partial – statistics
**File:** `resources/views/web/centers/_statistics.blade.php`

```blade
<section class="section bg-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <div class="counters-item">
                    <i class="tf-ion-ios-home-outline"></i>
                    <span class="stat-counter" data-count="{{ $stats['total_active_centers'] }}">0</span>
                    <h3>{{ __('web.stats.active_centers') }}</h3>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="counters-item">
                    <i class="tf-ion-earth"></i>
                    <span class="stat-counter" data-count="{{ $stats['active_countries'] }}">0</span>
                    <h3>{{ __('web.stats.countries') }}</h3>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
  (function() {
    var animated = false;
    function animateStatCounters() {
      $('.stat-counter').each(function() {
        var $el = $(this);
        if ($el.data('stat-animated')) return;
        $el.data('stat-animated', true);
        var target = parseInt($el.attr('data-count'), 10) || 0;
        $({ count: 0 }).animate({ count: target }, {
          duration: 1200,
          easing: 'swing',
          step: function(now) { $el.text(Math.floor(now)); },
          complete: function() { $el.text(target); }
        });
      });
    }
    function checkAndAnimate() {
      if ($('.stat-counter').length) {
        var top = $('.stat-counter').first().offset().top - window.innerHeight + 100;
        if ($(window).scrollTop() > top && !animated) {
          animated = true;
          animateStatCounters();
        }
      }
    }
    $(window).on('scroll', checkAndAnimate);
    $(window).on('load', checkAndAnimate);
  })();
</script>
@endpush
```

### Update centers index
**File:** `resources/views/web/centers/index.blade.php`

```blade
@extends('layouts.master')
@section('title', __('web.pages.centers.title'))
@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.centers.title'),
        'subtitle' => __('web.pages.centers.subtitle'),
    ])

    @include('web.centers._statistics')    {{-- NEW --}}

    <section class="section">
        <div class="container">
            @include('web.centers._filters')
            <div class="row">
                @include('components.sections.center_list')
            </div>
            @include('components.partials.pagination', ['items' => $centers])
        </div>
    </section>
@endsection
```

---

## 3. Trainers Statistics

### Repository method
**File:** `app/Repositories/Trainer/TrainerRepository.php`  
Add:

```php
public function getStatistics(): array
{
    return ['total_active_trainers' => $this->countActive()];
}
```

### Service method
**File:** `app/Services/Trainer/TrainerService.php`  
Add:

```php
public function getStatistics(): array
{
    return $this->repo->getStatistics();
}
```

### Controller update
**File:** `app/Http/Controllers/Web/Trainer/TrainerController.php`  
Change `index()`:

```php
public function index(TrainerIndexRequest $request): View
{
    $this->seoService->setMeta(
        __('web.pages.trainers.title'),
        __('web.pages.trainers.subtitle')
    );
    $trainers      = $this->service->listActive(filters: $request->filters(), perPage: 12);
    $whatsappNumber = $this->appSettingService->getByKey('whatsapp_number');
    $stats         = $this->service->getStatistics();                // NEW
    return view('web.trainers.index', compact('trainers', 'whatsappNumber', 'stats'));
}
```

### New partial – statistics
**File:** `resources/views/web/trainers/_statistics.blade.php`

```blade
<section class="section bg-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <div class="counters-item">
                    <i class="tf-ion-ios-person-outline"></i>
                    <span class="stat-counter" data-count="{{ $stats['total_active_trainers'] }}">0</span>
                    <h3>{{ __('web.stats.active_trainers') }}</h3>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
  (function() {
    var animated = false;
    function animateStatCounters() {
      $('.stat-counter').each(function() {
        var $el = $(this);
        if ($el.data('stat-animated')) return;
        $el.data('stat-animated', true);
        var target = parseInt($el.attr('data-count'), 10) || 0;
        $({ count: 0 }).animate({ count: target }, {
          duration: 1200,
          easing: 'swing',
          step: function(now) { $el.text(Math.floor(now)); },
          complete: function() { $el.text(target); }
        });
      });
    }
    function checkAndAnimate() {
      if ($('.stat-counter').length) {
        var top = $('.stat-counter').first().offset().top - window.innerHeight + 100;
        if ($(window).scrollTop() > top && !animated) {
          animated = true;
          animateStatCounters();
        }
      }
    }
    $(window).on('scroll', checkAndAnimate);
    $(window).on('load', checkAndAnimate);
  })();
</script>
@endpush
```

### Update trainers index
**File:** `resources/views/web/trainers/index.blade.php`

```blade
@extends('layouts.master')
@section('title', __('web.pages.trainers.title'))
@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.trainers.title'),
        'subtitle' => __('web.pages.trainers.subtitle'),
    ])

    @include('web.trainers._statistics')    {{-- NEW --}}

    <div class="container">
        <p class="lead">{{ __('web.pages.trainers.intro_text') }}</p>
        <div class="d-flex justify-content-start gap-3 mb-4">
            <a href="{{ route('web.memberships.index') }}" class="btn btn-main">
                {{ __('web.buttons.apply_membership') }}
            </a>
            @if(!empty($whatsappNumber))
                <a href="https://wa.me/{{ $whatsappNumber }}" class="btn btn-success" target="_blank">
                    {{ __('web.buttons.whatsapp_contact') }}
                </a>
            @endif
        </div>
    </div>

    <section class="section">
        <div class="container">
            @include('web.trainers._filters')
            <div class="row">
                @include('components.sections.trainer_list')
            </div>
            @include('components.partials.pagination', ['items' => $trainers])
        </div>
    </section>
@endsection
```

---

## Important Notes

- The animation uses a custom class **`.stat-counter`** (not `.counter`) to avoid conflicts with the existing counter in `script.js`.
- The script triggers once when the element enters the viewport (plus 100px offset) and never repeats.
- All text is wrapped in `__()` helpers – just add the corresponding keys to your language files, e.g.:
  ```php
  // resources/lang/en/web.php
  'stats' => [
      'certifications'    => 'Total Certifications',
      'countries'         => 'Countries',
      'trainees'          => 'Trainees',
      'trainers'          => 'Trainers',
      'by_creator'        => 'Certifications by Creator',
      'board'             => 'Board',
      'center'            => 'Center',
      'trainer'           => 'Trainer',
      'active_centers'    => 'Active Centers',
      'active_trainers'   => 'Active Trainers',
  ],
  ```

Now the pages will show beautiful animated statistics just like you requested.