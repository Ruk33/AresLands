<?php namespace Models\Vip;

use Character;

interface VipRepository
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return string 
     */
    public function getDescription();
    
    /**
     * @return string Path a la imagen del icono
     */
    public function getIcon();
    
    /**
     * @return integer Precio de monedas VIP
     */
    public function getPrice();
    
    /**
     * @return boolean
     */
    public function isEnabled();
    
    /**
     * @param Character $buyer
     * @param array $attributes
     * @return VipImplementation
     */
    public function getVipImplementation(Character $buyer = null, 
                                         array $attributes = array());
}