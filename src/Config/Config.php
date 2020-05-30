<?php 

namespace API\Config; 
use API\Config\Repository;

class Config 
{
    public static function item($group, $key) 
    {
        if (!Repository::retrieve($group, $key)) 
        {
            self::file($group);
        }

        return Repository::retrieve($group, $key);
    }

    public static function group($group) 
    {
        if (!Repository::retrieveByGroup($group)) 
        {
            self::file($group);
        }

        return Repository::retrieveByGroup($group);
    }

    public static function file(string $data) 
    {
        //$path 
        $path = path("entrance", $data); 
       
        if (file_exists($path)) 
        {
            $features = include_once $path; 

            if (is_array($features)) 
            {
                foreach($features as $feature => $value) 
                {
                    
                    Repository::store($data, $feature, $value);
                }
            } else {
                throw new \ErrorException(sprintf("Is'not being an array %s", $path)); 
            }
        } else {
            throw new \ErrorException(sprintf("File is not existing %s", $path));
        }
    }
}


?>