<!-- resources\views\web\pages\_content.blade.php -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                @if ($page->image)
                    <img loading="lazy" src="{{ Storage::url($page->image) }}" class="img-fluid mb-4 rounded"
                        alt="{{ $page->title }}">
                @endif
                <div class="content">
                    {!! $page->content !!}
                </div>

                @if($page->slug === 'contact-us')
                    @include('web.contact._form')
                @endif
            </div>
        </div>
    </div>
</section>