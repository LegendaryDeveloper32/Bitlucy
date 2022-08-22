<div class="nav">
    <div class="container-fluid">
        <div class="action" onclick="redirect('/')" data-page-trigger="'/'" data-toggle-class="active">
            <i class="fas fa-spade"></i> {{ __('general.head.games') }}
        </div>
        <div class="action" onclick="@if(auth()->guest()) $.auth(); @else redirect('/invest'); @endif" data-page-trigger="'/invest'" data-toggle-class="active">
            <i class="fab fa-bitcoin"></i> {{ __('general.head.invest') }}
        </div>
        <div class="action" onclick="redirect('/fairness')" data-page-trigger="'/fairness'" data-toggle-class="active">
            <i class="fas fa-check"></i> {{ __('general.footer.fairness') }}
        </div>
    </div>
</div>
