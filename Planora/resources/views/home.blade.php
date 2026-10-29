@extends('layouts.planora')

@section('title', 'Explorer les voyages — Planora')

@section('account')
    {{-- TODO PHP: display the signed-in account or connect the login link. --}}
    <button type="button" class="avatar" aria-label="Mon compte (bientôt disponible)" disabled>P</button>
@endsection

@section('content')
<div class="home-page">
    <section class="home-hero" aria-labelledby="home-title">
        <div>
            <p class="eyebrow">LE MONDE, À PLUSIEURS</p>
            <h1 id="home-title">Votre prochaine histoire<br>commence <em>ici.</em></h1>
            <p class="home-intro">Un coin du monde. Des personnes qui vous ressemblent.<br>Et cette petite envie de prendre le large.</p>
        </div>
        <div class="home-stamp" aria-hidden="true"><span>PRENDRE LE LARGE</span><strong>↗</strong><span>ENSEMBLE</span></div>
    </section>

    <div class="home-searchbar" role="search" aria-label="Trouver un voyage">
        <label class="home-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg><span class="home-sr-only">Rechercher un voyage</span><input id="home-search" type="search" placeholder="Un pays, une envie, une escapade…" autocomplete="off"></label>
        <label class="home-filter"><span>Votre ambiance</span><select id="home-filter"><option value="">Toutes les envies</option><option>Nature</option><option>Culture</option><option>Aventure</option><option>Détente</option></select></label>
    </div>

    <section aria-labelledby="home-trips-title">
        <div class="home-section-heading"><h2 id="home-trips-title">Des départs qui donnent envie</h2><span id="home-count" aria-live="polite"></span></div>
        {{-- TODO PHP: pass $events from the controller. null = mockup examples; [] = empty state.
             Each trip: country, title, description, tag, dates, price, places, image_url, url.
             The examples below do not represent real offers. --}}
        {{-- @php
            $events = $events ?? [
                ['country' => 'Portugal', 'title' => 'L’océan pour horizon', 'description' => 'Des ruelles de Lisbonne aux plages sauvages, on prend le temps.', 'tag' => 'Détente', 'dates' => '12–19 juin', 'price' => 480, 'places' => '3 places libres', 'scene' => 'coast'],
                ['country' => 'Italie', 'title' => 'La dolce vita, ensemble', 'description' => 'Petits villages, grandes tablées et soleil de fin de journée.', 'tag' => 'Culture', 'dates' => '03–10 juillet', 'price' => 620, 'places' => '2 places libres', 'scene' => 'village'],
                ['country' => 'Norvège', 'title' => 'L’appel du grand air', 'description' => 'Un sac à dos, des fjords et des kilomètres de souvenirs.', 'tag' => 'Nature', 'dates' => '18–25 août', 'price' => 850, 'places' => '4 places libres', 'scene' => 'mountain'],
                ['country' => 'Maroc', 'title' => 'Sous un autre ciel', 'description' => 'Des souks aux dunes, une parenthèse loin du quotidien.', 'tag' => 'Aventure', 'dates' => '05–12 septembre', 'price' => 540, 'places' => '5 places libres', 'scene' => 'desert'],
            ];
        @endphp --}}

        <div class="home-grid">
            @foreach($events as $event)
            {{-- <article class="home-card" data-home-card data-tag="{{ $event['tag'] ?? '' }}"> --}}
                <div class="home-card-image">
                    
                    {{-- @if(!empty($event['image_url'])) --}}
                    {{ $event }}
                        {{-- <img src="{{ $event->image_url }}" alt="{{ $event->title ?? 'Destination du voyage' }}" loading="lazy" width="600" height="460"> --}}
                    {{-- @else --}}
                        {{-- Decorative fallback illustration; replace with the trip photo.
                        <svg viewBox="0 0 300 230" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            @switch($event['scene'] ?? 'coast')
                                @case('village')
                                    <path fill="#e7d8bf" d="M0 0h300v230H0z"/><circle fill="#fff0cc" cx="222" cy="58" r="29"/><path fill="#a5b39a" d="M0 160Q90 80 180 160T300 150v80H0z"/><path fill="#c59272" d="M25 118h70v112H25zM115 90h66v140h-66zM207 133h76v97h-76z"/><path fill="#8b6152" d="m16 118 44-31 44 31zm90-28 42-35 42 35zm92 43 44-30 49 30z"/><path fill="#edcf9c" d="M45 150h15v25H45zM143 116h15v26h-15zM225 156h15v26h-15z"/>
                                    @break
                                @case('mountain')
                                    <path fill="#cedde4" d="M0 0h300v230H0z"/><circle fill="#f9efd4" cx="220" cy="54" r="26"/><path fill="#8a9ca6" d="m-30 230 130-190 130 190z"/><path fill="#e9eddf" d="m100 40-29 43 28-10 17 13 16-1z"/><path fill="#526f79" d="m100 230 126-147 120 147z"/><path fill="#a0bcc0" d="M0 190q100-20 190 30l110-22v32H0z"/>
                                    @break
                                @case('desert')
                                    <path fill="#e9c9b5" d="M0 0h300v230H0z"/><circle fill="#fff0cb" cx="206" cy="61" r="33"/><path fill="#d5ab82" d="M0 135q100-80 210 0t90 0v95H0z"/><path fill="#b97e5c" d="M0 194q130-130 300-10v46H0z"/><path fill="#956747" d="M0 230q130-68 300-20v20z"/>
                                    @break
                                @default
                                    <path fill="#cbded8" d="M0 0h300v230H0z"/><circle fill="#fff0cb" cx="216" cy="58" r="31"/><path fill="#86aaa4" d="M0 124q82-14 157 13t143-6v99H0z"/><path fill="#dfcda8" d="M0 151q80 7 148 52t152 8v19H0z"/><path fill="#558c87" d="M0 180q68-4 130 43l25 7H0z"/><path fill="none" stroke="#eef4e7" stroke-width="2" opacity=".6" d="M30 147h58m79 13h75m-100 24h45"/>
                            @endswitch
                        </svg>
                    @endif --}}
                    {{-- <span class="home-tag">{{ $event['tag'] ?? 'À découvrir' }}</span> --}}
                    {{-- <span class="home-country">{{ $event['country'] ?? 'Destination à venir' }}</span> --}}
                </div>
                {{-- <div class="home-card-body">
                    <p class="home-meta">{{ $event['dates'] ?? 'Dates à venir' }} <span>· {{ $event['places'] ?? 'Places à confirmer' }}</span></p>
                    <h3>{{ $event['title'] ?? 'Une nouvelle escapade' }}</h3>
                    <p class="home-description">{{ $event['description'] ?? 'Les détails de ce voyage arrivent bientôt.' }}</p>
                    <div class="home-card-bottom"><span>@if(isset($event['price']))<strong>{{ $event['price'] }} €</strong> / pers.@else<strong>Budget à venir</strong>@endif</span> --}}
                        {{-- TODO PHP: provide a real URL for the trip details page. --}}
                        {{-- @if(!empty($event['url']))
                            <a class="home-card-link" href="{{ $event['url'] }}" aria-label="Découvrir {{ $event['title'] ?? 'le voyage' }}">↗</a>
                        @else
                            <span class="home-card-link" aria-hidden="true">↗</span>
                        @endif --}}
                    {{-- </div>
                </div> --}}
            {{-- </article> --}}
            @endforeach
        </div>
        <div id="home-empty" class="home-empty" @if(count($events) > 0) hidden @endif><h3>Aucun départ à l’horizon… pour l’instant.</h3><p>Essayez une autre recherche, ou revenez découvrir les prochains voyages.</p></div>
    </section>

    <aside class="home-create"><div><p class="eyebrow">ET SI ON SUIVAIT VOTRE ENVIE ?</p><h2>Le prochain départ, c’est peut-être le vôtre.</h2><p>Une destination en tête ? Faites-en une aventure à partager.</p></div>
        {{-- TODO PHP: provide $createTripUrl once the creation route is available. --}}
        @if(!empty($createTripUrl))<a class="button button-primary" href="{{ $createTripUrl }}">Proposer un voyage <span aria-hidden="true">＋</span></a>
        @else<button class="button button-primary" type="button" disabled title="La création de voyage sera bientôt disponible">Proposer un voyage <span aria-hidden="true">＋</span></button>@endif
    </aside>
</div>
@endsection

@push('styles')
<style>
/* Home page styles, scoped to this view only. */
.home-page [hidden]{display:none!important}.home-sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.home-hero{display:flex;justify-content:space-between;align-items:center;gap:30px;margin-bottom:38px}.home-hero .eyebrow{margin:0 0 18px}.home-hero h1{margin:0;font-size:clamp(36px,4.4vw,60px);font-weight:500;line-height:1.12;letter-spacing:-2px}.home-hero em{font-family:Georgia,serif;font-weight:400;color:var(--p-accent)}.home-intro{margin:20px 0 0;font-size:15px;color:var(--p-muted)}.home-stamp{flex-shrink:0;width:148px;height:148px;border:1px solid var(--p-line);border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:var(--p-warm);transform:rotate(12deg);margin-right:25px}.home-stamp span{font-size:8px;letter-spacing:2px}.home-stamp strong{font-size:65px;line-height:1.25;font-weight:400}
.home-searchbar{display:flex;gap:16px;margin-bottom:38px}.home-search,.home-filter{display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--p-surface);border:1px solid var(--p-line);border-radius:14px}.home-search{flex:1}.home-search svg{width:22px;height:22px;flex-shrink:0;color:var(--p-muted)}.home-search input{width:100%;min-width:0;background:transparent;border:0;color:var(--p-ink);font-size:14px}.home-search input::placeholder{color:var(--p-muted)}.home-filter{min-width:270px;justify-content:space-between}.home-filter>span{font-size:11px;color:var(--p-muted)}.home-filter select{background:var(--p-surface);color:var(--p-ink);border:0;font-size:12px;cursor:pointer}
.home-section-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:20px}.home-section-heading h2,.home-create h2{margin:0;font-size:22px;font-weight:500;letter-spacing:-.6px}.home-section-heading>span{font-size:12px;color:var(--p-muted)}.home-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px}.home-card{min-width:0;background:var(--p-surface);border:1px solid var(--p-line);border-radius:19px;overflow:hidden;transition:transform .2s,box-shadow .2s}.home-card:hover{transform:translateY(-4px);box-shadow:0 12px 28px #0000000a}.home-card-image{position:relative;margin:8px;border-radius:12px;overflow:hidden;aspect-ratio:1.25;background:var(--p-soft)}.home-card-image::after{content:'';position:absolute;inset:50% 0 0;background:linear-gradient(transparent,#172f3660);pointer-events:none}.home-card-image>svg,.home-card-image>img{width:100%;height:100%;display:block;object-fit:cover}.home-tag{position:absolute;left:13px;top:13px;font-size:10px;border-radius:20px;padding:4px 10px;background:#fffefaef;color:#344c3e}.home-country{position:absolute;bottom:16px;left:17px;z-index:1;color:#fffefa;font-family:Georgia,serif;font-size:27px;letter-spacing:-.5px}.home-card-body{padding:12px 19px 18px}.home-meta{font-size:10px;color:var(--p-muted);margin:0 0 10px}.home-meta span{white-space:nowrap}.home-card h3{margin:0;font-size:18px;font-weight:600;line-height:1.35;letter-spacing:-.4px}.home-description{font-size:12px;line-height:1.7;color:var(--p-muted);margin:10px 0 0;min-height:62px}.home-card-bottom{display:flex;justify-content:space-between;align-items:center;gap:8px;border-top:1px solid var(--p-line);padding-top:16px;margin-top:18px}.home-card-bottom>span{font-size:10px;color:var(--p-muted)}.home-card-bottom strong{font-size:19px;color:var(--p-ink);font-weight:600}.home-card-link{width:33px;height:33px;display:grid;place-items:center;background:var(--p-soft);border-radius:50%;font-size:21px;flex-shrink:0}.home-card-link[href]:hover{background:var(--p-accent);color:var(--p-on-accent)}.home-create{display:flex;justify-content:space-between;align-items:center;gap:26px;background:var(--p-soft);border-radius:18px;padding:30px 34px;margin-top:42px}.home-create .eyebrow{margin:0 0 8px}.home-create>div>p:last-child{font-size:12px;color:var(--p-muted);margin:9px 0 0}.home-create .button{flex-shrink:0}.home-create button:disabled{opacity:.7;cursor:not-allowed}.home-empty{text-align:center;padding:45px 20px;border:1px dashed var(--p-line);border-radius:16px;color:var(--p-muted)}.home-empty h3{font-size:18px;color:var(--p-ink);margin:0 0 10px}.home-empty p{font-size:13px;margin:0}
@media(max-width:1050px){.home-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.home-card-image{aspect-ratio:1.6}.home-description{min-height:42px}.home-stamp{width:125px;height:125px;margin-right:0}.home-stamp span{font-size:7px}.home-create{align-items:flex-start}.home-filter{min-width:245px}}
@media(max-width:700px){.home-hero h1{font-size:38px;letter-spacing:-1.5px}.home-stamp{display:none}.home-intro{font-size:13px}.home-searchbar{flex-direction:column;gap:10px;margin-bottom:28px}.home-filter{min-width:0}.home-section-heading{align-items:flex-start}.home-section-heading h2{font-size:19px;max-width:240px}.home-section-heading>span{font-size:10px;white-space:nowrap}.home-grid{gap:12px}.home-card-image{aspect-ratio:1.05}.home-card-body{padding:10px 13px 14px}.home-card h3{font-size:16px}.home-meta span{display:block}.home-description{font-size:11px;min-height:76px}.home-card-bottom strong{font-size:17px}.home-country{font-size:23px;left:13px}.home-tag{left:10px;top:10px}.home-create{flex-direction:column;padding:25px}.home-create h2{font-size:21px}}
@media(max-width:400px){.home-grid{grid-template-columns:1fr}.home-card-image{aspect-ratio:1.5}.home-description{min-height:0}.home-meta span{display:inline}.home-section-heading{flex-direction:column;gap:8px}}
@media(prefers-reduced-motion:reduce){.home-card{transition:none}}
</style>
@endpush

@push('scripts')
<script>
// Filter demo cards without requests or authentication logic.
(() => {
    const search = document.getElementById('home-search');
    const filter = document.getElementById('home-filter');
    const cards = [...document.querySelectorAll('[data-home-card]')];
    const normalize = value => value.toLocaleLowerCase('fr').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    function update() {
        const query = normalize(search.value.trim());
        let count = 0;
        cards.forEach(card => {
            const visible = normalize(card.textContent).includes(query) && (!filter.value || card.dataset.tag === filter.value);
            card.hidden = !visible;
            if (visible) count++;
        });
        document.getElementById('home-count').textContent = `${count} ${count === 1 ? 'voyage à explorer' : 'voyages à explorer'}`;
        document.getElementById('home-empty').hidden = count !== 0;
    }
    search.addEventListener('input', update);
    filter.addEventListener('change', update);
    update();
})();
</script>
@endpush