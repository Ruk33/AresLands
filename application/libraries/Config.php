<?php namespace Libraries;

use \Laravel\IoC;

class Config extends \Laravel\Config
{
    /**
     * 
     * @param string $key
     * @param mixed $default
     * @param integer|null $serverId
     * @return mixed
     */
    public static function get($key, $default = null, $serverId = null)
    {
        if ($serverId) {
            $globalVariable = IoC::resolve("GlobalVariable")
                                 ->where_name($key)
                                 ->where_server_id($serverId)
                                 ->first();
            
            if ($globalVariable) {
                return $globalVariable->value;
            }
        }
        
        return parent::get($key, $default);
    }
}
