<?php namespace Models\Vip;

use Character;
use IronFistUser;
use Laravel\Log;

class VipShop
{
    /**
     *
     * @var VipRepository
     */
    protected $vip;
    
    /**
     *
     * @var array
     */
    protected $errors;
    
    /**
     * 
     * @return VipRepository
     */
    public function getVip()
    {
        return $this->vip;
    }
    
    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * 
     */
    protected function resetErrors()
    {
        $this->errors = array();
    }
    
    /**
     * @param IronFistUser $user Usuario al que restaremos las monedas vip
     * @param Character $buyer Personaje al que daremos los beneficios del objeto vip
     * @param array $attributes
     * @return boolean
     */
    public function buy(IronFistUser $user, Character $buyer, array $attributes)
    {
        $this->resetErrors();
        
        if (! $this->vip) {
            return array("El objeto VIP no existe");
        }
        
        if (! $this->vip->isEnabled()) {
            return array("Ese objeto VIP ha sido desactivado");
        }
        
        $implementation = $this->vip->getVipImplementation($buyer, $attributes);
        $validator = $implementation->getValidator();
        
        if ($validator->fails()) {
            $this->errors = $validator->errors->all();
            return false;
        }
        
        if (! $user->consume_coins($this->vip->getPrice())) {
            $this->errors = array(
                "No tienes suficientes monedas VIP para efectuar la compra"
            );
            
            return false;
        }
        
        if (! $implementation->execute()) {
            Log::write(
                "ERROR VipShop", 
                "Se le restaron {$this->vip->getPrice()} monedas vip al usuario" .
                " {$user->name} al intentar comprar el objeto {$this->vip->getName()}" .
                " pero fallaron las acciones a ejecutar en el personaje {$buyer->name}."
            );
            
            $this->errors = array(
                "Ocurrio un error al procesar tu solicitud. Por favor notifica" .
                " a los administradores al respecto en el foro para solucionar" .
                " este inconveniente."
            );
            
            return false;
        }
        
        return true;
    }
    
    /**
     * 
     * @param VipRepository $vip
     */
    public function __construct(VipRepository $vip = null)
    {
        $this->vip = $vip;
        $this->resetErrors();
    }
}