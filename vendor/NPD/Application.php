<?php
namespace NPD;
use Closure;
class Application
{
    /**
    * container of objects
    *
    * @var array
    */
    private $objects = array();
    /**
    * set special registers to store its values in customized way
    *
    * @var array
     */
    private $specials = array();
    /**
    * Constructor
    *
    */
    public function __construct(){}
    /**
    * initialize main settings
    *
    * @return void
    */
    public function bootstrap()
    {
        // load the method that is responsible for loading classes
        $this->loadClasses();
        // load main helpers files
        $this->loadHelpers();

        // initialize request class
        // and prepare its main settings
        $this->register('request' , new Request());

        // initialize route class
        $route = new Route($this->request);
        $this->register('route' , $route);
        // require the index file for the current script

        require ROOT . 'scripts' . DS . $this->request->getScriptName() . DS . 'index.php';

        // initialize session class
        $this->register('session' , function($app){
           return (new Session($app->request));
        });

        $this->route->build();
    }

    /**
    * get the required class from the object container
    *
    * @param string $key
    * @return mixed
    */
    public function call($class)
    {
        // if class object not in the object container then create a new object
        if(!$this->has($class))
        {
            if(in_array($class , $this->specials))
            {
                $method = 'register' . ucfirst($class);
                $this->{$method}();
            }
            else
            {
                $this->register($class);
            }
        }

        return $this->objects[$class];
    }

    /**
    * set an object to object container
    *
    * @param string $class
    * @param mixed $value
    * @return void
    */
    public function register($class , $value = null)
    {
        if(is_null($value))
        {
            $fullClassName = 'NPD\\' . ucfirst($class);
            $value = new $fullClassName();
        }
        elseif($value instanceof Closure)
        {
            $value = $this->executeCall($value);
        }

        $this->objects[$class] = $value;
    }

    /**
    * determine wither the object container has the given class name
    *
    * @return bool
    */
    public function has($class)
    {
        return isset($this->objects[$class]);
    }

    /**
    * set an object to object container
    *
    * @param string $key
    * @param mixed $value
    * @return void
    */
    public function __set($key , $value)
    {
        $this->register($key , $value);
    }
    /**
    * get the required class from the object container
    *
    * @param string $key
    * @return mixed
    */
    public function __get($key)
    {
        return $this->call($key);
    }

    /**
    * Register classes in the spl library
    *
    * @return void
    */
    private function loadClasses()
    {
        spl_autoload_register(array($this , 'loadClass'));
    }

    /**
    * require the file of the initialized object if found
    *
    * @param string $className
    * @return void
    */
    private function loadClass($className)
    {
        // if class name has a namespace separator
        // then replace it with directory separator
        if(strpos($className , '\\') !== false)
        {
            $className = str_replace('\\' , DS , $className);
        }

        if(file_exists($file = ROOT . 'vendor' . DS . $className.'.php'))
        {
            require $file;
        }
        else
        {
            die('class ' . $className . ' not found in ' . $file);
        }
    }

    /**
    * load main helpers file
    *
    * @return void
    */
    private function loadHelpers()
    {
        foreach(glob(ROOT . 'vendor' . DS . 'NPD' . DS . 'Helpers' .DS . '*.php') AS $file)
        {
            require $file;
        }
    }
    /**
    * execute a callable function
    *
    * @param callable $callable
    * @return mixed
    */
    private function executeCall(callable $callable )
    {
        return call_user_func($callable , $this);
    }
}
