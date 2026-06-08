<!-- resources\views\web\centers\_logo.blade.php -->
<div class="text-center">
    <img
        loading="lazy"
        src="{{ $center->logo_url ?? asset('assets/website/images/avatar.png') }}"
        class="img-standard"
        alt="{{ $center->name }}"
    >
</div>
