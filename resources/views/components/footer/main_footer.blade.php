<footer id="footer" class="bg-one">
    <div class="top-footer">
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-lg-4 col-md-6 mb-5 mb-lg-0">
                    <h3>{{ __('web.pages.home.title') }}</h3>
                    <p>Integer posuere erat a ante venenati dapibus posuere velit aliquet. Fusce
                        dapibus, tellus cursus commodo, tortor mauris sed posuere.</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                    <ul>
                        <li>
                            <h3>{{ __('web.pages.services.title') }}</h3>
                        </li>
                        <li><a href="#">Ui/Ux Design</a></li>
                        <li><a href="#">Graphic Design</a></li>
                        <li><a href="#">Web Design</a></li>
                        <li><a href="#">Web Development</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                    <ul>
                        <li>
                            <h3>Quick Links</h3>
                        </li>
                        <li><a href="{{ route('web.home') }}">{{ __('web.components.header.homepage') }}</a></li>
                        @foreach ($navigationPages as $page)
                            <li><a href="{{ route('web.pages.show', $page->slug) }}">{{ $page->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <ul>
                        <li>
                            <h3>Connect with us Socially</h3>
                        </li>
                        <li><a href="">Facebook</a></li>
                        <li><a href="">Twitter</a></li>
                        <li><a href="">Youtube</a></li>
                        <li><a href="">Github</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <h5>&copy; Copyright {{ date('Y') }}. All rights reserved.</h5>
        <h6>Design and Developed by Abdullah Alhabal</a></h6>
    </div>
</footer>