<!-- resources\views\components\partials\empty_state.blade.php -->
<div class="col-12 text-center py-5">
    <i class="tf-ion-ios-search" style="font-size: 3rem; color: #ccc;"></i>
    <p class="mt-3 text-muted">{{ $message ?? __('web.labels.no_results') }}</p>
</div>