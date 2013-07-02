<?php
namespace Slim;

class SlimC extends Slim
{
    public function controller($baseRoute, $controllerName, $routes)
    {
        $controllerClass = $this->config('controller.namespace') . '\\' . $controllerName;

        foreach ($routes as $subRoute => $controllerFunction) {
            list($methods, $subRoute) = explode(' ', $subRoute);
           
            if ($subRoute == '/') { $subRoute = ''; }

            $finalRoute = $baseRoute . $subRoute;

            $methodArray = explode(',', $methods);

            $route = new Route($finalRoute, function() use(
                $baseRoute,
                $controllerClass,
                $controllerFunction
            ) {
                $controllerInstance = $controllerClass::getInstance($baseRoute);

                $args = func_get_args();
                call_user_func_array(array($controllerInstance, $controllerFunction), $args );
            });

            $this->router->map($route);

            call_user_func_array(array($route, 'via'), $methodArray);

            $route->name($controllerName . '.' . $controllerFunction);
        }
    }
}

abstract class Controller
{
    private static $instance;
    protected static $baseRoute;

    protected $app, $basePath;

    final private function __construct($baseRoute)
    {
        self::$baseRoute = $baseRoute;

        $this->app = Slim::getInstance();
        
        if (empty($this->basePath)) {
            $this->basePath = $baseRoute;
        }

        $this->init();
    }

    abstract protected function init();

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

    protected function render($template, $extraVars = array())
    {
        $vars = array_merge($this->vars, $extraVars);
        $templatePath = substr($this->basePath, 1) . '/' .  $template;
        $this->app->render($templatePath, $vars);
    }
}
