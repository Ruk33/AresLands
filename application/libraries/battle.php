<?php

/**
 * Class Battle
 */
class Battle
{
	/**
	 * @var Unit
	 */
	protected $_winner;

	/**
	 * @var Unit
	 */
	protected $_loser;

	/**
	 * @var Message
	 */
	protected $_attackerNotificationMessage;

	/**
	 * @var Unit
	 */
	protected $_attacker;

	/**
	 * @var Unit
	 */
	protected $_target;

	/**
	 * @var Unit
	 */
	protected $_pair;

	/**
	 * Mensaje de cada ataque
	 *
	 * @var Array
	 */
	protected $_log = array();

	/**
	 *
	 * @var Array
	 */
	protected $_rewards = array();

	/**
	 * @var Array
	 */
	protected $_damageDone = array();

	/**
	 * @var Array
	 */
	protected $_initialLife = array();

	/**
	 * Orbe que fue robado (si hubo)
	 *
	 * @var Orb
	 */
	protected $_stolenOrb;

	/**
	 * @return Unit
	 */
	public function get_winner()
	{
		return $this->_winner;
	}

	/**
	 * @return Unit
	 */
	public function get_loser()
	{
		return $this->_loser;
	}

	/**
	 * @return Message
	 */
	public function get_attacker_notification_message()
	{
		return $this->_attackerNotificationMessage;
	}

	/**
	 * @return Unit
	 */
	public function get_attacker()
	{
		return $this->_attacker;
	}

	/**
	 * @return Unit
	 */
	public function get_target()
	{
		return $this->_target;
	}

	/**
	 * @return Unit
	 */
	public function get_pair()
	{
		return $this->_pair;
	}

	/**
	 * @return Array
	 */
	public function get_log()
	{
		return $this->_log;
	}
    
    public function get_rewards_for_view(Unit $unit)
    {
		$formatedString = '';

		foreach ( $this->_rewards[$this->get_secure_index($unit)] as $reward )
		{
			switch ($reward['item_id']) {
				case Config::get('game.coin_id'):
					$coins = Item::get_divided_coins($reward['amount']);
					
					$text = '<i class="coin coin-copper"></i>';
					$text = '<span data-toggle="tooltip" data-original-title="Cantidad: 
						<ul class=\'inline\' style=\'margin: 0;\'>
						<li><i class=\'coin coin-gold pull-left\'></i> ' . $coins['gold'] . '</li>
						<li><i class=\'coin coin-silver pull-left\'></i> ' . $coins['silver'] . '</li>
						<li><i class=\'coin coin-copper pull-left\'></i> ' . $coins['copper'] . '</li>
					</ul>">' . $text . '</span>';
					break;
				
				case Config::get('game.xp_item_id'):
					$text = '<img src="' . URL::base() . '/img/xp.png" width="22px" height="18px" />';
					$text = '<span data-toggle="tooltip" data-original-title="Cantidad: ' . $reward['amount'] . '">' . $text . '</span>';
					break;

				default:
					$text = '<img src="' . URL::base() . '/img/icons/items/'. $reward['item_id'] .'.png" />';
					$text = '<span data-toggle="tooltip" data-original-title="' . $reward['item']->get_text_for_tooltip() . '<p>Cantidad: ' . $reward['amount'] . '</p>">' . $text . '</span>';
					break;
			}

			$formatedString .= '<li style="vertical-align: top;"><div class="quest-reward-item">' . $text . '</div></li>';
		}

		return '<ul class="inline" style="margin: 0;">' . $formatedString . '</ul>';
    }

	/**
	 * Obtenemos un indice seguro (para evitar colisiones)
	 * @param  Unit $unit 
	 * @return mixed
	 */
	protected function get_secure_index(Unit $unit)
	{
		$index = $unit->id;

		if ($unit instanceof Npc) {
			$index = "npc-{$index}";
		}

		return $index;
	}

	/**
	 * Obtenemos el daÃ±o realizado por unidad
	 *
	 * @param Unit $unit
	 * @return float
	 */
	public function get_damage_done_by(Unit $unit)
	{
		$index = $this->get_secure_index($unit);
		return (isset($this->_damageDone[$index])) ? $this->_damageDone[$index] : 0;
	}

	/**
	 * Obtenemos la vida inicial de unidad
	 *
	 * @param Attacker $unit
	 * @return float
	 */
	public function get_initial_life_of(Unit $unit)
	{
		$index = $this->get_secure_index($unit);
		return (isset($this->_initialLife[$index])) ? $this->_initialLife[$index] : 0;
	}

	/**
	 * @return Orb
	 */
	public function get_stolen_orb()
	{
		return $this->_stolenOrb;
	}

	/**
	 * Verificamos el orbe (en caso de que haya que robar o agregar proteccion)
	 */
	protected function check_for_orbs()
	{
		if ($this->isPvp() && ! $this->_attacker->has_orb()) {
            $targetOrb = $this->_target->orbs()->first();

            if ($targetOrb && $targetOrb->can_be_stolen_by($this->_attacker)) {
                // Si gano entonces se lo damos, de lo contrario
                // se agrega proteccion al dueÃ±o del orbe
                if ($this->_winner->id == $this->_attacker->id) {
                    $targetOrb->give_to($this->_attacker);
                    $this->_stolenOrb = $targetOrb;
                } else {
                    $targetOrb->failed_robbery($this->_attacker);
                }
            }
		}
	}

	/**
	 * Damos las recompensas al ganador (en caso de ser Character)
	 */
	protected function give_rewards()
	{
		ActivityBar::add($this->_attacker, 2);

        if ($this->isPvp()) {
            $this->_winner->pvp_points++;
        }
        
		if ($this->_winner instanceof Character) {
			if ($this->_winner->level - 2 < $this->_loser->level) {
                $winnerIndex = $this->get_secure_index($this->_winner);
				$rewards = $this->_loser->drops();
				$xpBonus = 
                    max(0, $this->_loser->level - 2 - $this->_winner->level)/5;

				foreach ($rewards as $reward) {
                    $item = 
                        \Laravel\IoC::resolve("Item")->find($reward['item_id']);
                    
                    if (! $item) {
                        continue;
                    }
                    
                    if ($reward['item_id'] == Config::get('game.xp_item_id')) {
                        $reward['amount'] += $reward['amount'] * $xpBonus;
                    }

                    if ($this->_winner->add_item($item, $reward['amount'])) {
                        $this->_rewards[$winnerIndex][] = array(
                            'item_id' => $reward['item_id'],
                            'amount' => $reward['amount'],
                            'item' => $item
                        );
                    }
				}
			}
		}
	}
    
    /**
     * Verificamos si la batalla es de jugador contra jugador
     * @return boolean
     */
    public function isPvp()
    {
        return $this->_attacker instanceof Character && 
               $this->_target instanceof Character;
    }

	/**
	 * Verificamos si es necesario dar protecciones
	 */
	protected function check_for_protection()
	{
		if ($this->isPvp()) {
			if ($this->_attacker->level > $this->_target->level) {
				AttackProtection::add(
                    $this->_attacker, 
                    $this->_target, 
                    Config::get('game.protection_time_on_lower_level_pvp')
                );
			}
		}
	}

	/**
	 * Comenzamos la batalla
	 */
	protected function begin()
	{        
        $attackerIndex = $this->get_secure_index($this->_attacker);
        $targetIndex = $this->get_secure_index($this->_target);

        // El primer golpe siempre es de quien inicia la batalla
		$source = $this->_attacker;
		$target = $this->_target;

		// Maximo de 20 ataques
        $maxAttacks = 20;
		$attacks = $maxAttacks;
        
        // La mitad de los ataques sera fisico y la otra mitad magica.
        $magicalAttack = false;
        
        $message = "";
        
		while ($attacks > 0 && 
               $this->_attacker->get_current_life() > 0 && 
               $this->_target->get_current_life() > 0) {
                        
            $attacks--;
            
            // Guardamos la cantidad de vida antes de hacer el daÃ±o
            // para verificar luego si ha burlado a la muerte
            $prevLife = $target->get_current_life();

			$damage = $source->get_combat_behavior()->get_damage();
            $damage->to($target, $magicalAttack);
            
            if ($damage->is_miss()) {
                $message = "<div class='missed-hit'>¡Falla el ataque!</div>";
            } elseif ($damage->is_critical()) {
                $message = "<div class='critical-hit'>¡Acerta un golpe critico, "
                         . "haciendo {$damage->get_amount()} de daño!</div>";
            } else {
                $message = "Inflige {$damage->get_amount()} de daño";
            }
            
            $this->_damageDone[$this->get_secure_index($source)] += 
                $damage->get_amount();
            
            $this->_log[$this->get_secure_index($source)][] = array(
                'magical' => $magicalAttack,
                'message' => "<div class='positive'>{$message}</div>"
            );
            
            // Verificamos si burlo a la muerte
            $cheatedDeath = 
                $prevLife - $damage->get_amount() <= 0 &&
                $target->has_buff(Config::get('game.cheat_death_skill'));
                        
            if ($cheatedDeath) {
                $message = "<b>¡{$target->name} burla a la muerte!</b>";
            } else {
                $message = "Recibe {$damage->get_amount()} de daño";
            }
            
            $this->_log[$this->get_secure_index($target)][] = array(
                'magical' => $magicalAttack,
                'message' => "<div class='negative'>{$message}</div>"
            );
                
            if ($target->get_current_life() == 0 && ! $magicalAttack) {
                // Si el objetivo actual perdio la mitad del combate
                // (recordamos que son dos, fisico y magico)
                // entonces curamos la cantidad de daño que se hayan hecho
                // para que sea parejo y que ambos combates puedan efectuarse
                // (de otro modo, podria terminarse solo en el fisico)
                $target->heal($this->get_damage_done_by($source));
                $source->heal($this->get_damage_done_by($target));
                
                // Y por supuesto, pasamos al siguiente combate
                $magicalAttack = true;
            } else {
                // Cambiamos el tipo de daño a magico si ya se hicieron
                // la mitad de los ataques
                if ($maxAttacks/2 == $attacks) {
                    $magicalAttack = true;
                }
            }
            
            // Si hay una pareja y el ataque previo no fue de ella 
            // (evitamos multiples ataques consecutivos de la pareja)
			// vemos si tiene 33% de chance de golpear
            $pairAttacks = $this->_pair && 
                           $source->id != $this->_pair->id && 
                           mt_rand(1, 100) <= 33;
            
			if ($pairAttacks) {
				$source = $this->_pair;
				$target = $this->_target;
			} else {
                $prevSourceIsAttacker = 
                    $this->get_secure_index($source) == $attackerIndex;
                
                $doubleHitChance = 
                    mt_rand(1, 100) <= $damage->get_double_hit_chance();
                
				if ($doubleHitChance && ! $prevSourceIsAttacker) {
                    
                } else {
                    if ($prevSourceIsAttacker) {
                        $source = $this->_target;
                        $target = $this->_attacker;
                    } else {
                        $source = $this->_attacker;
                        $target = $this->_target;
                    }
				}
			}
		}
        
        $damageByAttacker = $this->get_damage_done_by($this->_attacker);
        $damageByTarget = $this->get_damage_done_by($this->_target);
        $damageByPair = ($this->_pair) ? $this->get_damage_done_by($this->_pair) : 0;
        
        // Actualizamos las vidas a los valores reales ya que a mitad
        // del combate, se realiza una curacion
        $this->_attacker->set_current_life(
            $this->get_initial_life_of($this->_attacker) - 
            $damageByTarget
        );
        
        $this->_target->set_current_life(
            $this->get_initial_life_of($this->_target) - 
            $damageByAttacker - 
            $damageByPair
        );
        
        $attackerIsAlive = $this->_attacker->get_current_life() > 0;
        $attackerHasDoneMoreDamage = $damageByAttacker > $damageByTarget;
        
        // Gana aquel que haya hecho mas daño y este vivo
		if ($attackerIsAlive && $attackerHasDoneMoreDamage) {
            $this->_winner = $this->_attacker;
            $this->_loser = $this->_target;
		} else {
            $this->_winner = $this->_target;
            $this->_loser = $this->_attacker;
		}
	}
    
    public function get_unit_log(Unit $unit)
    {
        $index = $this->get_secure_index($unit);
        return (isset($this->_log[$index])) ? $this->_log[$index] : array();
    }

	/**
     * 
     * @param Unit $attacker
     * @param Unit $target
     * @param Unit $pair
     */
	public function __construct(Unit $attacker, Unit $target, Unit $pair = null)
	{
        // Si uno de los dos no puede ser atacado entonces no hacemos nada
        if ( ! $attacker->is_attackable() || ! $target->is_attackable() )
        {
            return;
        }
        
		$this->_attacker = $attacker;
		$this->_target = $target;
		$this->_pair = $pair;

		$this->_attacker->check_skills_time();
		$this->_target->check_skills_time();

		$this->_attacker->regenerate_life(true);
        $this->_target->regenerate_life(true);

		if ( $this->_pair )
		{
			$this->_pair->check_skills_time();
			$this->_damageDone[$this->get_secure_index($pair)] = 0;
            $this->_log[$this->get_secure_index($pair)] = array();
		}
        
        $attackerIndex = $this->get_secure_index($attacker);
        $targetIndex = $this->get_secure_index($target);

		$this->_damageDone[$attackerIndex] = 0;
		$this->_damageDone[$targetIndex] = 0;

		$this->_initialLife[$attackerIndex] = $attacker->get_current_life();
		$this->_initialLife[$targetIndex] = $target->get_current_life();

        $this->_log[$attackerIndex] = array();
        $this->_log[$targetIndex] = array();
        
        $this->_rewards[$attackerIndex] = array();
        $this->_rewards[$targetIndex] = array();
        
		$this->begin();
		$this->check_for_protection();
		$this->give_rewards();
		$this->check_for_orbs();

		$this->_attackerNotificationMessage = Message::battle_report(
            $this->_attacker, $this->_attacker, $this
        );

		if ( $this->_target instanceof Character ) {
			Message::battle_report($this->_target, $this->_target, $this);
		}

		if ( $this->_pair ) {
			Message::battle_report($this->_attacker, $this->_pair, $this);
		}

		if ($this->isPvp()) {
			Event::fire('battle', array(
                $this->_attacker, $this->_target, $this->_winner
            ));
            
			$attacker->after_pvp_battle();
		} else {
			Event::fire('pveBattle', array(
                $this->_attacker, $this->_target, $this->_winner
            ));
            
			$attacker->after_battle();
		}

		$this->_attacker->save();
        
        if ($this->_target instanceof Character) {
            $this->_target->save();
        }
	}
}