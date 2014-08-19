<?php

class PveBattle extends Battle
{
    const REWARD_ACTIVITY_BAR = 1;
    const MAX_ATTACKS = 50;

    /**
     * Verificamos si el ganador debe o no recibir recompensas
     * Recordar que el oro sera otorgado de igual forma
     * @return boolean
     */
    protected function winnerShouldReceiveRewards()
    {
        return $this->getWinner()->level - 2 < $this->getLoser()->level;
    }

    /**
     * Otorgamos recompensas al ganador
     */
    protected function giveRewards()
    {
        ActivityBar::add($this->getAttacker(), self::REWARD_ACTIVITY_BAR);
        
        // Somos ortivas y no damos recompensa a los monstruos >:v
        if ($this->getWinner() instanceof Monster) {
            return;
        }

        $onlyCoins = $this->winnerShouldReceiveRewards() == false;

        foreach ($this->getLoser()->drops_for($this->getWinner()) as $reward) {
            // Si no merece recompensas y justamente la recompensa no es moneda
            // entonces la saltamos
            if ($onlyCoins && $reward['item_id'] != Config::get('game.coin_id')) {
                continue;
            }

            $item = Laravel\IoC::resolve('Item')->find($reward['item_id']);

            if (! $item) {
                continue;
            }

            if ($this->getWinner()->add_item($item, $reward['amount'])) {
                $this->getReportOf($this->getWinner())
                     ->registerReward($item, $reward['amount']);
            }
        }
    }

    protected function onStart() {
        $this->getAttacker()->regenerate_life(true);
        $this->getAttacker()->check_buffs_time();

        parent::onStart();
    }

    protected function onFinish() {
        $this->getAttacker()->after_battle();
        $this->getAttacker()->save();

        $this->giveRewards();

        \Laravel\Event::fire("pveBattle", array(
            $this->getAttacker(),
            $this->getTarget(),
            $this->getWinner()
        ));
    }

    protected function damageShouldBeMagic(Unit $attacker, Unit $target)
    {
        return $attacker->get_final_magic() > $attacker->get_final_strength();
    }

    protected function shouldContinue() {
        return parent::shouldContinue() && $this->getTurn() < self::MAX_ATTACKS;
    }

    public function __construct(Character $attacker, Monster $target)
    {
        $this->attacker = $attacker;
        $this->target = $target;

        $this->attackerReport = new BattleReport($attacker, $this);
        $this->targetReport = new BattleReport($target, $this);

        $this->start();
    }
}
