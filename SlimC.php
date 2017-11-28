<?php
/**
 * SlimC - Simple controller for Slim 2.x
 *
 * @version
 * @package     SlimC
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace SlimC;

use \Slim\Slim;
use \Slim\Route;

/*
 * Slim controller
 */
class SlimC extends Slim
{
    /**
     * Map a route to a controller class
     * @param string $baseRoute Base route for the whole class
     * @param string $controllerName Name of the controller class
     * @param type $routes Array defining the routes nad mapping to methods. Format: array("METHOD route [name]" => "methodName")
     * @param type $conditions Array of Slim conditions for route variables
     */
    public function controller($baseRoute, $controllerName, $routes, $conditions=array())
    {
        $controllerClass = $this->config('controller.namespace') . '\\' . $controllerName;

        foreach ($routes as $sub => $controllerFunction) {
            $arr = explode(' ', $sub);
            $methods    = !empty($arr[0]) ? $arr[0] : "";
            $subRoute   = !empty($arr[1]) ? $arr[1] : "";
            $name       = !empty($arr[2]) ? $arr[2] : "";

            if ($subRoute == '/') { $subRoute = ''; }

            $finalRoute = $baseRoute . $subRoute;

            $methodArray = explode(',', $methods);

            $route = new Route($finalRoute, function() use (
                $baseRoute,
                $controllerClass,
                $controllerFunction
            ) {
                $controllerInstance = $controllerClass::getInstance($baseRoute);

                $args = func_get_args();
                call_user_func_array(array($controllerInstance, $controllerFunction), $args );
            });

            //Set conditions if any
            $route->conditions($conditions);

            //Map the routes            
            $this->router->map($route);
            call_user_func_array(array($route, 'via'), $methodArray);

            //Custom names for routes otherwise use controllername and function
            $route->name(!empty($name) ? $name : $controllerName . '.' . $controllerFunction);
        }
    }
}

/**
 * Abstract class used as parent for custom controller classes
 */
abstract class Controller
{
    /**
     * @var \Slim\Controller Controller instance
     */
    private static $instance;

    /**
     * @var string Base route associated with this controller instance
     */
    protected static $baseRoute;

    /**
     *
     * @var \Slim\Slim Slim app
     */
    protected $app;

    /**
     * @var string Base path used when rendering templates
     */
    protected $basePath;

    /**
     * Constructor
     * @param string $baseRoute Setup optional base route
     */
    final private function __construct($baseRoute)
    {
        self::$baseRoute = $baseRoute;

        $this->app = Slim::getInstance();

        if (empty($this->basePath)) {
            //$this->basePath = $baseRoute;
            $this->basePath = '/';
        }

        $this->init();
    }

    /**
     * Mandatory method called upon costruction
     */
    abstract protected function init();

    /**
     * Gets the instance of the controller class for specified route
     * @param string $baseRoute Base route associated with the controller
     * @return \Slim\Controller Controller instance
     * @throws Exception
     */
    public final static function getInstance($baseRoute)
    {
        if (!isset(self::$instance)) {
            $class = get_called_class();
            self::$instance = new $class($baseRoute);
        }

        if (self::$instance instanceof $class) {
            return self::$instance;
        } else {
            throw new Exception('Only one controller may be active at the time.');
        }
    }

    /**
     * Renders the template with extra vars
     * @param string $template Template relative path and filename
     * @param array $extraVars Additional variables to pass to the template
     */
    protected function render($template, $extraVars = array())
    {
        $vars = array_merge($this->vars, $extraVars);
        $templatePath = substr($this->basePath, 1).'/'.$template;
        $this->app->render($templatePath, $vars);
    }
}
