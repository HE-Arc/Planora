<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Planora — Partir ensemble')</title>
    <script>try { document.documentElement.dataset.theme = localStorage.getItem('planora-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'); } catch (_) {}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="planora">
    <a href="#main" class="skip-link">Aller au contenu</a>
    <header class="site-header">
        <a class="brand" href="{{ url('/') }}"><span class="brand-mark" aria-hidden="true">↗</span>planora<span class="brand-dot">.</span></a>
        <span class="header-note">Moins de plans. Plus de départs.</span>
        <div class="header-actions">
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Changer le thème">◐ <span data-theme-label>Changer le thème</span></button>
            {{-- TODO PHP : remplacer par le compte connecté ou le lien de connexion. --}}
            @yield('account', ' ')
        </div>
    </header>
    <main id="main" class="page-shell">@yield('content')</main>
    <footer class="site-footer"><span>Planora © {{ date('Y') }}</span><span>Le voyage commence avec une rencontre.</span></footer>
    @stack('scripts')
</body>
</html>