@extends('layouts.planora')

@section('title', 'Votre voyage — Planora')

@section('content')
{{-- The controller supplies $event with its location, organizer and tags relations.
     TODO PHP: enforce authorization on the server. --}}
@php
    $mode = in_array($mode ?? 'view', ['view', 'create', 'edit'], true) ? ($mode ?? 'view') : 'view';
@endphp
<div class="trip-page" data-trip-page data-mode="{{ $mode }}">
    <div class="trip-topline">
        {{-- TODO PHP: provide $backUrl once the home/listing page is implemented. --}}
        @if(!empty($backUrl))<a class="trip-back" href="{{ $backUrl }}">← Tous les voyages</a>@else<span class="trip-back">VOTRE PROCHAINE ESCALE</span>@endif
    </div>
    <header class="trip-heading">
        <div><p class="eyebrow" data-display="eyebrow">{{ $event->location?->name ?? 'Lieu à préciser' }} · {{ $event->tags->pluck('name')->join(', ') ?: 'Aucun tag' }}</p><h1 data-mode-title>{{ $mode === 'create' ? 'Et si vous donniez le départ ?' : ($event->title ?? 'Votre prochaine aventure') }}</h1><p class="trip-lead" data-mode-subtitle>{{ $mode === 'create' ? 'Quelques détails pour donner aux autres l’envie de vous suivre.' : ($mode === 'edit' ? 'Affinez les petits détails de votre prochaine escapade.' : 'Une parenthèse pour soi. Des souvenirs à plusieurs.') }}</p></div>
        <span class="trip-status" data-read>{{ $event->status ?? 'Statut à confirmer' }}</span>
    </header>
    @if (session('success'))
        <p class="trip-feedback" role="status">
            {{ session('success') }}
        </p>
    @endif

    @if ($errors->any())
        <div class="trip-feedback" role="alert">
            <p>Some fields must be corrected :</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form
        class="trip-layout"
        data-trip-form
        @if ($mode !== 'view')
            method="POST"
            action="{{ $mode === 'create'
                ? route('events.store')
                : route('events.update', $event) }}"
        @endif
    >
        @if ($mode !== 'view')
            @csrf

            @if ($mode === 'edit')
                @method('PATCH')
            @endif
        @endif
        <div class="trip-main">
            <section class="trip-gallery" aria-label="Aperçu du voyage">
                {{-- The gallery displays the event's single stored image URL. --}}
                <div class="trip-gallery-image" data-gallery-image>
                    <svg viewBox="0 0 800 440" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Illustration d’un paysage côtier"><path fill="#cbded8" d="M0 0h800v440H0z"/><circle fill="#fff0cb" cx="585" cy="110" r="64"/><path fill="#9db7a1" d="M0 215q190-115 360 0t440-10v235H0z"/><path fill="#77a4a0" d="M0 275q210-40 450 20t350-15v160H0z"/><path fill="#decaa6" d="M0 292q170 5 370 130l430-55v73H0z"/><path fill="#4c817e" d="M0 350q140-18 300 90H0z"/><path fill="none" stroke="#edf2e5" stroke-width="3" opacity=".65" d="M115 284h130m210 36h110m55-29h95"/></svg>
                </div>
                <span class="trip-gallery-caption" data-display="location">{{ $event->location?->name ?? 'Lieu à préciser' }}</span>
                <div class="trip-gallery-controls"><button type="button" data-gallery-prev aria-label="Photo précédente">←</button><span data-gallery-counter aria-live="polite">Illustration</span><button type="button" data-gallery-next aria-label="Photo suivante">→</button></div>
                <span class="trip-gallery-label">L’ENVIE DE PRENDRE LE LARGE</span>
            </section>
            <div class="trip-upload" data-write>
                {{-- TODO PHP: implement validated image uploads and decide whether
                     to support multiple images; Event currently stores one image_url. --}}
                <label for="trip-photos"><span aria-hidden="true">↥</span><strong>Ajoutez vos photos</strong><small>JPG ou PNG · aperçu local uniquement</small></label>
                <input id="trip-photos" type="file" accept="image/jpeg,image/png" multiple>
                <p class="trip-hint" data-photo-feedback role="status">Aucune photo sélectionnée.</p>
            </div>

            <section class="trip-story" data-read>
                <p class="eyebrow">L’ESPRIT DU VOYAGE</p><h2>Du temps pour soi.<br><em>Des souvenirs à plusieurs.</em></h2>
                <p class="trip-description" data-display="description">{{ $event->description ?? 'Le programme de ce voyage arrive bientôt.' }}</p>
                <dl class="trip-facts"><div><dt>Âge minimum</dt><dd data-display="min_age">{{ isset($event->min_age) ? $event->min_age.' ans' : 'À préciser' }}</dd></div><div><dt>Le groupe</dt><dd data-display="max_participants">{{ isset($event->max_participants) ? $event->max_participants.' voyageurs' : 'À préciser' }}</dd></div><div><dt>Les tags</dt><dd data-display="tags">{{ $event->tags->pluck('name')->join(', ') ?: 'Aucun tag' }}</dd></div></dl>
            </section>
            <section class="surface trip-fields" data-write aria-labelledby="trip-story-heading">
                <div class="trip-section-heading"><h2 id="trip-story-heading">01 · Racontez le voyage</h2><span>L’envie commence ici</span></div>
                <label class="form-label" for="trip-title">Nom du voyage <span aria-hidden="true">*</span></label><input class="form-input" id="trip-title" name="title" required maxlength="255" placeholder="Une semaine au bord de l’océan" value="{{ old('title', $event->title) }}">
                {{-- TODO PHP: supply all $locations and $tags choices from create/edit.
                     Without these lists, only the event's current relations are available.
                     Validate and save the selected tag associations when connecting persistence. --}}
                <div class="trip-columns">
                    <div>
                        <label class="form-label" for="trip-location">Lieu <span aria-hidden="true">*</span></label>
                        <select class="form-input" id="trip-location" name="location_id" required>
                            <option value="">Choisir un lieu</option>
                            @foreach(($locations ?? collect([$event->location])->filter()) as $location)
                                <option value="{{ $location->id }}" @selected((string) old('location_id', $event->location_id) === (string) $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="trip-tags">Tags</label>
                        <select class="form-input" id="trip-tags" name="tags[]" multiple>
                            @foreach(($tags ?? $event->tags) as $tag)
                                <option value="{{ $tag->id }}" @selected(in_array($tag->id, (array) old('tags', $event->tags->modelKeys())))>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label class="form-label" for="trip-description">Votre idée, en quelques mots <span aria-hidden="true">*</span></label><textarea class="form-input" id="trip-description" name="description" rows="6" required placeholder="Le programme, les envies, ce que vous aimeriez partager…">{{ old('description', $event->description) }}</textarea><p class="trip-hint">Les champs marqués d’un * sont obligatoires.</p>
            </section>
        </div>
        <aside class="trip-sidebar">
            <section class="surface trip-booking" data-read aria-label="Informations et inscription">
                <p class="eyebrow">VOTRE PLACE AU SOLEIL</p><p class="trip-price"><span data-display="price">{{ $event->price ?? '—' }}</span> € <small>/ personne</small></p><p class="trip-hint">Budget indicatif pour le séjour</p>
                {{-- TODO PHP: define which registration statuses consume a place,
                     then calculate the remaining capacity. --}}
                <dl class="trip-booking-facts"><div><dt>Début</dt><dd data-display="starts_at">{{ !empty($event->starts_at) ? \Carbon\Carbon::parse($event->starts_at)->format('d/m/Y H:i') : 'À préciser' }}</dd></div><div><dt>Fin</dt><dd data-display="ends_at">{{ !empty($event->ends_at) ? \Carbon\Carbon::parse($event->ends_at)->format('d/m/Y H:i') : 'À préciser' }}</dd></div><div><dt>Disponibilités</dt><dd>À confirmer</dd></div></dl>
                {{-- TODO PHP: connect registration and check availability and permissions on the server. --}}
                <button class="button button-primary trip-full" type="button" data-register>Ça me tente ! <span aria-hidden="true">↗</span></button><p class="trip-hint trip-center">Un premier échange, puis on prépare le départ ensemble.</p>
                <div class="trip-organizer"><span class="avatar" aria-hidden="true">{{ mb_substr($event->organizer?->email ?? 'P', 0, 1) }}</span><div><small>Votre compagnon de départ</small><strong>{{ $event->organizer?->email ?? 'Organisateur à préciser' }}</strong></div></div><p class="trip-contact">Contact : <span data-display="contact">{{ $event->organizer?->email ?? 'Contact à préciser' }}</span></p>
                {{-- TODO PHP: provide $createTripUrl once events.create renders the creation form. --}}
                @if(!empty($createTripUrl))<a href="{{ $createTripUrl }}" class="button button-secondary trip-full">＋ Proposer mon voyage</a>@endif
            </section>
            <section class="surface trip-practical" data-write aria-labelledby="trip-practical-heading">
                <h2 id="trip-practical-heading">02 · Les petits détails</h2>
                <label class="form-label" for="trip-starts-at">Date et heure de début <span aria-hidden="true">*</span></label><input class="form-input" id="trip-starts-at" type="datetime-local" name="starts_at" required value="{{ old('starts_at', !empty($event->starts_at) ? \Carbon\Carbon::parse($event->starts_at)->format('Y-m-d\TH:i') : '') }}">
                <label class="form-label" for="trip-ends-at">Date et heure de fin <span aria-hidden="true">*</span></label><input class="form-input" id="trip-ends-at" type="datetime-local" name="ends_at" required value="{{ old('ends_at', !empty($event->ends_at) ? \Carbon\Carbon::parse($event->ends_at)->format('Y-m-d\TH:i') : '') }}">
                <div class="trip-columns"><div><label class="form-label" for="trip-capacity">Places <span aria-hidden="true">*</span></label><input class="form-input" id="trip-capacity" name="max_participants" type="number" min="1" step="1" required placeholder="6" value="{{ old('max_participants', $event->max_participants) }}"></div><div><label class="form-label" for="trip-price">Budget / pers. (€) <span aria-hidden="true">*</span></label><input class="form-input" id="trip-price" name="price" type="number" min="0" max="999999.99" step="0.01" required placeholder="480" value="{{ old('price', $event->price ?? 0) }}"></div></div>
                <label class="form-label" for="trip-age">Âge minimum <span aria-hidden="true">*</span></label><input class="form-input" id="trip-age" name="min_age" type="number" min="0" step="1" required placeholder="18" value="{{ old('min_age', $event->min_age ?? 0) }}">
                <label class="form-label" for="trip-contact">Contact de l’organisateur</label><input class="form-input" id="trip-contact" type="email" readonly value="{{ $event->organizer?->email ?? '' }}" placeholder="Contact à préciser">
                <button class="button button-primary trip-full" type="submit"><span data-submit-label>{{ $mode === 'create' ? 'Proposer le voyage' : 'Enregistrer les modifications' }}</span><span aria-hidden="true">↗</span></button>
                <p class="trip-hint trip-center">Les petits détails font les beaux voyages.</p>
            </section>
        </aside>
        <p class="trip-feedback" data-feedback role="status" hidden></p>
    </form>
</div>
@endsection

@push('styles')
<style>
/* Page-specific styles stay in this view and inherit the shared theme tokens. */
.trip-page [hidden]{display:none!important}.trip-page[data-mode=view] [data-write]{display:none}.trip-page:not([data-mode=view]) [data-read]{display:none}.trip-topline{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:34px}.trip-back{font-size:11px;color:var(--p-muted)!important;letter-spacing:.5px}.trip-mode-switch{border:0;padding:0;margin:0;display:flex;gap:3px;background:var(--p-soft);border-radius:10px}.trip-mode-switch legend{font-size:9px;color:var(--p-muted);padding:0 0 5px}.trip-mode-switch label{position:relative;cursor:pointer}.trip-mode-switch input{position:absolute;opacity:0;width:100%;height:100%;margin:0}.trip-mode-switch span{display:block;padding:7px 13px;font-size:11px;border-radius:8px;border:1px solid transparent}.trip-mode-switch input:checked+span{background:var(--p-surface);border-color:var(--p-line)}.trip-mode-switch input:focus-visible+span{outline:3px solid var(--p-accent);outline-offset:2px}.trip-heading{display:flex;align-items:center;justify-content:space-between;gap:25px;margin-bottom:32px}.trip-heading .eyebrow{margin:0 0 12px;text-transform:uppercase}.trip-heading h1{margin:0;font-size:clamp(32px,3.7vw,49px);line-height:1.15;font-weight:500;letter-spacing:-1.7px}.trip-lead{font-size:14px;color:var(--p-muted);margin:17px 0 0}.trip-status{font-size:11px;white-space:nowrap;background:var(--p-soft);padding:8px 14px;border-radius:30px}.trip-layout{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:32px;align-items:start}.trip-main{min-width:0}.trip-gallery{position:relative;overflow:hidden;border-radius:20px;background:var(--p-soft)}.trip-gallery-image{height:375px}.trip-gallery-image svg,.trip-gallery-image img{display:block;width:100%;height:100%;object-fit:cover}.trip-gallery::after{content:'';position:absolute;inset:55% 0 0;background:linear-gradient(transparent,#18312f80);pointer-events:none}.trip-gallery-caption{position:absolute;bottom:32px;left:32px;color:#fffefa;font:42px Georgia,serif;z-index:1}.trip-gallery-label{position:absolute;top:22px;left:25px;color:#25392f;letter-spacing:2px;font-size:9px;background:#fffefaab;border-radius:20px;padding:6px 11px}.trip-gallery-controls{position:absolute;bottom:27px;right:24px;display:flex;align-items:center;gap:10px;z-index:1}.trip-gallery-controls button{border:0;cursor:pointer;width:33px;height:33px;border-radius:50%;background:#fffefa;color:#25392f;font-size:17px}.trip-gallery-controls button:disabled{opacity:.45;cursor:default}.trip-gallery-controls span{font-size:10px;color:#fffefa}.trip-story{padding:32px 0}.trip-story .eyebrow{margin:0 0 15px}.trip-story h2{margin:0;font-size:30px;line-height:1.25;font-weight:500;letter-spacing:-.7px}.trip-story em{font-family:Georgia,serif;font-weight:400}.trip-description{font-size:14px;line-height:1.9;color:var(--p-muted);margin:19px 0 0;white-space:pre-line}.trip-facts{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin:28px 0 0;padding-top:23px;border-top:1px solid var(--p-line)}.trip-facts dt{font-size:11px;color:var(--p-muted);margin-bottom:6px}.trip-facts dd{margin:0;font-size:13px;font-weight:600}.trip-sidebar{position:sticky;top:24px}.trip-booking .eyebrow{margin:0 0 12px}.trip-price{font-size:40px;letter-spacing:-1px;margin:0;line-height:1.2}.trip-price small{font-size:12px;letter-spacing:0;color:var(--p-muted)}.trip-hint{font-size:11px;color:var(--p-muted);margin:12px 0 0;line-height:1.7}.trip-center{text-align:center}.trip-booking-facts{margin:24px 0}.trip-booking-facts>div{display:flex;justify-content:space-between;gap:12px;border-bottom:1px solid var(--p-line);padding:13px 0;font-size:12px}.trip-booking-facts dt{color:var(--p-muted)}.trip-booking-facts dd{margin:0;font-weight:600}.trip-full{width:100%}.trip-organizer{display:flex;align-items:center;gap:12px;border-top:1px solid var(--p-line);padding-top:23px;margin-top:25px}.trip-organizer small,.trip-organizer strong{display:block}.trip-organizer small{font-size:10px;color:var(--p-muted)}.trip-organizer strong{font-size:13px}.trip-contact{font-size:11px;color:var(--p-muted);margin:14px 0 0;overflow-wrap:anywhere}.trip-booking .button-secondary{margin-top:20px}.trip-fields{margin-top:24px}.trip-fields h2,.trip-practical h2{font-size:20px;font-weight:500;letter-spacing:-.5px;margin:0}.trip-section-heading{display:flex;justify-content:space-between;align-items:center;gap:12px}.trip-section-heading>span{font-size:10px;color:var(--p-muted)}.trip-columns{display:grid;grid-template-columns:1fr 1fr;gap:14px}.trip-fields textarea{resize:vertical;min-height:150px}.trip-practical .form-input,.trip-fields .form-input{font-size:13px}.trip-practical .button{margin-top:25px}.trip-upload{border:1px dashed var(--p-line);border-radius:14px;padding:17px 22px;margin-top:14px;background:var(--p-surface)}.trip-upload label{display:flex;align-items:center;gap:12px;flex-wrap:wrap;cursor:pointer;margin-bottom:12px}.trip-upload label>span{font-size:26px;color:var(--p-accent)}.trip-upload strong{font-size:13px}.trip-upload small{font-size:10px;color:var(--p-muted)}.trip-upload input{max-width:100%;font-size:11px}.trip-feedback{grid-column:1/-1;margin:0;border:1px solid var(--p-line);border-radius:12px;background:var(--p-soft);padding:16px 20px;font-size:13px;color:var(--p-ink)}
@media(max-width:1000px){.trip-layout{grid-template-columns:minmax(0,1fr) 300px;gap:22px}.trip-gallery-image{height:320px}.trip-section-heading{align-items:flex-start;flex-direction:column}.trip-sidebar .surface{padding:23px}.trip-heading h1{font-size:38px}}
@media(max-width:760px){.trip-topline{align-items:flex-start;flex-direction:column;gap:18px;margin-bottom:25px}.trip-heading{align-items:flex-start;flex-direction:column;gap:18px}.trip-heading h1{font-size:35px}.trip-lead{font-size:13px}.trip-layout{grid-template-columns:1fr;gap:22px}.trip-sidebar{position:static}.trip-gallery-image{height:300px}.trip-gallery-caption{font-size:33px;left:23px;bottom:30px}.trip-gallery-controls{right:16px;gap:7px}.trip-gallery-controls span{font-size:9px}.trip-facts{gap:12px}.trip-facts dd{font-size:12px}.trip-story{padding-bottom:0}.trip-section-heading{flex-direction:row}.trip-section-heading>span{display:none}}
@media(max-width:400px){.trip-columns{grid-template-columns:1fr}.trip-gallery-image{height:270px}.trip-gallery-controls{bottom:14px}.trip-gallery-caption{bottom:60px}.trip-facts{grid-template-columns:1fr;gap:16px}.trip-mode-switch span{padding:7px 11px}}
</style>
@endpush

@push('scripts')
<script>
// The controller selects the mode; valid forms submit to Laravel.
(() => {
    const page = document.querySelector('[data-trip-page]');
    const form = page.querySelector('[data-trip-form]');
    const mode = page.dataset.mode;
    const feedback = page.querySelector('[data-feedback]');
    const showFeedback = text => { feedback.textContent = text; feedback.hidden = false; };
    form.querySelectorAll('input, select, textarea').forEach(field => { field.disabled = mode === 'view'; });
    const startsAt = form.elements.starts_at;
    const endsAt = form.elements.ends_at;
    const validateDates = () => {
        endsAt.setCustomValidity(endsAt.value && startsAt.value && endsAt.value <= startsAt.value ? 'La fin doit être prévue après le début.' : '');
    };
    startsAt.addEventListener('input', validateDates);
    endsAt.addEventListener('input', validateDates);
    validateDates();
    form.addEventListener('submit', event => {
        validateDates();

        if (mode === 'view' || !form.reportValidity()) {
            event.preventDefault();
        }
    });
    page.querySelector('[data-register]').addEventListener('click', () => showFeedback('Aperçu uniquement : l’inscription à ce voyage reste à brancher.'));
    const imageContainer = page.querySelector('[data-gallery-image]');
    const illustration = imageContainer.innerHTML;
    // Local file selections remain previews and are never uploaded.
    const imageUrl = @json($event->image_url);
    const serverPhotos = imageUrl ? [imageUrl] : [];
    let localPhotos = [];
    let photoIndex = 0;
    const previous = page.querySelector('[data-gallery-prev]');
    const next = page.querySelector('[data-gallery-next]');
    function syncGallery() {
        const photos = mode !== 'view' && localPhotos.length ? localPhotos : serverPhotos;
        photoIndex = photos.length ? photoIndex % photos.length : 0;
        if (photos.length) {
            const image = document.createElement('img');
            image.src = photos[photoIndex];
            image.alt = `Photo ${photoIndex + 1} du voyage`;
            imageContainer.replaceChildren(image);
        } else imageContainer.innerHTML = illustration;
        page.querySelector('[data-gallery-counter]').textContent = photos.length ? `${photoIndex + 1} / ${photos.length}` : 'Illustration';
        previous.disabled = next.disabled = photos.length < 2;
    }
    previous.addEventListener('click', () => { const length = (mode !== 'view' && localPhotos.length ? localPhotos : serverPhotos).length; photoIndex = (photoIndex - 1 + length) % length; syncGallery(); });
    next.addEventListener('click', () => { photoIndex++; syncGallery(); });
    page.querySelector('#trip-photos').addEventListener('change', event => {
        localPhotos.forEach(url => URL.revokeObjectURL(url));
        const selected = [...event.target.files].filter(file => ['image/jpeg', 'image/png'].includes(file.type));
        localPhotos = selected.map(file => URL.createObjectURL(file));
        photoIndex = 0;
        page.querySelector('[data-photo-feedback]').textContent = selected.length ? `${selected.length} photo(s) en aperçu. Aucun fichier envoyé.` : 'Aucune photo JPG ou PNG sélectionnée.';
        syncGallery();
    });
    window.addEventListener('pagehide', () => localPhotos.forEach(url => URL.revokeObjectURL(url)));
    syncGallery();
})();
</script>
@endpush
