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
	private $_attackerUnit;
	
	/**
	 * Unidad que es atacada
	 * @var Eloquent
	 */
	private $_attackedUnit;
	
	/**
	 *	Ganador de la batalla
	 * 
	 * 	@var Eloquent
	 */
	private $_winner;
	
	/**
	 *	Perdedor de la batalla
	 * 
	 * 	@var Eloquent
	 */
	private $_loser;
	
	/**
	 *	Mensajes de batalla
	 * 
	 * 	@var String
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
	private static function get_random_message()
	{
		return self::$messages[mt_rand(0, count(self::$messages) - 1)];
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
		
		if ( $this->winner instanceof Character )
		{
			$this->add_blank_space();
			
			if ( $this->loser instanceof Npc )
			{
				$winnerExperience = $this->loser->xp * Config::get('game.xp_rate');
				
				// Actualizamos db
				$this->winner->xp += $winnerExperience;
				$this->winner->points_to_change += $winnerExperience;
			}
			else
			{
				// Experiencia del ganador
				if ( $this->winner->level <= $this->loser->level )
				{
					$winnerExperience = (3 + ($this->loser->level - $this->winner->level)) * Config::get('game.xp_rate');
				}
				
				// Experiencia del perdedor
				$loserExperience = 1 * Config::get('game.xp_rate');
				
				// Actualizamos en db
				$this->winner->xp += $winnerExperience;
				$this->winner->points_to_change += $winnerExperience;
				
				$this->loser->xp += $loserExperience;
				$this->loser->points_to_change += $loserExperience;
				
				$this->add_message_to_log($this->loser->name . ' recibe ' . $loserExperience . ' punto(s) de experiencia.', true);
			}
			
			$this->add_message_to_log($this->winner->name . ' recibe ' . $winnerExperience . ' punto(s) de experiencia.', true);
		}
	}
	
	private function give_rewards()
	{
		// Le damos 2 puntos a la barra de actividad
		// del atacante
		ActivityBar::add($this->_attacker, 2);
		
		if ( $this->winner instanceof Character )
		{
			$coins = (50 * $this->loser->level) * Config::get('game.coins_rate');
			$percentage = 0;
			
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
					}
					else
					{
						// Si el ganador tiene mas nivel que el perdedor
						// entonces cada nivel de diferencia lo restamos al porcentaje
						$percentage = 0.25 - ($this->winner->level - $this->loser->level) * 0.01;
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
			$this->add_message_to_log($this->winner->name . ' recibe ' . $coins . ' moneda(s) (' . $percentage . '% robado del enemigo).', true);
			
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
                if ( count($attackedOrbs) > 0 && $this->winner->orbs()->count() < 2 )
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
		$info['is_warrior'] = $info['stats']['stat_strength'] > $info['stats']['stat_magic'];
		$info['current_life'] = ( $info['is_player'] ) ? $unit->current_life : $unit->life;
		
		if ( $info['is_warrior'] )
		{
			$info['cd'] = 1000 / ($info['stats']['stat_dexterity'] + 1);
		}
		else
		{
			$info['cd'] = 1000 / ($info['stats']['stat_magic_skill'] + 1);
		}
		
		$info['current_cd'] = $info['cd'];
		
		// Daños
		// --------------------------------
		//    VER ESTA PARTE, ¡RE-HACER!
		// --------------------------------
		$info['min_damage'] = ( $info['is_warrior'] ) ? $info['stats']['stat_strength'] : $info['stats']['stat_magic'];
		$info['max_damage'] = $info['min_damage'];
		
		$info['min_damage'] *= 0.25;
		$info['max_damage'] *= 0.75;
		
		// Defensas
		// --------------------------------
		//    VER ESTA PARTE, ¡RE-HACER!
		// --------------------------------
		$info['min_defense'] = ( $info['is_warrior'] ) ? $info['stats']['stat_resistance'] : $info['stats']['stat_magic_resistance'];
		$info['max_defense'] = $info['min_defense'];
		
		$info['min_defense'] *= 0.75;
		$info['max_defense'] *= 1.25;
		
		if ( $info['min_defense'] > $info['max_defense'] )
		{
			$info['max_defense'] = $info['min_defense'];
		}
		
		// Log
		$info['initial_life'] = $info['current_life'];
		$info['damage_done'] = 0;
		
		return $info;
	}
	
	private function to_battle()
	{
		$attackerStats = self::get_unit_info($this->_attacker);
		$attackedStats = self::get_unit_info($this->_attacked);
		
		$attacker;
		$defender;
		
		$damage = 0;
		$defense = 0;
		$realDamage = 0;
		
		while ( $attackerStats['current_life'] > 0 && $attackedStats['current_life'] > 0 )
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
			
			// Calculamos el daño
			$attacker['average_damage'] = mt_rand($attacker['min_damage'], $attacker['max_damage']);

			// 35% de crítico físico
			if ( $attacker['is_warrior'] && mt_rand(0, 100) <= 35 )
			{
				$damage = $attacker['average_damage'] * 1.50;
				//self::on_excellent_attack_warrior($attacker, $defender, $damage);
			}
			// 25% de crítico mágico
			elseif ( ! $attacker['is_warrior'] && mt_rand(0, 100) <= 25 )
			{
				$damage = $attacker['average_damage'] * 2.50;
				//self::on_excellent_attack_mage($attacker, $defender, $damage);
			}
			// 10% de golpe fallido
			elseif ( mt_rand(0, 100) <= 10 )
			{
				$damage = $attacker['average_damage'] * 0.75;
				//self::on_poor_attack($attacker, $defender, $damage);
			}
			else
			{
				$damage = $attacker['average_damage'];
				//self::on_normal_attack($attacker, $defender, $damage);
			}
			
			// Calculamos la defensa
			$defender['average_defense'] = mt_rand($defender['min_defense'], $defender['max_defense']);
			
			// 30% de defensa exitosa
			if ( mt_rand(0, 100) <= 30 )
			{
				$defense = $defender['average_defense'] * 1.75;
				//self::on_excellent_defense($attacker, $defender, $defense);
			}
			// 10% de defensa fallida
			elseif ( mt_rand(0, 100) <= 10 )
			{
				$defense = $defender['average_defense'] * 0.75;
				//self::on_poor_defense($attacker, $defender, $defense);
			}
			else
			{
				$defense = $defender['average_defense'];
				//self::on_normal_defense($attacker, $defender, $defense);
			}
			
			// Calculamos el daño verdadero
			$realDamage = min($damage - $defense * 0.4, $defender['current_life']);

			// Evitamos que un daño negativo
			// cure al oponente
			if ( $realDamage < 0 )
			{
				$realDamage = 0;
			}
			
			// HIT!
			//self::before_hit($attacker, $defender, $realDamage);
			
			$defender['current_life'] -= $realDamage;
			$attacker['damage_done'] += $realDamage;
			
			//self::after_hit($attacker, $defender, $realDamage);
			
			$this->add_message_to_log(
				sprintf(
					self::get_random_message(), 
					$attacker['name'], 
					$defender['name']
				) . ' (daño: ' . number_format($realDamage, 2) . ', defendido: ' . number_format($damage - $realDamage, 2) . ')'
			);
		}
		
		// Verificamos si no fue un empate
		if ( $attackerStats['current_life'] <= 0 && $attackedStats['current_life'] <= 0 )
		{
			$this->winner = null;
			$this->loser = null;
		}
		else
		{
			if ( $attackerStats['current_life'] > 0 )
			{
				$this->winner = $this->_attacker;
				$this->loser = $this->_attacked;
			}
			else
			{
				$this->winner = $this->_attacked;
				$this->loser = $this->_attacker;
			}
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

		$this->_attacker->current_life = $attackerStats['current_life'];
		$this->_attacker->save();
		
		if ( $attackedStats['is_player'] )
		{
			$this->_attacked->current_life = $attackedStats['current_life'];
			$this->_attacked->save();
		}
		
		// Disparamos el evento de batalla
		if ( $attackedStats['is_player'] )
		{
			Event::fire('battle', array($this->_attacker, $this->_attacked));
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
	 */
	public function __construct(Eloquent $attacker, Eloquent $attacked)
	{		
		$this->log = new BattleLog();
		
		$this->_attacker = $attacker;
		$this->_attacked = $attacked;
		
		$this->to_battle();
		
		$this->log->message = '<ul class="unstyled">' . $this->log->message . '</ul>';
		$this->log->save();
		
		$attacker->after_battle();
	}
}