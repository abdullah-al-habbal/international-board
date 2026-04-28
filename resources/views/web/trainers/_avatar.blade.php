<!-- resources\views\web\trainers\_avatar.blade.php -->
<img
    loading="lazy"
    src="{{ $trainer->avatar_url ?? asset('assets/website/images/about/member.jpg') }}"
    class="img-fluid rounded"
    alt="{{ $trainer->name }}"
>
