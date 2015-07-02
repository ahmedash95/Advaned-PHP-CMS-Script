<?php
namespace NPD;
class Request{
    /**
    * the route of the requested page
    *
    *  @var array
    */
    private $route = array();
    /**
    * the folder name of the current script
    *
    * @var string
    */
    private $script;

    /**
    * Constructor
    *
    */
    public function __construct()
    {
        // clear main super gobals from any unwanted codes
        $this->filterGlobals();
        $requestURI = $this->server('REQUEST_URI');
        $scriptName = dirname($this->server('SCRIPT_NAME'));
        if(strpos($requestURI , '?') !== false)
        {
            // then this uri contains a query string
            list($route , $queryString) = explode('?' , $requestURI);
        }
        else
        {
            $route = $requestURI;
        }

        // remove the main folder name of the application from the route
        // and remove the trailing slash
        $route = trim(str_replace($scriptName , '' , $route) , '/');

        // replace duplicated slashes with one slash
        $route = preg_replace('#/+#' , '/' , $route);

        $this->route = explode('/' , $route);

        $this->setProperScript();
    }
       /**
    * get a value from super global $_POST by key
    * otherwise return the default value
    *
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    public function post($key , $default = null)
    {
        return array_get($_POST , $key , $default);
    }

    /**
    * get a value from super global $_GET by key
    * otherwise return the default value
    *
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    public function get($key , $default = null)
    {
        return array_get($_GET , $key , $default);
    }

    /**
    * get a value from super global $_SERVER by key
    * otherwise return the default value
    *
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    public function server($key , $default = null)
    {
        return array_get($_SERVER , $key , $default);
    }

    /**
    * get the script name of the current request
    *
    * @return string
    */
    public function getScriptName()
    {
        return $this->script;
    }

    /**
    * get the route of the current request
    *
    * @return array
    */
    public function getRoute()
    {
        return $this->route;
    }

    /**
    * determine wether the request is sent Via SSL
    * checking if it uses https
    *
    * @return bool
    */
    public function isSecure()
    {
        return (bool) ($this->server('HTTPS') AND $this->server('HTTPS') == 'on');
    }

    /**
    * determine wether request method is post
    *
    * @return bool
    */
    public function isPost()
    {
        return (bool) $this->method() == 'POST';
    }

    /**
    * check if the request method is not post
    *
    * @return bool
    */
    public function isNotPost()
    {
        return !$this->isPost();
    }

    /**
    * get the request method of the request
    *
    * @return string
    */
    public function method()
    {
        return $this->server('REQUEST_METHOD');
    }

    /**
    * determine wether this request comes through ajax call
    *
    * @return bool
    */
    public function isAjax()
    {
       return (bool) strtolower($this->server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    /**
    * get the ip of the visitor
    *
    * @return string
    */
    public function ip()
    {
        // get th direct ip for the user
        $ip =  $this->server('REMOTE_ADDR');

        // if user uses proxy
        if ($this->server('HTTP_CLIENT_IP'))
        {
            $ip = $this->server('HTTP_CLIENT_IP');
        }
        elseif ($this->server('HTTP_X_FORWARDED_FOR'))
        {
            $ip = $this->server('HTTP_X_FORWARDED_FOR');
        }
        return $ip;
    }

    /**
    * get the language code of the accepted languages for the user
    *
    * @return array
    */
    public function langs()
    {
        $languageList = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        $languages = array();

        $languageRanges = explode(',', trim($languageList));

        foreach($languageRanges as $languageRange)
        {
            if(preg_match('/(\*|[a-zA-Z0-9]{1,8}(?:-[a-zA-Z0-9]{1,8})*)(?:\s*;\s*q\s*=\s*(0(?:\.\d{0,3})|1(?:\.0{0,3})))?/', trim($languageRange), $match))
            {
                if(!isset($match[2]))
                {
                    $match[2] = '1.0';
                }
                else
                {
                    $match[2] = (string) floatval($match[2]);
                }

                if(!isset($languages[$match[2]]))
                {
                    $languages[$match[2]] = array();
                }

                $languages[$match[2]][] = strtolower($match[1]);
            }
        }

        krsort($languages);

        return $languages;
    }

    /**
    * get scripts names from scripts.php file in config folder
    * then set the script name for the current request
    *
    * @return void
    */
    private function setProperScript()
    {
        // get scripts names
        if(!file_exists($scriptsFile = ROOT . 'config' . DS . 'scripts.php'))
        {
            die('Scripts file should be in config folder');
        }

        $scripts = require $scriptsFile;

        if(!array_key_exists('default' , $scripts))
        {
            die('default script key is missing from scripts file in ' . $scriptsFile);
        }

        // if the first element of the route exists in the scripts array key
        // then set the script name to be the value of the script key
        if(in_array($this->route[0] , array_keys($scripts)))
        {
            $this->script = $scripts[array_shift($this->route)];
        }
        else
        {
            $this->script = $scripts['default'];
        }

        // remove empty elements from route
        $this->route = array_filter($this->route , 'strlen');
    }

    /**
    * filter superglobals $_POST | $_GET for each request
    * @return void
    */
    private function filterGlobals()
    {
        foreach(array($_GET , $_POST) AS $global)
        {
            filter_array($global);
        }
    }

}