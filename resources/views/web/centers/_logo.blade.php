<!-- resources\views\web\centers\_logo.blade.php -->
<img
    loading="lazy"
    src="{{ $center->logo_url ?? asset('assets/website/images/about/member.jpg') }}"
    class="img-fluid rounded"
    alt="{{ $center->name }}"
>
