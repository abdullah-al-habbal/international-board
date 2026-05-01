<!-- resources\views\components\partials\page_header.blade.php -->
<section class="page-title bg-2">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="block text-center mt-5">
                    <h1 class="mb-3">{{ $title }}</h1>
                    @if (!empty($subtitle))
                        <p class="lead">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>