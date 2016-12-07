<?php
namespace Slim;

class SlimC extends Slim
{
  /*
  Example usage
      $app->controller(
          '/reports',
          'ReportsController',
          array(
              'GET /' => 'getIndex',
              'GET /:id name2' => 'showReport',
              'GET,POST /page/:var name3' => 'getPageWithVar'
          )
      );
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

            $route = new Route($finalRoute, function() use(
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
            //echo (!empty($name) ? $name : $controllerName . '.' . $controllerFunction)."<br/>"; print_r($finalRoute); echo "<br/>";
            $route->name(!empty($name) ? $name : $controllerName . '.' . $controllerFunction);
            //$route->name($controllerName . '.' . $controllerFunction);
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
            //$this->basePath = $baseRoute;
            $this->basePath = '/';
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
        //print_r($this->vars);
        $vars = array_merge($this->vars, $extraVars);
        $templatePath = substr($this->basePath, 1) . '/' .  $template;
        $this->app->render($templatePath, $vars);
    }
}
