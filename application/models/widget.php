<?php

/**
 * Todo desciende de Widget, Widget lo es todo, alabad a Widget mortales
 * 
 * Widget es la clase madre de todos los elementos (modelos) que se encontrarán
 * (objetos, npc, etc.).
 * 
 * @abstract
 */
abstract class Widget extends Base_Model
{
    /**
     * @return string Tooltip con descripcion detallada del widget
     */
    public abstract function get_tooltip();
    
    /**
     * Devolvemos una cadena vacia en algunos casos... de momento
     * (por ejemplo para los monstruos nos falta el bestiario)
     * 
     * @return string Link
     */
    public function get_link()
    {
        return '';
    }
    
    /**
     * Obtenemos la ruta para la imagen
     * @return string
     */
    public abstract function get_image_path();
    
    /**
     * Obtenemos la fuerza final (sumando o restando todo dependiendo de buffs)
     * @return real
     */
    public function get_final_strength()
    {
        return $this->stat_strength;
    }
    
    /**
     * Obtenemos la destreza final (sumando o restando todo dependiendo de buffs)
     * @return real
     */
    public function get_final_dexterity()
    {
        return $this->stat_dexterity;
    }
    
    /**
     * Obtenemos la resistencia fisica final (sumando o restando todo dependiendo de buffs)
     * @return real
     */
    public function get_final_resistance()
    {
        return $this->stat_resistance;
    }
    
    /**
     * Obtenemos la magia final (sumando o restando todo dependiendo de buffs)
     * @return real
     */
    public function get_final_magic()
    {
        return $this->stat_magic;
    }
    
    /**
     * Obtenemos la habilidad magica final (sumando o restando todo dependiendo de buffs)
     * @return real
     */
    public function get_final_magic_skill()
    {
        return $this->stat_magic_skill;
    }
    
    /**
     * Obtenemos la resistencia magica final (sumando o restando todo dependiendo de buffs)
     * @return real
     */
    public function get_final_magic_resistance()
    {
        return $this->stat_magic_resistance;
    }
    
    /**
     * Obtenemos array con las habilidades del widget
     * @return array
     */
    public function get_skills()
    {
        return array();
    }
}