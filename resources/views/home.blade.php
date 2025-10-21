@extends('layouts.app')

@section('title', 'Home - HOA Montaña')

@section('content')
<!-- Hero Section -->
<section class="py-16 bg-gradient-to-br from-primary-container to-background md:py-24">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid items-center grid-cols-1 gap-12 md:grid-cols-2">
            <div>
                <h1 class="mb-6 text-4xl font-bold md:text-6xl text-on-surface">
                    Experience the Convenience of Reservation
                </h1>
                <p class="mb-8 text-lg text-on-surface-variant">
                    Reserve HOA Montaña facilities through our mobile app. Check real-time availability,
                    book time slots, confirm the payment at the HOA Office, and access the facility with QR codes.
                </p>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('download') }}" class="px-8 py-3 font-medium text-center transition-colors duration-200 rounded-lg bg-primary text-on-primary">
                        Download Now
                    </a>
                    <a href="{{ route('about') }}" class="px-8 py-3 font-medium text-center transition-colors duration-200 border-2 rounded-lg border-primary text-primary hover:bg-primary-container">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- App Screenshot Carousel -->
            <div class="flex justify-center">
                <div class="relative">
                    <!-- Carousel Container -->
                    <div class="relative w-48 h-96">
                        <!-- Slide 1 - Home Screen -->
                        <div id="slide-1" class="absolute inset-0 p-1 transition-all duration-500 transform shadow-2xl carousel-slide active">
                            <div class="h-full overflow-hidden rounded-2xl">
                                <img src="{{ asset('images/mobile-app/login-page-frame.png') }}" alt="HOA Montaña App Home Screen" class="object-cover w-full h-full">
                            </div>
                        </div>

                        <!-- Slide 2 - Schedule Screen -->
                        <div id="slide-2" class="absolute inset-0 p-1 transition-all duration-500 transform shadow-2xl carousel-slide">
                            <div class="h-full overflow-hidden rounded-2xl">
                                <img src="{{ asset('images/mobile-app/home-page-frame.png') }}" alt="Schedule Screen" class="object-cover w-full h-full">
                            </div>
                        </div>

                        <!-- Slide 3 - QR Code Screen -->
                        <div id="slide-3" class="absolute inset-0 p-1 transition-all duration-500 transform shadow-2xl carousel-slide">
                            <div class="h-full overflow-hidden rounded-2xl">
                                <img src="{{ asset('images/mobile-app/qr-code-home-page-frame.png') }}" alt="QR Code Screen" class="object-cover w-full h-full">
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <button id="prev-btn" class="absolute left-0 flex items-center justify-center w-10 h-10 transition-colors duration-200 transform -translate-x-4 -translate-y-1/2 rounded-full shadow-lg top-1/2 text-on-primary">
                        <i class="fas fa-chevron-left"></i>
                    </button>

                    <button id="next-btn" class="absolute right-0 flex items-center justify-center w-10 h-10 transition-colors duration-200 transform translate-x-4 -translate-y-1/2 rounded-full shadow-lg top-1/2 text-on-primary">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <!-- Dots Indicator -->
                    <div class="flex justify-center mt-6 space-x-2">
                        <button class="w-3 h-3 transition-all duration-200 rounded-full dot bg-primary/30 active:bg-primary" data-slide="0"></button>
                        <button class="w-3 h-3 transition-all duration-200 rounded-full dot bg-primary/30 active:bg-primary" data-slide="1"></button>
                        <button class="w-3 h-3 transition-all duration-200 rounded-full dot bg-primary/30 active:bg-primary" data-slide="2"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    let currentSlide = 0;

    // Function to show a specific slide
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('active');
            slide.style.opacity = '0';
            slide.style.transform = 'translateX(100%)';
        });

        // Reset dots
        dots.forEach(dot => dot.classList.remove('active'));

        // Show current slide
        slides[index].classList.add('active');
        slides[index].style.opacity = '1';
        slides[index].style.transform = 'translateX(0)';

        // Update active dot
        dots[index].classList.add('active');

        currentSlide = index;
    }

    // Next slide function
    function nextSlide() {
        let next = currentSlide + 1;
        if (next >= slides.length) next = 0;
        showSlide(next);
    }

    // Previous slide function
    function prevSlide() {
        let prev = currentSlide - 1;
        if (prev < 0) prev = slides.length - 1;
        showSlide(prev);
    }

    // Event listeners
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);

    // Dot click events
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => showSlide(index));
    });

    // Auto-advance every 5 seconds (optional)
    setInterval(nextSlide, 5000);

    // Initialize first slide
    showSlide(0);
});
</script>

<style>
.carousel-slide {
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.5s ease-in-out;
}

.carousel-slide.active {
    opacity: 1;
    transform: translateX(0);
}

.dot.active {
    background-color: rgb(153, 70, 28) !important; /* Your primary color */
    transform: scale(1.2);
}
</style>

<!-- Features Section -->
<section class="py-16 bg-surface">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-16 text-center">
            <h2 class="mb-4 text-3xl font-bold md:text-4xl text-on-surface">Reserve Facilities</h2>
            <p class="max-w-2xl mx-auto text-lg text-on-surface-variant">
                From tennis matches to birthday celebrations, reserve our premium amenities in just a few taps.
                Real-time availability ensures you never face scheduling conflicts.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div class="p-6 border shadow-lg bg-background rounded-xl border-surface-variant">
                <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-lg bg-secondary-container">
                    <i class="text-2xl ph-fill ph-tennis-ball text-primary"></i>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-on-surface">Tennis Court</h3>
                <p class="text-on-surface-variant">Enjoy friendly matches and practice sessions on our well-maintained tennis courts, perfect for players of all skill levels.</p>
            </div>

            <div class="p-6 border shadow-lg bg-background rounded-xl border-surface-variant">
                <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-lg bg-secondary-container">
                    <i class="text-2xl fas fa-basketball text-secondary"></i>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-on-surface">Basketball Court</h3>
                <p class="text-on-surface-variant">Dribble, shoot, and score on our full-sized basketball court. Perfect for pickup games, team practice, or casual shooting sessions with friends and neighbors.</p>
            </div>

            <div class="p-6 border shadow-lg bg-background rounded-xl border-surface-variant">
                <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-lg bg-tertiary-container">
                    <i class="text-2xl fas fa-cake-candles text-tertiary"></i>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-on-surface">Event Place</h3>
                <p class="text-on-surface-variant">Host unforgettable celebrations, gatherings, and special occasions. Perfect for birthdays, anniversaries, pictorials, and community events in our versatile event space.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-primary-container">
    <div class="max-w-4xl px-4 mx-auto text-center sm:px-6 lg:px-8">
        <h2 class="mb-4 text-3xl font-bold md:text-4xl text-on-primary-container">Join Our Community</h2>
        <p class="max-w-2xl mx-auto mb-8 text-lg text-on-surface-variant">
            Become part of our thriving HOA community and enjoy seamless facility reservations at your fingertips.
        </p>
        <a href="{{ route('download') }}" class="inline-block px-8 py-3 font-medium transition-colors duration-200 rounded-lg bg-primary text-on-primary">
            Download our app
        </a>
    </div>
</section>
@endsection
