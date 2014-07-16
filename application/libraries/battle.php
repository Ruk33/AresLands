<?php

/**
 * Class Battle
 */
class Battle
{
	/**
	 * @var Attackable
	 */
	protected $_winner;

	/**
	 * @var Attackable
	 */
	protected $_loser;

	/**
	 * @var Message
	 */
	protected $_attackerNotificationMessage;

	/**
	 * @var Attackable
	 */
	protected $_attacker;

	/**
	 * @var Attackable
	 */
	protected $_target;

	/**
	 * @var Attackable
	 */
	protected $_pair;

	/**
	 * Mensaje de cada ataque
	 *
	 * @var Array
	 */
	protected $_log = array();

	/**
	 * Historial de recompensas
	 *
	 * @var Array
	 */
	protected $_rewardLog = array();

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
	 * @return Attackable
	 */
	public function get_winner()
	{
		return $this->_winner;
	}

	/**
	 * @return Attackable
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
	 * @return Attackable
	 */
	public function get_attacker()
	{
		return $this->_attacker;
	}

	/**
	 * @return Attackable
	 */
	public function get_target()
	{
		return $this->_target;
	}

	/**
	 * @return Attackable
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

	/**
	 * @return Array
	 */
	public function get_reward_log()
	{
		return $this->_rewardLog;
	}

	/**
	 * Obtenemos un indice seguro (para evitar colisiones)
	 * @param  Attackable $attackable 
	 * @return mixed
	 */
	protected function get_secure_index(Unit $attackable)
	{
		$index = $attackable->id;

		if ( $attackable instanceof Monster )
		{
			$index = "npc-{$index}";
		}

		return $index;
	}

	/**
	 * Obtenemos el daño realizado por unidad
	 *
	 * @param Attacker $unit
	 * @return float
	 */
	public function get_damage_done_by(Attackable $unit)
	{
		$index = $this->get_secure_index($unit);
		return ( isset($this->_damageDone[$index]) ) ? $this->_damageDone[$index] : 0;
	}

	/**
	 * Obtenemos la vida inicial de unidad
	 *
	 * @param Attacker $unit
	 * @return float
	 */
	public function get_initial_life_of(Attackable $unit)
	{
		$index = $this->get_secure_index($unit);
		return ( isset($this->_initialLife[$index]) ) ? $this->_initialLife[$index] : 0;
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
		if ( $this->_attacker instanceof Character && $this->_target instanceof Character )
		{
			if ( ! $this->_attacker->has_orb() )
			{
				$targetOrb = $this->_target->orbs()->first();

				if ( $targetOrb )
				{
					if ( $targetOrb->can_be_stolen_by($this->_attacker) )
					{
						// Si gano entonces se lo damos, de lo contrario
						// se agrega proteccion al dueño del orbe
						if ( $this->_winner->id == $this->_attacker->id )
						{
							$targetOrb->give_to($this->_attacker);
							$this->_stolenOrb = $targetOrb;
						}
						else
						{
							$targetOrb->failed_robbery($this->_attacker);
						}
					}
				}
			}
		}
	}

	/**
	 * Damos las recompensas al ganador (en caso de ser Character)
	 * @param  integer $dungeonLevel Nivel del dungeon
	 */
	protected function give_rewards($dungeonLevel = 1)
	{
		ActivityBar::add($this->_attacker, 2);

		if ( $this->_winner instanceof Character )
		{
			if ( $this->_loser instanceof Character )
			{
				$this->_winner->pvp_points++;
			}

			if ( $this->_winner->level - 2 < $this->_loser->level )
			{
				$rewards = $this->_loser->get_rewards($dungeonLevel);
				$xpBonus = max(0, $this->_loser->level - 2 - $this->_winner->level) / 5;

				foreach ( $rewards as $reward )
				{
					$item = Item::select(array('id', 'name'))->where('id', '=', $reward['item_id'])->first();

					if ( $item )
					{
						if ( $item->id == Config::get('game.xp_item_id') )
						{
							$reward['amount'] += $reward['amount'] * $xpBonus;
						}

						if ( $this->_winner->add_item($reward['item_id'], $reward['amount']) )
						{
							$this->_rewardLog[] = "{$this->_winner->name} obtiene {$reward['amount']} {$item->name}";
						}
						else
						{
							$this->_rewardLog[] = "{$this->_winner->name} no tiene espacio para guardar {$item->name}, debe dejarlo.";
						}
					}
				}
			}
		}
	}

	/**
	 * Verificamos si es necesario dar protecciones
	 */
	protected function check_for_protection()
	{
		if ( $this->_attacker instanceof Character && $this->_target instanceof Character )
		{
			if ( $this->_attacker->level > $this->_target->level )
			{
				AttackProtection::add($this->_attacker, $this->_target, Config::get('game.protection_time_on_lower_level_pvp'));
			}
		}
	}

	/**
	 * Comenzamos la batalla
	 */
	protected function begin()
	{
		// Guardamos los cooldown de las unidades
		$cds = array(
			$this->get_secure_index($this->_attacker) => $this->_attacker->get_cd(),
			$this->get_secure_index($this->_target) => $this->_target->get_cd(),
		);

		$source = null;
		$target = null;

		// Maximo de 30 ataques
		$attacks = 20;
        $attackWithMagic = false;

		while ( $attacks > 0 && $this->_attacker->get_current_life() > 0 && $this->_target->get_current_life() > 0 )
		{
			$attacks--;

			// Si hay una pareja y el ataque previo no fue de ella (evitamos multiples ataques consecutivos de la pareja)
			// vemos si tiene 33% de chance de golpear
			if ( $this->_pair && $source && $source->id != $this->_pair->id && mt_rand(0, 100) <= 33 )
			{
				$source = $this->_pair;
				$target = $this->_target;
			}
			else
			{
				if ( $cds[$this->get_secure_index($this->_attacker)] < $cds[$this->get_secure_index($this->_target)] )
				{
					$source = $this->_attacker;
					$target = $this->_target;
				}
				else
				{
					$source = $this->_target;
					$target = $this->_attacker;
				}

				$cds[$this->get_secure_index($source)] += $source->get_cd();
			}

			if ( mt_rand(0, 100) <= $target->get_evasion_chance() )
			{
				$this->_log[] = "{$target->name} evade el ataque de {$source->name}.";
			}
			else
			{
				$damage = Damage::normal($source, $target);
				$this->_damageDone[$this->get_secure_index($source)] += $damage;

				if ( $target->get_current_life() <= 0 && $target->has_skill(Config::get('game.cheat_death_skill')) )
				{
					$target->cheat_death();
					$target->set_current_life(1);

					$this->_log[] = "{$target->name} recibe {$damage} de daño pero, ¡logra burlar a la muerte!.";
				}
				else
				{
					$this->_log[] = "{$source->name} ataca a {$target->name}, haciendo {$damage} de daño.";
				}

				// Evitamos reflejar mas del daño que se realizo
				$reflectedDamage = min($damage, $target->get_reflected_damage($source->attacks_with_magic()));

				// Verificamos si tiene para reflejar y si tiene 33% de chance para hacerlo
				if ( $reflectedDamage > 0 && mt_rand(0, 100) <= 33 )
				{
					$damage = Damage::to_target($target, $source, $reflectedDamage, $source->attacks_with_magic());
					$this->_damageDone[$this->get_secure_index($target)] += $damage;

					$this->_log[] = "{$target->name} refleja {$damage} de daño a {$source->name}.";
				}
			}
		}
        
        $attackerIsAlive = $this->_attacker->get_current_life() > 0;
        $attackerHasDoneMoreDamage = $this->get_damage_done_by($this->_attacker) > $this->get_damage_done_by($this->_target);
        
        // Gana aquel que haya hecho mas daño y este vivo
		if ( $attackerIsAlive && $attackerHasDoneMoreDamage )
		{
			$this->_winner = $this->_attacker;
			$this->_loser = $this->_target;
		}
		else
		{
			$this->_winner = $this->_target;
			$this->_loser = $this->_attacker;
		}

		$this->_log[] = "{$this->_loser->name} ya no puede continuar. ¡{$this->_winner->name} obtiene la victoria!.";
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
		}
        
        $attackerIndex = $this->get_secure_index($attacker);
        $targetIndex = $this->get_secure_index($target);

		$this->_damageDone[$attackerIndex] = 0;
		$this->_damageDone[$targetIndex] = 0;

		$this->_initialLife[$attackerIndex] = $attacker->get_current_life();
		$this->_initialLife[$targetIndex] = $target->get_current_life();

		$this->begin();
		$this->check_for_protection();
		$this->give_rewards();
		$this->check_for_orbs();

		$this->_attackerNotificationMessage = Message::battle_report($this->_attacker, $this->_attacker, $this);

		if ( $this->_target instanceof Character )
		{
			Message::battle_report($this->_target, $this->_target, $this);
		}

		if ( $this->_pair )
		{
			Message::battle_report($this->_attacker, $this->_pair, $this);
		}

		if ( $target instanceof Character )
		{
			Event::fire('battle', array($this->_attacker, $this->_target, $this->_winner));
			$attacker->after_pvp_battle();
		}
		else
		{
			Event::fire('pveBattle', array($this->_attacker, $this->_target, $this->_winner));
			$attacker->after_battle();
		}

		$this->_attacker->save();
		$this->_target->save();
	}
}