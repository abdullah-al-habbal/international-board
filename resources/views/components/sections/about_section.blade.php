<section class="about-2 section" id="about">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="title text-center">
                    <h2>{{ __('web.pages.about.title') }}</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quibusdam reprehenderit accusamus
                        labore iusto,
                        aut, eum itaque illo totam tempora eius.</p>
                    <div class="border"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <img loading="lazy" src="{{ asset('assets/website/images/about/about-2.png') }}" class="img-fluid" alt="">
            </div>
            <div class="col-md-6">
                <ul class="checklist">
                    <li>Donec sed odio dui. Aenean eu leo quam. Pellentesque ornare sem laca quam venenatis
                        vestibulum.</li>
                    <li>Aenean quam. Pellentesque ornare sem laca quam venenatis vestibulum.</li>
                    <li>Donec sed odio dui. Aenean eu leo quam. Pellentesque ornare sem laca quam venenatis
                        vestibulum.</li>
                    <li>Etiam porta sem multipage evint landing magna mollis euismod a pharetra augue.</li>
                    <li>Aenean quam. Pellentesque ornare sem laca quam venenatis vestibulum.</li>
                </ul>
                <a href="{{ route('web.pages.show', 'about-us') }}" class="btn btn-main mt-20">{{ __('web.buttons.learn_more') }}</a>
            </div>
        </div>
    </div>
</section>
