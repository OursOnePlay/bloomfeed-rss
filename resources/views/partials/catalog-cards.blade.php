            @foreach ($feeds as $item)
                    <div class="market-card" data-category="{{ $item['category'] }}" data-lang="{{ $item['lang'] }}" data-search="{{ Str::lower($item['title'].' '.$item['description'].' '.$item['category']) }}">
                        <div class="market-card-head">
                            <span class="market-card-title">{{ $item['title'] }}</span>
                            <span class="chip chip-static">{{ $item['category'] }}</span>
                        </div>
                        <p class="market-card-desc">{{ $item['description'] ?: $item['url'] }}</p>
                        <div class="market-card-foot">
                            <span class="market-lang">{{ strtoupper($item['lang']) }}</span>
                            @if (in_array($item['url'], $subscribedUrls))
                                <span class="market-added">✓ {{ __('app.added') }}</span>
                            @else
                                <form method="POST" action="{{ route('feeds.store') }}">
                                    @csrf
                                    <input type="hidden" name="url" value="{{ $item['url'] }}">
                                    <input type="hidden" name="title" value="{{ $item['title'] }}">
                                    <button type="submit" class="btn btn-primary btn-sm">+ {{ __('app.add') }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
            @endforeach
