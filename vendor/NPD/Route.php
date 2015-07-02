<?php
namespace NPD;
use Closure;
use stdClass;
class Route{
    /**
    * current route of the request
    * @var string
    */
    private $currentRoute;
    /**
    * collection of accepted routes
    *
    * @var array
    */
    private $routesCollection = array();
    /**
    * container of controller names
     * that will be loaded before | after main route loads
    *
    * @var array
    */
    private $calls = array();
    /**
    * Request Object
    * @var Request
    */
    private $request;

    /**
    * Constructor
    * @param Request $request
    */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * add a new route for expected path to routes collection
    *
    * @return void
    */
    public function setRoute($route)
    {
        $routeDetails = new stdClass;
        if($route instanceof Closure)
        {
            $routeDetails = call_user_func($route  , $routeDetails);

            if(!property_exists($routeDetails , 'controller'))
            {
                die('please add controller attribute for all your routes collection');
            }

            if(!property_exists($routeDetails , 'path') AND !property_exists($routeDetails , 'paths'))
            {
                die('path attribute is missing in your routes collection for ' .  $routeDetails->controller);
            }
            if(property_exists($routeDetails , 'path'))
            {
                // prepare the pattern for the path
                $pattern = '#^';

                $path = trim(str_replace('//' , '/' , $routeDetails->path) , '/');

                $pattern .= $path . '/'; // #^posts/

                // if there is an expected parameters
                if(property_exists($routeDetails , 'parameters'))
                {
                    foreach((array)  $routeDetails->parameters AS $parameter)
                    {
                        if($parameter == 'int')
                        {
                            $pattern .= '\d+/';
                        }
                        elseif($parameter == 'float')
                        {
                            $pattern .= '\f+/';
                        }
                        elseif($parameter == 'string')
                        {
                            $pattern .= '[a-zA-Z0-9-_+]+/';
                        }
                    }
                }
                $pattern .= '$#ui';
                $this->routesCollection[$pattern] = $routeDetails;
            }
        }
        else
        {
            $route = func_get_args();
        }
    }

    public function build()
    {
        $route = implode('/' , $this->request->getRoute()) . '/';
        foreach($this->routesCollection AS $pattern => $details)
        {
            if(preg_match($pattern , $route))
            {
                echo $pattern;
            }
        }
    }
}