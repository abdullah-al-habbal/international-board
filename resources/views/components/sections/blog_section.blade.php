<section class="blog" id="blog">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="title text-center ">
                    <h2> Latest <span class="color">Posts</span></h2>
                    <div class="border"></div>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus facere
                        accusamus, reprehenderit libero inventore nam.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <article class="col-lg-4 col-md-6">
                <div class="post-item">
                    <div class="media-wrapper">
                        <img loading="lazy" src="{{ asset('assets/website/images/blog/post-1.jpg') }}"
                            alt="amazing caves coverimage" class="img-fluid">
                    </div>
                    <div class="content">
                        <h3><a href="#">Reasons to Smile</a></h3>
                        <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                            richardson ad squid.</p>
                        <a class="btn btn-main" href="#">{{ __('web.buttons.read_more') }}</a>
                    </div>
                </div>
            </article>
            {{-- More posts can be added here --}}
        </div>
    </div>
</section>