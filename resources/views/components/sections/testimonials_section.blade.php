<!-- resources\views\components\sections\testimonials_section.blade.php -->
@if (!empty($testimonials))
    <section class="testimonial section" id="testimonial">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="testimonial-slider"> @foreach ($testimonials as $testimonial) <div class="item text-center">
                            <i class="tf-ion-chatbubbles"></i>
                            <div class="client-details">
                                <p>{{ $testimonial['text'] ?? '' }}</p>
                            </div>
                            @if (!empty($testimonial['avatar']))
                                <div class="client-thumb">
                                    <img loading="lazy" src="{{ asset($testimonial['avatar']) }}" class="img-fluid"
                                        alt="{{ $testimonial['name'] ?? '' }}">
                                </div>
                            @endif
                            <div class="client-meta">
                                <h3>{{ $testimonial['name'] ?? '' }}</h3>
                                <span>{{ $testimonial['role'] ?? '' }}</span>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif