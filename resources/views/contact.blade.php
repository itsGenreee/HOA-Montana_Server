@extends('layouts.app')

@section('title', 'Contact - HOA Montaña')

@section('content')
<!-- Contact Hero -->
<section class="py-16 bg-surface">
    <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
        <h1 class="mb-6 text-4xl font-bold md:text-5xl text-on-surface">Get In Touch</h1>
        <p class="max-w-2xl mx-auto text-xl text-on-surface-variant">
            Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
    </div>
</section>

<!-- Contact Information -->
<section class="py-16 bg-background">
    <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="mb-12 text-3xl font-bold md:text-4xl text-on-surface">Contact Information</h2>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="p-6 text-center border shadow-lg bg-surface rounded-xl border-surface-variant">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-lg bg-primary-container">
                        <i class="text-2xl fas fa-map-marker-alt text-primary"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-on-surface">Our Office</h3>
                    <p class="text-on-surface-variant">Metro Montana Village<br>Brgy. Burgos, Rod., Rizal</p>
                </div>

                <div class="p-6 text-center border shadow-lg bg-surface rounded-xl border-surface-variant">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-lg bg-secondary-container">
                        <i class="text-2xl fas fa-phone text-secondary"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-on-surface">Phone</h3>
                    <p class="text-on-surface-variant">+1 (555) 123-4567<br>Mon-Fri 8am-6pm</p>
                </div>

                <div class="p-6 text-center border shadow-lg bg-surface rounded-xl border-surface-variant">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-lg bg-tertiary-container">
                        <i class="text-2xl fas fa-envelope text-tertiary"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-on-surface">Email</h3>
                    <p class="text-on-surface-variant">support@hoamontana.com<br>Reply within 24 hours</p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16 text-center">
            <h2 class="mb-8 text-3xl font-bold md:text-4xl text-on-surface">Frequently Asked Questions</h2>
            <div class="max-w-3xl mx-auto space-y-4">
                <div class="p-6 text-left rounded-lg bg-surface-variant">
                    <h3 class="mb-3 text-lg font-semibold text-on-surface-variant">How do I make a reservation?</h3>
                    <p class="text-on-surface-variant">Simply download our mobile app, create your account, and navigate to the Reservation tab. Select your preferred facility, choose your date and time slot, then visit the HOA office to confirm and complete your payment.</p>
                </div>

                <div class="p-6 text-left rounded-lg bg-surface-variant">
                    <h3 class="mb-3 text-lg font-semibold text-on-surface-variant">How do I access the facility I reserved?</h3>
                    <p class="text-on-surface-variant">After confirming your reservation at the HOA office, your app will generate a unique QR code. Simply present this QR code to the facility staff at your reserved time for quick scanning and seamless access.</p>
                </div>

                <div class="p-6 text-left rounded-lg bg-surface-variant">
                    <h3 class="mb-3 text-lg font-semibold text-on-surface-variant">Can I use the app offline?</h3>
                    <p class="text-on-surface-variant">The app requires an internet connection for most features, including viewing real-time availability, making reservations, and generating QR codes. This ensures you always have access to the most up-to-date schedule information.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
