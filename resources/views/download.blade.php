@extends('layouts.app')

@section('title', 'Download - HOA Montaña')

@section('content')
<!-- Download Hero -->
<section class="py-16 bg-gradient-to-br from-primary to-primary-container">
    <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
        <h1 class="mb-6 text-4xl font-bold md:text-5xl text-on-primary">Download HOA Montaña App Today</h1>
        <p class="max-w-2xl mx-auto mb-8 text-xl text-on-primary-container">
            Join our thriving community and experience seamless facility reservations right from your smartphone.
        </p>
        <div class="flex flex-col justify-center gap-4 sm:flex-row">
           <a href="https://github.com/itsGenreee/HOA_Montana---Mobile-App-Download/releases/download/v1.0.0/HOA.Montana.-.Mobile.App.apk" class="flex items-center justify-center px-8 py-4 font-medium transition-colors duration-200 rounded-lg bg-on-primary text-primary hover:bg-on-primary/90">
                <i class="mr-3 text-2xl fa-solid fa-download"></i>
                <div class="text-left">
                    <div class="text-lg font-semibold">Download</div>
                </div>
            </a>
        </div>
    </div>
</section>
@endsection
