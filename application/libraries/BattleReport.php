<?php

class BattleReport
{
    /**
     *
     * @var Unit
     */
    protected $unit;
    
    /**
     *
     * @var Battle
     */
    protected $battle;
    
    /**
     *
     * @var float
     */
    protected $initialLife;
    
    /**
     *
     * @var float
     */
    protected $damageDone;
    
    /**
     *
     * @var float
     */
    protected $damageTaken;
    
    /**
     *
     * @var array
     */
    protected $damageMessages = array();
    
    /**
     *
     * @var array
     */
    protected $rewards = array();
    
    /**
     *
     * @var Message
     */
    protected $message;
    
    /**
     * 
     * @param Unit $target
     * @param Damage $damage
     */
    public function registerDoneDamage(Unit $target, Damage $damage)
    {
        $this->damageDone += $damage->get_amount();
                
        if ($damage->is_miss()) {
            $message = "<div class='missed-hit'>¡Falla el ataque!</div>";
        } elseif ($damage->is_critical()) {
            $message = "<div class='critical-hit'>¡Golpe critico, "
                     . "inflige {$damage->get_amount()} de daño!</div>";
        } else {
            $message = "Inflige {$damage->get_amount()} de daño";
        }
        
        $this->damageMessages[] = "<div class='positive'>{$message}</div>";
    }
    
    /**
     * 
     * @return float
     */
    public function getDamageDone()
    {
        return $this->damageDone;
    }
    
    /**
     * 
     * @param Unit $attacker
     * @param Damage $damage
     */
    public function registerTakenDamage(Unit $attacker, Damage $damage)
    {
        $this->damageTaken += $damage->get_amount();
        
        $message = "Recibe {$damage->get_amount()} de daño";
        $this->damageMessages[] = "<div class='negative'>{$message}</div>";
    }
    
    /**
     * 
     * @return float
     */
    public function getDamageTaken()
    {
        return $this->damageTaken;
    }
    
    /**
     * 
     * @return array
     */
    public function getDamageMessages()
    {
        return $this->damageMessages;
    }
    
    /**
     * 
     * @param Item $reward
     * @param integer $amount
     */
    public function registerReward(Item $reward, $amount)
    {
        $this->rewards[] = array("item" => $reward, "amount" => $amount);
    }

    /**
     *
     * @param Orb $orb
     */
    public function registerOrb(Orb $orb)
    {
        $this->rewards[] = array("item" => $orb, "amount" => 1);
    }
    
    /**
     * 
     * @return array
     */
    public function getRewards()
    {
        return $this->rewards;
    }
    
    /**
     * 
     * @return string
     */
    public function getRewardsForView()
    {
        $elements = "";

		foreach ($this->getRewards() as $reward) {
			switch ($reward['item']->id) {
				case Config::get('game.coin_id'):
                    $image = '<i class="coin coin-copper"></i>';
					break;
				
				case Config::get('game.xp_item_id'):
                    $src = URL::base() . '/img/xp.png';
					$image = "<img src='{$src}' width='22px' height='18px' />";
					break;

				default:
                    $src = $reward['item']->get_image_path();
					$image = "<img src='{$src}' width='22px' height='18px' />";
					break;
			}

            $tooltip = $reward['item']->get_text_for_tooltip() 
                     . "<p>Cantidad: {$reward['amount']}</p>";
            
            $div = "<div class='quest-reward-item' data-toggle='tooltip' "
                 . "data-original-title=\"{$tooltip}\">{$image}</div>";
                 
			$elements .= "<li style='vertical-align: top;'>{$div}</li>";
		}

		return "<ul class='inline' style='margin: 0;'>{$elements}</ul>";
    }
    
    /**
     * 
     * @param float $life
     */
    public function registerInitialLife($life)
    {
        $this->initialLife = $life;
    }
    
    /**
     * 
     * @return float
     */
    public function getInitialLife()
    {
        return $this->initialLife;
    }
    
    /**
     * 
     * @return Message
     */
    public function getMessage()
    {
        if (! $this->message) {
            $this->message = Message::battle_report(
                $this->unit, $this->unit, $this->battle
            );
        }
        
        return $this->message;
    }
    
    public function __construct(Unit $unit, Battle $battle) {
        $this->unit = $unit;
        $this->battle = $battle;
    }
}