<!-- resources/views/web/certifications/_statistics.blade.php -->
<section class="section bg-gray">
    <div class="container">
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
