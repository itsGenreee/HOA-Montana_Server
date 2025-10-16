@extends('layouts.public')

@section('content')
  {{-- Hero Section --}}
  <section id="home" class="relative bg-gradient-to-b from-blue-600 to-blue-700 text-white py-32 text-center">
    <div class="max-w-3xl mx-auto px-6">
      <h1 class="text-5xl font-bold mb-4">Welcome to HOA Montana</h1>
      <p class="text-lg text-blue-100 mb-8">
        Building a better community — together.
      </p>
      <a href="#about" class="inline-block bg-white text-blue-700 font-semibold px-6 py-3 rounded-lg shadow hover:bg-blue-50 transition">
        Learn More
      </a>
    </div>

    {{-- Subtle wave separator --}}
    <div class="absolute bottom-0 left-0 right-0 overflow-hidden leading-[0] rotate-180">
      <svg class="relative block w-full h-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120">
        <path d="M321.39 56.44C187.16 82.09 85.94 79.6 0 65.48V120h1200V0c-91.71 21.78-189.86 39.58-321.39 56.44z" fill="#ffffff"></path>
      </svg>
    </div>
  </section>

  {{-- About Section --}}
  <section id="about" class="py-20 bg-white text-center">
    <div class="max-w-5xl mx-auto px-6">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">About Our Community</h2>
      <p class="text-gray-600 leading-relaxed max-w-3xl mx-auto">
        HOA Montana is dedicated to fostering a peaceful, organized, and secure environment
        for all residents. We ensure the smooth operation of facilities and promote
        harmonious living within our neighborhood.
      </p>
    </div>
  </section>

  {{-- Announcements Section --}}
  <section id="announcements" class="py-20 bg-gray-50 text-center">
    <div class="max-w-6xl mx-auto px-6">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Announcements</h2>
      <p class="text-gray-600 mb-12">Stay updated with the latest community news and events.</p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition">
          <h3 class="font-semibold text-lg mb-2">Community Clean-up Drive</h3>
          <p class="text-gray-600 text-sm">Join us this weekend for a neighborhood clean-up initiative!</p>
        </div>
        <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition">
          <h3 class="font-semibold text-lg mb-2">Upcoming Meeting</h3>
          <p class="text-gray-600 text-sm">Monthly HOA meeting scheduled for next Saturday at the clubhouse.</p>
        </div>
        <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition">
          <h3 class="font-semibold text-lg mb-2">Security Update</h3>
          <p class="text-gray-600 text-sm">New CCTV installations are now active throughout the area.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- Contact Section --}}
  <section id="contact" class="py-20 bg-white text-center">
    <div class="max-w-5xl mx-auto px-6">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Get in Touch</h2>
      <p class="text-gray-600 mb-8">Have questions or concerns? Reach out to us anytime.</p>

      <form action="#" method="POST" class="max-w-xl mx-auto text-left space-y-4">
        <div>
          <label class="block text-gray-700 mb-2">Name</label>
          <input type="text" name="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Email</label>
          <input type="email" name="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Message</label>
          <textarea name="message" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
          Send Message
        </button>
      </form>
    </div>
  </section>
@endsection
