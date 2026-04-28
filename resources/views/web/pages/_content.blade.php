<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                @if ($page->image)
                    <img loading="lazy"
                         src="{{ Storage::url($page->image) }}"
                         class="img-fluid mb-4 rounded"
                         alt="{{ $page->title }}">
                @endif
                <div class="content">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
</section>
