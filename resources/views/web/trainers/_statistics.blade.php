<!-- resources/views/web/trainers/_statistics.blade.php -->
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
