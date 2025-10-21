@extends('layouts.app')

@section('title', 'About - HOA Montaña')

@section('content')
<!-- About Hero -->
<section class="py-16 bg-surface">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="mb-6 text-4xl font-bold md:text-5xl text-on-surface">About HOA Montaña</h1>
            <p class="max-w-3xl mx-auto text-xl text-on-surface-variant">
                We're on a mission to revolutionize our community's reservation experience through innovative technology and user-centered design.
            </p>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="py-16 bg-background">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid items-center grid-cols-1 gap-12 lg:grid-cols-2">
            <div>
                <h2 class="mb-6 text-3xl font-bold text-on-surface">Our Story</h2>
                <p class="mb-4 text-on-surface-variant">
                    It all began with the frustration of manual facility reservations. Remember calling the HOA office,
                    only to find out your preferred time slot was already taken? Or showing up for your tennis game
                    to discover someone else had the same reservation? The lack of real-time visibility into schedule
                    availability made planning community activities a guessing game.
                </p>
                <p class="mb-4 text-on-surface-variant">
                    We experienced firsthand the inconvenience of outdated reservation systems—double bookings,
                    scheduling conflicts, and wasted trips to the office. The manual process was not just inefficient;
                    it was preventing our community from fully enjoying the facilities we all contribute to maintain.
                </p>
                <p class="text-on-surface-variant">
                    That's why we created HOA Montaña's digital reservation platform. Our mission is to transform
                    community living by providing a seamless, transparent, and convenient way to reserve facilities.
                    Now, with real-time availability, instant bookings, and QR code access, our community can focus
                    on what truly matters—enjoying time together and making the most of our shared spaces.
                </p>
            </div>
            <div class="p-8 bg-surface-variant rounded-2xl">
    <div class="grid grid-cols-2 gap-6">
        <div class="overflow-hidden bg-background rounded-xl">
            <img src="{{ asset('images/facilities/Tennis_Court-facility2.jpg') }}" alt="Tennis Court" class="object-cover w-full h-32">
            <div class="p-4">
                <div class="font-semibold text-on-surface">Tennis Court</div>
            </div>
        </div>
        <div class="overflow-hidden bg-background rounded-xl">
            <img src="{{ asset('images/facilities/basketball_court-facility2.jpg') }}" alt="Basketball Court" class="object-cover w-full h-32">
            <div class="p-4">
                <div class="font-semibold text-on-surface">Basketball Court</div>
            </div>
        </div>
        <div class="overflow-hidden bg-background rounded-xl">
            <img src="{{ asset('images/facilities/event_place-facility1.jpg') }}" alt="Event Place main entrance" class="object-cover w-full h-32">
            <div class="p-4">
                <div class="font-semibold text-on-surface">Event Place (Main Entrance)</div>
            </div>
        </div>
        <div class="overflow-hidden bg-background rounded-xl">
            <img src="{{ asset('images/facilities/event_place-facility4.jpg') }}" alt="Event Place hall" class="object-cover w-full h-32">
            <div class="p-4">
                <div class="font-semibold text-on-surface">Event Place (Hall)</div>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</section>

@endsection
