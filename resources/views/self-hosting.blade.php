@extends('layouts.marketing', ['title' => __('app.selfhost_title').' — BloomFeed'])

@section('content')
<section class="docs-shell">
    <h1>{{ __('app.selfhost_title') }}</h1>
    <p class="hero-lede">{{ __('app.selfhost_intro') }}</p>

    <div class="card mt-3">
        <h2>{{ __('app.prereq_title') }}</h2>
        <ul class="docs-list">
            <li>{{ __('app.prereq_docker') }}</li>
            <li>{{ __('app.prereq_port') }}</li>
            <li>{{ __('app.prereq_proxy') }}</li>
        </ul>
    </div>

    <div class="card mt-2">
        <h2>{{ __('app.install_title') }}</h2>
        <ol class="docs-list docs-list-numbered">
            <li>
                {{ __('app.install_step_clone') }}
                <pre class="code-block">git clone https://git.eldorianet.work/esteban/BloomFeed-RSS bloomfeed
cd bloomfeed</pre>
            </li>
            <li>
                {{ __('app.install_step_script') }}
                <pre class="code-block">bash scriptsite.sh</pre>
                {{ __('app.install_env_note') }}
            </li>
            <li>{{ __('app.install_step_containers') }}</li>
            <li>{{ __('app.install_step_owner') }}</li>
        </ol>
    </div>

    <div class="card mt-2">
        <h2>{{ __('app.config_title') }}</h2>
        <ul class="docs-list">
            <li>{{ __('app.config_registrations') }}</li>
            <li>{{ __('app.config_language') }}</li>
            <li>{{ __('app.config_catalog') }}</li>
        </ul>
    </div>

    <div class="card mt-2">
        <h2>{{ __('app.update_title') }}</h2>
        <p>{{ __('app.update_text') }}</p>
        <pre class="code-block">bash scriptsite.sh</pre>
    </div>

    <div class="card mt-2">
        <h2>{{ __('app.uninstall_title') }}</h2>
        <p>{{ __('app.uninstall_text') }}</p>
        <pre class="code-block">bash uninstall.sh</pre>
    </div>

    <div class="card mt-2">
        <h2>{{ __('app.source_title') }}</h2>
        <p>
            <a href="https://git.eldorianet.work/esteban/BloomFeed-RSS" target="_blank" rel="noopener">git.eldorianet.work/esteban/BloomFeed-RSS</a>
        </p>
    </div>
</section>
@endsection
