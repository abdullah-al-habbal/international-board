<!-- resources\views\components\partials\pagination.blade.php -->
@if ($items->hasPages())
    <div class="row justify-content-center mt-5">
        <div class="col-auto">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
@endif
