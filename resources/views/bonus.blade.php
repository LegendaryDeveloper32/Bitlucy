<div class="container-fluid">
    <div class="row">
        @if(!auth()->guest() && auth()->user()->vipLevel() >= 1)
            <div class="col-12 d-md-none">
                <div class="banner banner-vip-bonus" onclick="$.vipBonus()">
                    <div class="name">
                        {!! __('bonus.vip.description') !!}
                    </div>
                </div>
            </div>
        @endif
        <div class="col wheel-column">
            <div class="banner banner-wheel" data-toggle-bonus-sidebar="wheel">
                <div class="name">
                    {!! __('bonus.wheel.description') !!}
                </div>
            </div>
            <div class="wheel-popup" style="display: none">
                {!! __('bonus.wheel.prompt') !!}
            </div>
        </div>
        <div class="col d-flex b">
            <div class="banner banner-w50 banner-promo" data-toggle-bonus-sidebar="promo">
                <div class="name">
                    {!! __('bonus.promo.description') !!}
                </div>
            </div>
            <div class="banner banner-w50 banner-partner" @if(auth()->guest() || count(auth()->user()->referral_wager_obtainer ?? []) < 10) onclick="redirect('/partner')" @else data-toggle-bonus-sidebar="partner" @endif>
                <div class="name">
                    {!! __('bonus.partner.description') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col sm-column @if(!(auth()->guest() || auth()->user()->vipLevel() == 0)) d-none d-md-block @endif">
            <div class="banner banner-vip-bonus" onclick="{{ (auth()->guest() || auth()->user()->vipLevel() == 0) ? "$.vip()" : "$.vipBonus()" }}">
                <div class="name">
                    {!! auth()->guest() || auth()->user()->vipLevel() == 0 ? __('bonus.vip.unavailable_description') : __('bonus.vip.description') !!}
                </div>
            </div>
        </div>
        <div class="col sm-column">
            <div class="banner banner-rain" data-toggle-bonus-sidebar="rain">
                <div class="name">
                     {!! __('bonus.rain.description') !!}
                </div>
            </div>
        </div>
        <div class="col sm-column">
            <div class="banner banner-discord" @if(!(!auth()->guest() && auth()->user()->discord_bonus) || auth()->guest()) data-toggle-bonus-sidebar="discord" @endif>
                @if(!auth()->guest() && auth()->user()->discord_bonus)
                    <div class="unavailable">
                        <div class="slanting">
                            <div class="content">
                                {{ __('general.obtained') }}
                            </div>
                        </div>
                    </div>
                @endif
                <i class="fab fa-discord"></i>
                <div class="name">
                    {!! __('bonus.discord.description') !!}
                </div>
            </div>
        </div>
        <div class="col">
            <div class="banner banner-notifications">
                <div class="unavailable">
                    <div class="slanting">
                        <div class="content">
                            {{ __('general.not_available') }}
                        </div>
                    </div>
                </div>
                <i class="fad fa-bells"></i>
                <div class="name">
                    {!! __('bonus.notifications.description') !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="bonus-overlay" style="display: none"></div>
<div class="bonus-side-menu"></div>
