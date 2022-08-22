<div class="container-fluid">
    <div class="slider">
        <div class="glide" id="slider">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <li class="glide__slide" style="background: #f20064">
                        <div class="image" style="background-image: url({{ asset('img/misc/slide_vip.png') }})"></div>
                        <div class="slideContent">
                            <div class="slideContentWrapper">
                                <div class="header">
                                    VIP
                                </div>
                                <div class="description">
                                    As a member of our VIP program, you are entitled for a weekly VIP bonus.
                                </div>
                                <div class="button" onclick="{{ auth()->guest() ? '$.auth()' : '$.vip()' }}">
                                    Learn more
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="glide__slide" style="background: #080549">
                        <div class="image" style="background-image: url({{ asset('img/misc/slide_invest.png') }})"></div>
                        <div class="slideContent">
                            <div class="slideContentWrapper">
                                <div class="header">
                                    Investments
                                </div>
                                <div class="description">
                                   We allows players to play just like other casinos, but also offers the unique feature allowing users to be "The Bank".
                                </div>
                                <div class="button" onclick="{{ auth()->guest() ? '$.auth()' : 'redirect(\'/invest\')' }}">
                                    Learn more
                                </div>
                            </div>
                        </div>
                    </li>
                    {{--<li class="glide__slide" style="background: linear-gradient(5.33deg,#825fde 13.99%,#313774 70.93%)">
                        <div class="slideContent">
                            <div class="slideContentWrapper">
                                <div class="header">
                                    Slide Header
                                </div>
                                <div class="description">
                                    Slide Description, more info, more info,more info,more info,more info,more info,more info,more info,more info,more info,
                                </div>
                                <div class="button">
                                    Button
                                </div>
                            </div>
                        </div>
                    </li>--}}
                </ul>
            </div>
            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left" data-glide-dir="<"></button>
                <button class="glide__arrow glide__arrow--right" data-glide-dir=">"></button>
            </div>
            <div class="glide__bullets" data-glide-el="controls[nav]">
                <button class="glide__bullet" data-glide-dir="=0"></button>
                <button class="glide__bullet" data-glide-dir="=1"></button>
            </div>
        </div>
    </div>
    @include('modals.nav')
    <div class="games">
        @foreach(\App\Games\Kernel\Game::list() as $game)
            <div class="game_poster game-{{ $game->metadata()->id() }}" @if(!$game->isDisabled()) onclick="redirect('/game/{{ $game->metadata()->id() }}')" @endif>
                @if($game->isDisabled())
                    <div class="unavailable">
                        <div class="slanting">
                            <div class="content">
                                {{ $game->metadata()->isPlaceholder() ? __('general.coming_soon') : __('general.not_available') }}
                            </div>
                        </div>
                    </div>
                @endif
                <div class="name">
                    {{ $game->metadata()->name() }}
                </div>
            </div>
        @endforeach
    </div>
</div>
