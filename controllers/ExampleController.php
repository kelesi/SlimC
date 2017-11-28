<?php
namespace Example\Controllers;

use \SlimC\Controller;

class ExampleController extends Controller
{
	protected $basePath = '/example'; // optional

	protected function init()
	{
		$this->vars = array(
			'my_var' => 'is shared in all sub-routes',
			'route_name' => $this->app->router->getCurrentRoute()->getName()
		);
	}

	public function getIndex()
	{
		$this->render('home.php');
	}

	public function getSecondIndex()
	{
		$this->render('home.php');
		echo '<p>With $basePath set, our views will always be called from the same directory.</p>';
		echo '<p>Without $basePath, it will look in the same folder as the URI.</p>';
	}

	public function getPage()
	{
		extract($this->vars);
		echo '<p>A page</p>';
		echo '<p>Route name: ' . $route_name . '</p>';
	}

	public function getPageWithVar($var)
	{
		extract($this->vars);
		echo '<p>A page with a var: '. $var . '</p>';
		echo '<p>Route name: ' . $route_name . '</p>';

	}

	public function getPageById($id)
	{
		extract($this->vars);
		echo '<p>A page with a requested id: '. $id . '</p>';
		echo '<p>Route name: ' . $route_name . '</p>';

	}    
}