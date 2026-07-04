<form method="POST" action="{{ route('locale.update') }}" class="locale-form">
    @csrf
    <select name="locale" class="locale-select" onchange="this.form.submit()" title="{{ __('app.language') }}">
        @foreach (['fr' => 'Français', 'en' => 'English', 'es' => 'Español', 'de' => 'Deutsch'] as $code => $label)
            <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $label }}</option>
        @endforeach
    </select>
</form>
