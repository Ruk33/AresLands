<?php namespace Models\Vip;

use Laravel\Validator;
use Laravel\Config;

class VipReductionTimeImplementation extends VipBuffImplementation
{
    protected function getSkill()
    {
        $buffId = Config::get('game.vip_reduction_time_skill');
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