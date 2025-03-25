<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Head content -->
</head>

<body class="antialiased">
    @livewire('livewire.navigation.main-navbar')

    <main class="max-w-screen-xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    @livewire('navigation.main-footer')
</body>

</html>
