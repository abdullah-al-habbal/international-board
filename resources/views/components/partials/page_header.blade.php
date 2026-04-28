{{-- Shared page title banner. Pass: $title (required), $subtitle (optional) --}}
<section class="page-title bg-2">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="block">
                    <h1>{{ $title }}</h1>
                    @if (!empty($subtitle))
                        <p>{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
