<?php namespace Models\Vip;

use Laravel\Validator;
use Laravel\Config;

class VipCoinMultiplierImplementation extends VipBuffImplementation
{
    protected function getSkill()
    {
        $buffId = Config::get('game.vip_multiplier_coin_rate_skill');
        return $this->getSkillRepository()->find($buffId);
    }

    public function getInputs()
    {
        return "";
    }

    public function getValidator()
    {
        return Validator::make($this->attributes, array());
    }
}