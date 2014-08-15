<?php

class NessyCombatBehavior extends AttackableBehavior
{
    public function before_turn(Battle $battle)
    {
        $this->unit->heal($this->get_current_life() * 0.02);
    }
}
