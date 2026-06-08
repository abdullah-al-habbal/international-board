<!-- resources\views\web\trainers\_avatar.blade.php -->
<div class="text-center">
    <img
        loading="lazy"
        src="{{ $trainer->avatar_url ?? asset('assets/website/images/avatar.png') }}"
        class="img-standard"
        alt="{{ $trainer->name }}"
    >
</div>
