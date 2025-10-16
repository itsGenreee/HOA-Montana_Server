<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'HOA Montana' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="text-gray-900 bg-gray-50">
  <main class="min-h-screen">
    @yield('content')
  </main>

  <footer class="py-4 text-sm text-center text-gray-600 bg-gray-100">
    © {{ date('Y') }} HOA. All rights reserved.
  </footer>
</body>
</html>
