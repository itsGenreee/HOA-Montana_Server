<nav class="bg-white shadow-md sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">

      {{-- Logo / Brand --}}
      <a href="{{ url('/') }}" class="flex items-center space-x-2">
        <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
        </svg>
        <span class="font-bold text-xl text-gray-800">HOA Montana</span>
      </a>

      {{-- Desktop Menu --}}
      <div class="hidden md:flex space-x-8">
        <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium transition">About</a>
        <a href="#services" class="text-gray-700 hover:text-blue-600 font-medium transition">Services</a>
        <a href="#announcements" class="text-gray-700 hover:text-blue-600 font-medium transition">Announcements</a>
        <a href="#contact" class="text-gray-700 hover:text-blue-600 font-medium transition">Contact</a>
      </div>

      {{-- Admin Login Button --}}
      <a href="{{ route('login') }}" class="hidden md:inline-block bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
        Admin Login
      </a>

      {{-- Mobile Menu Toggle --}}
      <button id="menu-btn" class="md:hidden focus:outline-none text-gray-700 hover:text-blue-600">
        <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>

  {{-- Mobile Menu --}}
  <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 bg-white">
    <a href="#about" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">About</a>
    <a href="#services" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Services</a>
    <a href="#announcements" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Announcements</a>
    <a href="#contact" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Contact</a>
    <a href="{{ route('login') }}" class="block px-4 py-2 text-blue-600 font-semibold hover:bg-blue-50">Admin Login</a>
  </div>
</nav>

<script>
document.getElementById('menu-btn').addEventListener('click', () => {
  const menu = document.getElementById('mobile-menu');
  menu.classList.toggle('hidden');
});
</script>
