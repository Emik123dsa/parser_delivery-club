<?php 

namespace API\Config; 

class Repository 
{
    /**
     * Protected
     *
     * @var array
     */
    protected static $stored = []; 
    /**
     * Store
     *
     * @param [type] $group
     * @param [type] $key
     * @param [type] $data
     * @return void
     */
    public static function store($group, $key, $data) 
    {
        if (!isset(static::$stored) || !is_array(static::$stored)) {
            static::$stored[$group] = []; 
        }

        static::$stored[$group][$key] = $data; 
    }
    /**
     * Retireiver by keys
     *
     * @param [type] $group
     * @param [type] $key
     * @return void
     */
    public static function retrieve($group, $key) 
    {
        return isset(static::$stored[$group][$key]) ? static::$stored[$group][$key] : [];
    }
    /**
     * Retriever by Groups
     *
     * @param [type] $group
     * @return void
     */
    public static function retrieveByGroup($group) 
    {
        return isset(static::$stored[$group]) ? static::$stored[$group] : [];
    }
}

?>