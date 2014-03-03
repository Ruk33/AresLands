<?php

/**
 * 	Esta clase contiene la lógica de las batallas, incluyendo:
 * 
 * 		- Eventos (cuando se ataca, etc.)
 * 		- Recompensas
 * 		- Notificaciones por mensaje privado
 * 		- Robo de orbes
 * 		- Proteccion contra abusos
 * 		- Mensaje de batalla
 * 
 *	@author Franco Montenegro
 */
 
class Battle
{
	/**
	 * Unidad que ataca
	 * @var Eloquent
	 */
	private $_attacker;
	
	/**
	 * Unidad que es atacada
	 * @var Eloquent
	 */
	private $_attacked;
	
	/**
	 * Unidad que es pareja de $_attacker
	 * @var Eloquent
	 */
	private $_pair;
	
	/**
	 *	Ganador de la batalla
	 * 
	 * 	@var Eloquent
	 */
	private $winner;
	
	/**
	 *	Perdedor de la batalla
	 * 
	 * 	@var Eloquent
	 */
	private $loser;
	
	/**
	 *	Mensajes para daños fisicos
	 * 	@var Array
	 */
	private static $messages = array(
		'%1$s logra asestar un gran golpe a %2$s. %2$s no puede evitar soltar un pequeño alarido',
		'%1$s golpea con majestuocidad a %2$s. %2$s se queja',
		'%1$s lanza un feroz ataque a %2$s que sufre algunas heridas',
		'%1$s ataca salvajemente a %2$s que sufre dolorosas heridas',
		'%1$s se mueve ágil y velozmente hacia %2$s para propinarle un gran golpe',
		'%2$s se ve algo cansado, intenta esquivar pero el ataque %1$s lo alcanza',
		'%1$s se dirige a toda marcha hacia %2$s logrando un gran golpe',
		'%2$s da un paso en falso y %1$s aprovecha la oportunidad para golpear',
		'%1$s se avalanza sobre %2$s para propinarle un zarpazo',
		'%2$s lanza un ataque a %1$s, ¡pero éste lo esquiva y lo devuelve!',
		'%2$s intenta defenderse, pero %1$s se las arregla para herirlo',
		'%1$s lanza un sanguinareo ataque contra %2$s el cual da en el blanco',
		'%2$s se distrae y no se percata del ataque de %1$s',
		'%1$s titubea un poco, pero finalmente logra acertar un golpe a %2$s',
		'%1$s con mucha suerte, logra golpear eficazmente a %2$s',
		'%2$s se avalanza contra %1$s, pero %1$s lo sorprende con un inesperado golpe',
		'%1$s hace uso de su agilidad para llegar rápidamente a %2$s y propinar un gran golpe',
	);
	
	/**
	 * Mensajes para daños magicos
	 * @var type 
	 */
	private static $magicMessages = array(
		'%1$s lanza un poderoso hechizo dando en el pecho de %2$s',
		'%2$s cae en la trampa de %1$s recibiendo daño de su magia',
		'%1$s canaliza por un tiempo y lanza su magia contra %2$s',
		'%2$s inesperadamente recibe un gran hechiso de %1$s',
	);
	
	/**
	 *	Registro de la batalla
	 * 
	 * 	@var BattleLog
	 */
	public $log = null;
	
	/**
	 *	Obtenemos el ganador
	 * 
	 * 	@return Character/Npc
	 */
	public function get_winner()
	{
		return $this->winner;
	}
	
	/**
	 *	Obtenemos al perdedor
	 *
	 * 	@return Character/Npc
	 */
	public function get_loser()
	{
		return $this->loser;
	}
	
	private static function on_excellent_attack_warrior($attacker, $defender, &$damage){}
	private static function on_excellent_attack_mage($attacker, $defender, &$damage){}
	private static function on_poor_attack($attacker, $defender, &$damage){}
	private static function on_normal_attack($attacker, $defender, &$damage){}
	
	private static function on_excellent_defense($attacker, $defender, &$defense){}
	private static function on_poor_defense($attacker, $defender, &$defense){}
	private static function on_normal_defense($attacker, $defender, &$defense){}
	
	private static function before_hit($attacker, $defender, &$damage){}
	private static function after_hit($attacker, $defender, $damage){}
	
	/**
	 *	Obtenemos un mensaje de batalla aleatorio
	 * 
	 * 	@return String
	 */
	private static function get_random_message($magical)
	{
		if ( $magical )
		{
			return self::$magicMessages[mt_rand(0, count(self::$magicMessages) - 1)];
		}
		else
		{
			return self::$messages[mt_rand(0, count(self::$messages) - 1)];
		}
	}
	
	/**
	 *	Agregamos un mensaje al log
	 * 
	 * 	@param String 	$message 	Mensaje a agregar
	 * 	@param Bool 	$onTop 		true si queremos el mensaje arriba del todo
	 */
	private function add_message_to_log($message, $onTop = false)
	{
		if ( $onTop )
		{
			$this->log->message = '<li>' . $message . '</li>' . $this->log->message;
		}
		else
		{
			$this->log->message .= '<li>' . $message . '</li>';
		}
	}
	
	/**
	 *	Agrega salto de linea al log
	 * 
	 * 	@param Bool $onTop
	 */
	private function add_blank_space($onTop = true)
	{
		$this->add_message_to_log('&nbsp;', $onTop);
	}
	
	private function give_experience()
	{
		$winnerExperience = 0;
		$loserExperience = 0;
		
		$tournament = Tournament::get_active()->first();
		
		if ( $this->winner instanceof Character )
		{
			$this->add_blank_space();
			
			if ( $this->loser instanceof Npc )
			{
				if ( $this->loser->level > $this->winner->level - 2 )
				{
					$winnerExperience = (int) ($this->loser->xp + max($this->loser->level, 5) / 5 * $this->winner->get_xp_rate());

					// Actualizamos db
					$this->winner->xp += $winnerExperience;
					$this->winner->points_to_change += $winnerExperience;
				}

				$this->add_message_to_log($this->winner->name . ' recibe ' . $winnerExperience . ' punto(s) de experiencia.', true);
			}
			else
			{
				// Experiencia del ganador
				if ( $this->winner->level <= $this->loser->level )
				{
					$winnerExperience = (3 + ($this->loser->level - $this->winner->level)) * $this->winner->get_xp_rate();
				}
				
				// Experiencia del perdedor
				$loserExperience = 1 * $this->loser->get_xp_rate();
				
				if ( ! $tournament || ! $this->winner->is_registered_in_tournament($tournament) )
				{
					$this->winner->xp += $winnerExperience;
					$this->winner->points_to_change += $winnerExperience;
					
					$this->add_message_to_log($this->winner->name . ' recibe ' . $winnerExperience . ' punto(s) de experiencia.', true);
				}
				
				if ( ! $tournament || ! $this->loser->is_registered_in_tournament($tournament) )
				{
					$this->loser->xp += $loserExperience;
					$this->loser->points_to_change += $loserExperience;
					
					$this->add_message_to_log($this->loser->name . ' recibe ' . $loserExperience . ' punto(s) de experiencia.', true);
				}
			}
		}
	}
	
	private function give_rewards()
	{
		// Le damos 2 puntos a la barra de actividad
		// del atacante
		ActivityBar::add($this->_attacker, 2);
		
		if ( $this->winner instanceof Character )
		{
			$coins = (50 * $this->loser->level) * $this->winner->get_coins_rate();
			$percentage = 0;

			if ( $this->loser instanceof Npc )
			{
				if ( $this->loser->level < $this->winner->level - 2 )
				{
					$coins = 0;
				}
			}
			
			if ( $this->loser instanceof Character )
			{
				// Puntos de PVP
				$this->winner->pvp_points += 1;
				
				// Monedas
				$loserCoins = $this->loser->get_coins();
				
				if ( $loserCoins && $loserCoins->count > 0 )
				{
					if ( $this->loser->level > $this->winner->level )
					{
						// Si el perdedor tiene mas nivel que el ganador
						// entonces cada nivel de diferencia lo sumamos al porcentaje
						$percentage = 0.25 + ($this->loser->level - $this->winner->level) * 0.01;
						$percentage = min(0.33, $percentage);
					}
					else
					{
						// Si la diferencia es de mas de 10 niveles
						if ( $this->winner->level - $this->loser->level > 10 )
						{
							$percentage = 0;
							$coins /= 2;
						}
						else
						{
							// Si el ganador tiene mas nivel que el perdedor
							// entonces cada nivel de diferencia lo restamos al porcentaje
							$percentage = 0.25 - ($this->winner->level - $this->loser->level) * 0.01;
						}
					}
					
					$percentage = round(abs($percentage));
					
					if ( $percentage > 0 )
					{
						$coins += $loserCoins->count * $percentage;
			
						$loserCoins->count -= $loserCoins->count * $percentage;
			
						if ( $loserCoins->count > 0 )
						{
							$loserCoins->save();
						}
						else
						{
							$loserCoins->delete();
						}
					}
				}
			}
			
			$this->add_blank_space();
			$this->add_message_to_log($this->winner->name . ' recibe (' . $percentage . '% robado del enemigo): ' . Item::get_divided_coins($coins)['text'], true);
			
			$this->winner->add_coins($coins);
		}
	}

	private function check_for_orbs()
	{
		if ( $this->_attacker instanceof Character && $this->_attacked instanceof Character )
		{
			$attackedOrbs = $this->_attacked->orbs;
			
            if ( $this->_attacker == $this->winner )
            {
                // Verificamos si el perdedor tiene orbes
                // y si éstos pueden ser robados por el ganador
                if ( count($attackedOrbs) > 0 && ! $this->winner->has_orb() )
                {
                    $stolenOrb = null;

                    foreach ( $attackedOrbs as $attackedOrb )
                    {
                        if ( $attackedOrb->can_be_stolen_by($this->winner) )
                        {							
                            $attackedOrb->give_to($this->winner);
                            $stolenOrb = $attackedOrb;

                            break;
                        }
                    }
                    
                    // Si se robó un orbe, entonces
                    // lo notificamos
                    if ( $stolenOrb )
                    {
                        $this->add_blank_space();
                        $this->add_message_to_log('<p>¡' . $this->winner->name . ' ha robado ' . $stolenOrb->name . ' de ' . $this->loser->name . '!</p>', true);
                    }
                }
            }
			else
			{
				foreach ( $attackedOrbs as $attackedOrb )
				{
					if ( $attackedOrb->can_be_stolen_by($this->_attacker) )
					{
						$attackedOrb->failed_robbery($this->_attacker);
						
						// Solo 1 proteccion
						break;
					}
				}
			}
		}
	}
	
	private function check_for_protection()
	{
        if ( $this->_attacker instanceof Character && $this->_attacked instanceof Character )
        {
            if ( $this->_attacker->level > $this->_attacked->level )
            {
                AttackProtection::add($this->_attacker, $this->_attacked, Config::get('game.protection_time_on_lower_level_pvp'));
            }
        }
	}
	
	private function send_notification_message()
	{		
		if ( $this->_attacked instanceof Character )
		{
			if ( $this->_pair )
			{
				Message::attack_report($this->_pair, $this->_pair, $this->log->message, $this->winner);
			}
			Message::attack_report($this->_attacker, $this->_attacked, $this->log->message, $this->winner);
			Message::defense_report($this->_attacked, $this->_attacker, $this->log->message, $this->winner);
		}
	}
		
	/**
	 * 
	 * @param Eloquent $unit
	 * @return array
	 */
	private static function get_unit_info(Eloquent $unit)
	{
		$info = array();
		
		$info['name'] = $unit->name;
		$info['is_player'] = $unit instanceof Character;
		$info['stats'] = $unit->get_stats();
		$info['current_life'] = ( $info['is_player'] ) ? $unit->current_life : $unit->life;
		$info['is_magic'] = $info['stats']['stat_magic'] > $info['stats']['stat_strength'];
		$info['evasion'] = 0;
		$info['critical'] = 0;
		$info['reflect_magic_damage'] = 0;
		$info['reflect_physical_damage'] = 0;
		$info['cheat_death'] = 0;
		$info['cheated_death'] = false;
		$info['added_life'] = 0;
		
		if ( $info['is_magic'] )
		{
			$info['min_damage'] = max(0, $info['stats']['stat_magic'] * 1.25);
			$info['max_damage'] = max($info['min_damage'], $info['stats']['stat_magic'] * 2.85);
			
			$info['cd'] = 1000 / ($info['stats']['stat_magic_skill'] + 1);
		}
		else
		{
			$info['min_damage'] = max(0, $info['stats']['stat_strength']);
			$info['max_damage'] = max($info['min_damage'], $info['stats']['stat_strength'] * 2.60);
			
			$info['cd'] = 800 / ($info['stats']['stat_dexterity'] + 1);
		}
		
		// Defensa magica
		$info['min_magic_defense'] = max(0, $info['stats']['stat_magic_resistance'] * 0.75);
		$info['max_magic_defense'] = max($info['min_magic_defense'], $info['stats']['stat_magic_resistance'] * 1.25);
		
		// Defensa fisica
		$info['min_defense'] = max(0, $info['stats']['stat_resistance'] * 0.75);
		$info['max_defense'] = max($info['min_defense'], $info['stats']['stat_resistance'] * 1.25);
		
		if ( $info['is_player'] )
		{
			$extraDamage = ( $info['is_magic'] ) ? $unit->magic_damage + $unit->magic_damage_extra : $unit->physical_damage + $unit->physical_damage_extra;
			
			$info['min_damage'] += $extraDamage;
			$info['max_damage'] += $extraDamage;
			
			$extraMagicDefense = $unit->magic_defense + $unit->magic_defense_extra;
			$info['min_magic_defense'] += $extraMagicDefense;
			$info['max_magic_defense'] += $extraMagicDefense;
			
			$extraPhysicalDefense = $unit->physical_defense + $unit->physical_defense_extra;
			$info['min_defense'] += $extraPhysicalDefense;
			$info['max_defense'] += $extraPhysicalDefense;
			
			$info['evasion'] = min(50, $unit->evasion + $unit->evasion_extra);
			$info['critical'] = min(50, $unit->critical_chance + $unit->critical_chance_extra);
			
			$info['reflect_magic_damage'] = $unit->reflect_magic_damage + $unit->reflect_magic_damage_extra;
			$info['reflect_physical_damage'] = $unit->reflect_physical_damage + $unit->reflect_physical_damage_extra;
			
			if ( $unit->has_skill(Config::get('game.cheat_death_skill')) )
			{
				$info['cheat_death'] = 5;
			}
			
			if ( $unit->max_life == $unit->current_life )
			{
				$info['current_life'] += $unit->max_life_extra;
				$info['added_life'] = $unit->max_life_extra;
			}
			
			$info['cd'] -= $unit->attack_speed + $unit->attack_speed_extra;
		}
		
		$info['current_cd'] = $info['cd'];
		
		// Log
		$info['initial_life'] = $info['current_life'];
		$info['damage_done'] = 0;
		
		return $info;
	}
	
	private function get_damage($unit, $isMagic)
	{
		$averageDamage = mt_rand($unit['min_damage'], $unit['max_damage']) + 1;
		$damage = $averageDamage;

		// 35% chance de crítico físico
		if ( ! $isMagic && mt_rand(0, 100) <= 35 )
		{
			$damage *= 1.50;
			//self::on_excellent_attack_warrior($attacker, $defender, $damage);
		}
		// 25% chance de crítico mágico
		elseif ( $isMagic && mt_rand(0, 100) <= 25 )
		{
			$damage *= 2.50;
			//self::on_excellent_attack_mage($attacker, $defender, $damage);
		}
		// 10% chance de golpe fallido
		elseif ( mt_rand(0, 100) <= 10 )
		{
			$damage *= 0.75;
			//self::on_poor_attack($attacker, $defender, $damage);
		}
		
		return $damage;
	}
	
	private function get_defense($unit, $isMagic)
	{
		if ( $isMagic )
		{
			$averageDefense = mt_rand($unit['min_magic_defense'], $unit['max_magic_defense']);
		}
		else
		{
			$averageDefense = mt_rand($unit['min_defense'], $unit['max_defense']);
		}
		
		$defense = $averageDefense;

		// 30% de defensa exitosa
		if ( mt_rand(0, 100) <= 30 )
		{
			$defense = $averageDefense * 1.75;
			//self::on_excellent_defense($attacker, $defender, $defense);
		}
		// 10% de defensa fallida
		elseif ( mt_rand(0, 100) <= 10 )
		{
			$defense = $averageDefense * 0.75;
			//self::on_poor_defense($attacker, $defender, $defense);
		}
		
		return $defense;
	}
	
	private function to_battle()
	{
		$attackerStats = self::get_unit_info($this->_attacker);
		$pairStats = ( $this->_pair ) ? self::get_unit_info($this->_pair) : null;
		$attackedStats = self::get_unit_info($this->_attacked);
		
		$attacker = null;
		$defender = null;
		
		$attacks = 0;
		
		while ( $attacks < 100 && $attackerStats['current_life'] > 0 && $attackedStats['current_life'] > 0 )
		{
			$attacks++;
			
			if ( $pairStats && $attacker != $pairStats && mt_rand(0, 100) <= 10 )
			{
				$attacker = &$pairStats;
				$defender = &$attackedStats;
			}
			else
			{
				// Golpea el que menos CD tenga
				if ( $attackerStats['current_cd'] <= $attackedStats['current_cd'] )
				{
					$attacker = &$attackerStats;
					$defender = &$attackedStats;
				}
				else
				{
					$attacker = &$attackedStats;
					$defender = &$attackerStats;
				}

				// Actualizamos CD
				$attacker['current_cd'] += $attacker['cd'];
			}
			
			if ( mt_rand(0, 100) <= $defender['evasion'] )
			{
				$this->add_message_to_log("¡{$defender['name']} esquiva el ataque de {$attacker['name']}!");
				continue;
			}
			
			// Calculamos el daño
			$damage = $this->get_damage($attacker, $attacker['is_magic']);
			
			if ( mt_rand(0, 100) <= $attacker['critical'] )
			{
				$damage *= 2;
			}
			
			$defense = $this->get_defense($defender, $attacker['is_magic']) * 0.40;
			
			// Calculamos el daño verdadero
			$realDamage = min($damage - $defense, $defender['current_life']);
			$realDamage = max(0, $realDamage);
			
			// HIT!
			//self::before_hit($attacker, $defender, $realDamage);
			
			if ( $defender['current_life'] - $realDamage <= 0 )
			{
				if ( $defender['cheat_death'] > 0 )
				{
					$defender['current_life'] = 1;
					
					$defender['cheat_death']--;
					$this->add_message_to_log("¡{$defender['name']} burla a la muerte!");
					
					$defender['cheated_death'] = true;
					
					continue;
				}
			}
			
			$defender['current_life'] -= $realDamage;
			$attacker['damage_done'] += $realDamage;
			
			//self::after_hit($attacker, $defender, $realDamage);
			
			$randomMessage = sprintf(self::get_random_message($attacker['is_magic']), $attacker['name'], $defender['name']);
			
			$this->add_message_to_log($randomMessage . " (daño: {$realDamage})");
			
			if ( $defender['current_life'] > 0 )
			{
				$reflectedDamage = ( $attacker['is_magic'] ) ? $defender['reflect_magic_damage'] : $defender['reflect_physical_damage'];
				
				if ( $reflectedDamage > 0 )
				{
					$attacker['current_life'] -= $reflectedDamage;
					$defender['damage_done'] += $reflectedDamage;

					$this->add_message_to_log("{$defender['name']} refleja {$reflectedDamage} de daño hacia {$attacker['name']}");
				}
			}
		}
		
		if ( $attackerStats['current_life'] > $attackedStats['current_life'] )
		{
			$this->winner = $this->_attacker;
			$this->loser = $this->_attacked;
		}
		else
		{
			$this->winner = $this->_attacked;
			$this->loser = $this->_attacker;
		}
		
		$this->give_experience();
		$this->give_rewards();
		$this->check_for_orbs();
		$this->check_for_protection();
		$this->send_notification_message();
		
		// Agregamos al registro la vida inicial de ambos
		$this->add_blank_space();
		$this->add_message_to_log('Vida inicial de ' . $attackerStats['name'] . ': ' . $attackerStats['initial_life'], true);
		$this->add_message_to_log('Vida inicial de ' . $attackedStats['name'] . ': ' . $attackedStats['initial_life'], true);
		
		// Agregamos al registro el daño realizado por ambos
		$this->add_blank_space();
		$this->add_message_to_log('Daño realizado por ' . $attackerStats['name'] . ': ' . number_format($attackerStats['damage_done'], 2), true);
		$this->add_message_to_log('Daño realizado por ' . $attackedStats['name'] . ': ' . number_format($attackedStats['damage_done'], 2), true);
		
		// Actualizamos las vidas
		if ( $this->_pair )
		{
			$this->add_message_to_log('Daño realizado por ' . $pairStats['name'] . ': ' . number_format($pairStats['damage_done'], 2), true);
		}
		
		$this->_attacker->current_life = $attackerStats['current_life'] - $attackerStats['added_life'];
		$this->_attacker->save();
		
		if ( $attackerStats['cheated_death'] )
		{
			// Removemos Burlar a la muerte (puesto que ya ha sido utilizado)
			$cheatDeathSkill = $this->_attacker->skills()->where('skill_id', '=', Config::get('game.cheat_death_skill'))->first();
			
			if ( $cheatDeathSkill )
			{
				$this->_attacker->remove_buff($cheatDeathSkill);
			}
		}
		
		if ( $attackedStats['is_player'] )
		{
			$this->_attacked->current_life = $attackedStats['current_life'] - $attackedStats['added_life'];
			$this->_attacked->save();
			
			if ( $attackedStats['cheated_death'] )
			{
				// Removemos Burlar a la muerte (puesto que ya ha sido utilizado)
				$cheatDeathSkill = $this->_attacked->skills()->where('skill_id', '=', Config::get('game.cheat_death_skill'))->first();

				if ( $cheatDeathSkill )
				{
					$this->_attacked->remove_buff($cheatDeathSkill);
				}
			}
		}
		
		// Disparamos el evento de batalla
		if ( $attackedStats['is_player'] )
		{
			Event::fire('battle', array($this->_attacker, $this->_attacked, $this->winner));
		}
		else
		{
			Event::fire('pveBattle', array($this->_attacker, $this->_attacked, $this->winner));
		}
	}
	
	/**
	 * 
	 * @param Eloquent $attacker
	 * @param Eloquent $attacked
	 * @param Eloquent $pair
	 */
	public function __construct(Character $attacker, Eloquent $attacked, Character $pair = null)
	{
		$this->log = new BattleLog();
		
		$this->_attacker = $attacker;
		$this->_attacked = $attacked;
		$this->_pair = $pair;
		
		$attacker->check_skills_time();
		
		if ( $attacked instanceof Character )
		{
			$attacked->check_skills_time();
			$attacked->regenerate_life(true);
		}
		
		if ( $pair )
		{
			$pair->check_skills_time();
		}
		
		$this->to_battle();
		
		$this->log->message = '<ul class="unstyled">' . $this->log->message . '</ul>';
		$this->log->save();
		
		if ( $attacked instanceof Character )
		{
			$attacker->after_pvp_battle();
		}
		else
		{
			$attacker->after_battle();
		}
	}
}