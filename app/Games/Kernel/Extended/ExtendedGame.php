<?php namespace App\Games\Kernel\Extended;

use App\Currency\Currency;
use App\Events\BalanceModification;
use App\Games\Kernel\Data;
use App\Games\Kernel\Game;
use App\Games\Kernel\ProvablyFairResult;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\DB;

abstract class ExtendedGame extends Game {

    public abstract function start(\App\Game $game);

    public abstract function turn(\App\Game $game, array $turnData): Turn;

    abstract function isLoss(ProvablyFairResult $result, \App\Game $game, array $turnData): bool;

    public function process(Data $data) {
        if(!$this->acceptsDemo() && ($data->guest() || $data->demo())) return ['error' => [-7, 'This game does not accept any demo bets']];
        if(!$this->acceptBet($data)) return ['error' => [-6, 'Game won\'t accept any bets right now']];

        if(!$data->guest()) $data->user()->balance(Currency::find($data->currency()))->demo($data->demo())->subtract($data->bet(), Transaction::builder()->game($data->id())->message('Game')->get());

        $game = \App\Game::create([
            'id' => DB::table('games')->count() + 1,
            'user' => $data->guest() ? null : $data->user()->_id,
            'game' => $this->metadata()->id(),
            'wager' => $data->bet(),
            'status' => 'in-progress',
            'profit' => 0,
            'server_seed' => $this->server_seed(),
            'client_seed' => $this->client_seed(),
            'nonce' => $this->nonce(),
            'multiplier' => 0,
            'currency' => $data->currency(),
            'data' => [
                'turn' => 0,
                'history' => [],
                'game_data' => [],
                'user_data' => $data->toArray()
            ],
            'demo' => $data->demo(),
            'type' => 'extended'
        ]);

        $this->start($game);

        return ['response' => ['id' => $game->_id, 'wager' => $data->bet()]];
    }

    public function finish(\App\Game $game) {
        $game->update([
            'profit' => $game->status === 'lose' ? 0 : $game->wager * $game->multiplier,
            'status' => $game->status === 'in-progress' ? 'win' : $game->status
        ]);

        if($game->user != null) {
            $currency = Currency::find($game->currency);
            $user = User::where('_id', $game->user)->first();

            if($this->getTurn($game) == 0) {
                $this->handleCancellation($game);
            } else {
                if ($game->profit == 0) event(new BalanceModification($user, $currency, 'subtract', $game->demo, $game->wager, 0));
                else {
                    auth()->user()->balance($currency)->demo($game->demo)->quiet()->add($game->profit, Transaction::builder()->game($game->game)->message('Win')->get());
                    event(new BalanceModification(auth()->user(), $currency, 'add', $game->demo, $game->profit, 0));
                }
                if (!$game->demo) event(new \App\Events\LiveFeedGame($game, 0));

                if(!$game->demo && auth()->user()->vipLevel() > 0 && (auth()->user()->weekly_bonus ?? 0) < 100)
                    auth()->user()->update([
                        'weekly_bonus' => (auth()->user()->weekly_bonus ?? 0) + 0.1
                    ]);
            }
        }
    }

    protected function handleCancellation(\App\Game $game) {
        $game->update([
            'status' => 'cancelled'
        ]);

        $user = User::where('_id', $game->user)->first();
        if($user != null) $user->balance(Currency::find($game->currency))->demo($game->demo)->add($game->wager, Transaction::builder()->game($game->game)->message('Cancellation')->get());
    }

    protected function acceptBet(Data $data) {
        return true;
    }

    protected function acceptsDemo() {
        return true;
    }

    public function inHistory(\App\Game $game, $validate) {
        return in_array($validate, $game->data['history']);
    }

    public function userData(\App\Game $game) {
        return $game->data['user_data'];
    }

    public function gameData(\App\Game $game) {
        return $game->data['game_data'];
    }

    public function pushData(\App\Game $game, array $value) {
        $data = $game->data['game_data'];
        $data = array_merge($data, $value);
        $game->update([
            'data' => [
                'turn' => $game->data['turn'],
                'history' => $game->data['history'],
                'game_data' => $data,
                'user_data' => $game->data['user_data']
            ]
        ]);
    }

    public function pushHistory(\App\Game $game, $validate) {
        $history = $game->data['history'];
        array_push($history, $validate);
        $game->update([
            'data' => [
                'turn' => $game->data['turn'],
                'history' => $history,
                'game_data' => $game->data['game_data'],
                'user_data' => $game->data['user_data']
            ]
        ]);
    }

    public function setTurn(\App\Game $game, int $turn) {
        $game->update([
            'data' => [
                'turn' => $turn,
                'history' => $game->data['history'],
                'game_data' => $game->data['game_data'],
                'user_data' => $game->data['user_data']
            ]
        ]);
    }

    public function getTurn(\App\Game $game): int {
        return $game->data['turn'];
    }

    protected function getCards(ProvablyFairResult $result, int $count, $fisher_yates = false): array {
        $cards = range(0, 207);
        $output = [];
        for($i = 0; $i < $count; $i++) array_push($output,
            $fisher_yates ? array_splice($cards, floor($result->extractFloats($count)[$i] * (52 - $i)), 1)[0]
                                 : $cards[floor($result->extractFloats($count)[$i] * 52)]);
        return $output;
    }

}
