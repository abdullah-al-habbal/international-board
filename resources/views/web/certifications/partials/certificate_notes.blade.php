{{-- resources/views/web/certifications/partials/certificate_notes.blade.php --}}
<div class="certificate-notes">
    <h3 class="certificate-notes-title">{{ __('web.labels.training_info') }}</h3>
    <p class="certificate-notes-text">{!! nl2br(e($certification->notes)) !!}</p>
</div>
