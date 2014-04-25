<?php

class DungeonBattle extends Battle
{
	/**
	 * @var Dungeon
	 */
	protected $_dungeon = null;

	/**
	 * Nivel del dungeon
	 * @var integer
	 */
	protected $_level = 1;

	/**
	 * ¿El personaje pudo completar la dungeon?
	 * @var boolean
	 */
	protected $_completed = false;

	/**
	 * ¿Puede el personaje recibir la recompensa especial?
	 * Solamente aquellos que no hayan completado 
	 * aun la mazmorra y la hayan terminado pueden hacerlo
	 * @var boolean
	 */
	protected $_canGetSpecialReward = false;

	/**
	 * Verificamos si personaje pudo completar la mazmorra
	 * @return boolean
	 */
	public function get_completed()
	{
		return $this->_completed;
	}

	protected function give_rewards($dungeonLevel = 1)
	{
		// No queremos que de barra de actividad
		// en cada batalla
		ActivityBar::add($this->_attacker, -2);
		parent::give_rewards($dungeonLevel);
	}

	protected function give_special_rewards()
	{
		foreach ( $this->_dungeon->rewards as $reward )
		{
			if ( mt_rand(0, 100) <= $reward->chance )
			{
				$this->_attacker->add_item($reward->item_id, $reward->amount);
			}
		}
	}

	/**
	 * Incrementamos los atributos del monstruo por el nivel del dungeon
	 */
	protected function increase_target_stats()
	{
		if ( $this->_level <= 0 || $this->_level == 1 )
		{
			return;
		}

		$this->_target->life                  *= $this->_level;
		$this->_target->stat_strength         *= $this->_level;
		$this->_target->stat_dexterity        *= $this->_level;
		$this->_target->stat_resistance       *= $this->_level;
		$this->_target->stat_magic            *= $this->_level;
		$this->_target->stat_magic_skill      *= $this->_level;
		$this->_target->stat_magic_resistance *= $this->_level;
	}

	/**
	 * @param Character $character
	 * @param Dungeon   $dungeon  
	 * @param integer   $level
	 */
	public function __construct(Character $character, Dungeon $dungeon, $level = 1)
	{
		$this->_dungeon = $dungeon;
		$this->_attacker = $character;
		$this->_level = $level;
		$this->_canGetSpecialReward = $dungeon->get_progress_percent_of($character, $level) != 100;

		$this->_attacker->check_skills_time();

		$monsters = $dungeon->monsters()->order_by('level', 'asc')->get();

		foreach ( $monsters as $monster )
		{
			$this->_target = $monster;
			$this->increase_target_stats();

			$this->_damageDone[$this->get_secure_index($this->_attacker)] = 0;
			$this->_damageDone[$this->get_secure_index($this->_target)] = 0;

			$this->_initialLife[$this->get_secure_index($this->_attacker)] = $this->_attacker->get_current_life();
			$this->_initialLife[$this->get_secure_index($this->_target)] = $this->_target->get_current_life();

			$this->_rewardLog = array();
			$this->_log = array();

			$this->begin();

			Message::battle_report($this->_attacker, $this->_attacker, $this);
			Event::fire('pveBattle', array($this->_attacker, $this->_target, $this->_winner));

			// Si el personaje perdio, cortamos
			if ( ! $this->_winner instanceof Character )
			{
				break;
			}
			else
			{
				// Entre cada batalla, recargamos 25% de la vida
				$this->_attacker->current_life *= 1.25;
				$this->give_rewards($this->_level);

				// Actualizamos el progreso
				CharacterDungeon::make_progress($this->_attacker, $dungeon, $this->_level, $this->_target);
			}
		}

		$this->_completed = $this->_winner->id == $this->_attacker->id;

		if ( $this->_canGetSpecialReward && $this->_completed )
		{
			$this->give_special_rewards();
		}

		$this->_attacker->after_dungeon($dungeon, $level);

		ActivityBar::add($this->_attacker, 5);

		$this->_attacker->save();
	}
}