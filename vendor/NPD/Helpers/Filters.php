<?php
if(!function_exists('data'))
{
    /**
    * trim any whitespace from the given string
    * and convert any html tag to html entities
    *
    * @param string $data
    * @return string
    */
    function data($data)
    {
        if(is_array($data)) return $data;

        return trim(htmlspecialchars($data));
    }
}

if(!function_exists('filter_array'))
{
    /**
    * filter array or multi dimensional array if found with a function
    * note : this function is called by reference
    * so there is no return
    *
    * @ param array &$array
    * @ @param string $filter
    * @ @return void
    */
    function filter_array(array &$array , $filter = 'data')
    {
        foreach($array AS $key => $value)
        {
            if(is_array($value))
            {
                filter_array($value);
            }
            else
            {
                $value = $filter($value);
            }
            $array[$filter($key)] = $value;
        }
    }
}