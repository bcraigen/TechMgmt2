<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP Project
 * @package       Cake.Test.Case.Controller
 * @since         CakePHP(tm) v 1.2.0.5436
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('Router', 'Routing');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('SecurityComponent', 'Controller/Component');
App::uses('CookieComponent', 'Controller/Component');

/**
 * AppController class
 *
 * @package       Cake.Test.Case.Controller
 */
class ControllerTestAppController extends Controller {

/**
 * helpers property
 *
 * @var array
 */
	public $helpers = array('Html');

/**
 * uses property
 *
 * @var array
 */
	public $uses = array('ControllerPost');

/**
 * components property
 *
 * @var array
 */
	public $components = array('Cookie');
}

/**
 * ControllerPost class
 *
 * @package       Cake.Test.Case.Controller
 */
class ControllerPost extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'posts';

/**
 * invalidFields property
 *
 * @var array
 */
	public $invalidFields = array('name' => 'error_msg');

/**
 * lastQuery property
 *
 * @var mixed
 */
	public $lastQuery = null;

/**
 * beforeFind method
 *
 * @param mixed $query
 * @return void
 */
	public function beforeFind($query) {
		$this->lastQuery = $query;
	}

/**
 * find method
 *
 * @param string $type
 * @param array $options
 * @return void
 */
	public function find($type = 'first', $options = array()) {
		if ($type === 'popular') {
			$conditions = array($this->name . '.' . $this->primaryKey . ' > ' => '1');
			$options = Hash::merge($options, compact('conditions'));
			return parent::find('all', $options);
		}
		return parent::find($type, $options);
	}

}

/**
 * ControllerPostsController class
 *
 * @package       Cake.Test.Case.Controller
 */
class ControllerCommentsController extends ControllerTestAppController {

	protected $_mergeParent = 'ControllerTestAppController';
}

/**
 * ControllerComment class
 *
 * @package       Cake.Test.Case.Controller
 */
class ControllerComment extends CakeTestModel {

/**
 * name property
 *
 * @var string
 */
	public $name = 'Comment';

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'comments';

/**
 * data property
 *
 * @var array
 */
	public $data = array('name' => 'Some Name');

/**
 * alias property
 *
 * @var string
 */
	public $alias = 'ControllerComment';
}

/**
 * ControllerAlias class
 *
 * @package       Cake.Test.Case.Controller
 */
class ControllerAlias extends CakeTestModel {

/**
 * alias property
 *
 * @var string
 */
	public $alias = 'ControllerSomeAlias';

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'posts';
}

/**
 * NameTest class
 *
 * @package       Cake.Test.Case.Controller
 */
class NameTest extends CakeTestModel {

/**
 * name property
 * @var string
 */
	public $name = 'Name';

/**
 * useTable property
 * @var string
 */
	public $useTable = 'comments';

/**
 * alias property
 *
 * @var string
 */
	public $alias = 'Name';
}

/**
 * TestController class
 *
 * @package       Cake.Test.Case.Controller
 */
class TestController extends ControllerTestAppController {

/**
 * helpers property
 *
 * @var array
 */
	public $helpers = array('Session');

/**
 * components property
 *
 * @var array
 */
	public $components = array('Security');

/**
 * uses property
 *
 * @var array
 */
	public $uses = array('ControllerComment', 'ControllerAlias');

	protected $_mergeParent = 'ControllerTestAppController';

/**
 * index method
 *
 * @param mixed $testId
 * @param mixed $test2Id
 * @return void
 */
	public function index($testId, $testTwoId) {
		$this->data = array(
			'testId' => $testId,
			'test2Id' => $testTwoId
		);
	}

/**
 * view method
 *
 * @param mixed $testId
 * @param mixed $test2Id
 * @return void
 */
	public function view($testId, $testTwoId) {
		$this->data = array(
			'testId' => $testId,
			'test2Id' => $testTwoId
		);
	}

	public function returner() {
		return 'I am from the controller.';
	}

	//@codingStandardsIgnoreStart
	protected function protected_m() {
	}

	private function private_m() {
	}

	public function _hidden() {
	}
	//@codingStandardsIgnoreEnd

	public function admin_add() {
	}

}

/**
 * TestComponent class
 *
 * @package       Cake.Test.Case.Controller
 */
class TestComponent extends Object {

/**
 * beforeRedirect method
 *
 * @return void
 */
	public function beforeRedirect() {
	}

/**
 * initialize method
 *
 * @return void
 */
	public function initialize(Controller $controller) {
	}

/**
 * startup method
 *
 * @return void
 */
	public function startup(Controller $controller) {
	}

/**
 * shutdown method
 *
 * @return void
 */
	public function shutdown(Controller $controller) {
	}

/**
 * beforeRender callback
 *
 * @return void
 */
	public function beforeRender(Controller $controller) {
		if ($this->viewclass) {
			$controller->viewClass = $this->viewclass;
		}
	}

}

class Test2Component extends TestComponent {

	public $model;

	public function __construct(ComponentCollection $collection, $settings) {
		$this->controller = $collection->getController();
		$this->model = $this->controller->modelClass;
	}

	public function beforeRender(Controller $controller) {
		return false;
	}

}

/**
 * AnotherTestController class
 *
 * @package       Cake.Test.Case.Controller
 */
class AnotherTestController extends ControllerTestAppController {

/**
 * uses property
 *
 * @var array
 */
	public $uses = false;

/**
 * merge parent
 *
 * @var string
 */
	protected $_mergeParent = 'ControllerTestAppController';
}

/**
 * ControllerTest class
 *
 * @package       Cake.Test.Case.Controller
 */
class ControllerTest extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = array(
		'core.post',
		'core.comment'
	);

/**
 * reset environment.
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		App::objects('plugin', null, false);
		App::build();
		Router::reload();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		CakePlugin::unload();
	}

/**
 * testLoadModel method
 *
 * @return void
 */
	public function testLoadModel() {
		$request = new CakeRequest('controller_posts/index');
		$response = $this->getMock('CakeResponse');
		$Controller = new Controller($request, $response);

		$this->assertFalse(isset($Controller->ControllerPost));

		$result = $Controller->loadModel('ControllerPost');
		$this->assertTrue($result);
		$this->assertInstanceOf('ControllerPost', $Controller->ControllerPost);
		$this->assertContains('ControllerPost', $Controller->uses);
	}

/**
 * Test loadModel() when uses = true.
 *
 * @return void
 */
	public function testLoadModelUsesTrue() {
		$request = new CakeRequest('controller_posts/index');
		$response = $this->getMock('CakeResponse');
		$Controller = new Controller($request, $response);
		$Controller->uses = true;

		$Controller->loadModel('ControllerPost');
		$this->assertInstanceOf('ControllerPost', $Controller->ControllerPost);
		$this->assertContains('ControllerPost', $Controller->uses);
	}

/**
 * testLoadModel method from a plugin controller
 *
 * @return void
 */
	public function testLoadModelInPlugins() {
		App::build(array(
			'Plugin' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
			'Controller' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Controller' . DS),
			'Model' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Model' . DS)
		));
		CakePlugin::load('TestPlugin');
		App::uses('TestPluginAppController', 'TestPlugin.Controller');
		App::uses('TestPluginController', 'TestPlugin.Controller');

		$Controller = new TestPluginController();
		$Controller->plugin = 'TestPlugin';
		$Controller->uses = false;

		$this->assertFalse(isset($Controller->Comment));

		$result = $Controller->loadModel('Comment');
		$this->assertTrue($result);
		$this->assertInstanceOf('Comment', $Controller->Comment);
		$this->assertTrue(in_array('Comment', $Controller->uses));

		ClassRegistry::flush();
		unset($Controller);
	}

/**
 * testConstructClasses method
 *
 * @return void
 */
	public function testConstructClasses() {
		$request = new CakeRequest('controller_posts/index');

		$Controller = new Controller($request);
		$Controller->uses = array('ControllerPost', 'ControllerComment');
		$Controller->constructClasses();
		$this->assertInstanceOf('ControllerPost', $Controller->ControllerPost);
		$this->assertInstanceOf('ControllerComment', $Controller->ControllerComment);

		$this->assertEquals('Comment', $Controller->ControllerComment->name);

		unset($Controller);

		App::build(array('Plugin' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS)));
		CakePlugin::load('TestPlugin');

		$Controller = new Controller($request);
		$Controller->uses = array('TestPlugin.TestPluginPost');
		$Controller->constructClasses();

		$this->assertTrue(isset($Controller->TestPluginPost));
		$this->assertInstanceOf('TestPluginPost', $Controller->TestPluginPost);
	}

/**
 * testConstructClassesWithComponents method
 *
 * @return void
 */
	public function testConstructClassesWithComponents() {
		$Controller = new TestPluginController(new CakeRequest(), new CakeResponse());
		$Controller->uses = array('NameTest');
		$Controller->components[] = 'Test2';

		$Controller->constructClasses();
		$this->assertEquals('NameTest', $Controller->Test2->model);
		$this->assertEquals('Name', $Controller->NameTest->name);
		$this->assertEquals('Name', $Controller->NameTest->alias);
	}

/**
 * testAliasName method
 *
 * @return void
 */
	public function testAliasName() {
		$request = new CakeRequest('controller_posts/index');
		$Controller = new Controller($request);
		$Controller->uses = array('NameTest');
		$Controller->constructClasses();

		$this->assertEquals('Name', $Controller->NameTest->name);
		$this->assertEquals('Name', $Controller->NameTest->alias);

		unset($Controller);
	}

/**
 * testFlash method
 *
 * @return void
 */
	public function testFlash() {
		$request = new CakeRequest('controller_posts/index');
		$request->webroot = '/';
		$request->base = '/';

		$Controller = new Controller($request, $this->getMock('CakeResponse', array('_sendHeader')));
		$Controller->flash('this should work', '/flash');
		$result = $Controller->response->body();

		$expected = '<!DOCTYPE html>
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>this should work</title>
		<style><!--
		P { text-align:center; font:bold 1.1em sans-serif }
		A { color:#444; text-decoration:none }
		A:HOVER { text-decoration: underline; color:#44E }
		--></style>
		</head>
		<body>
		<p><a href="/flash">this should work</a></p>
		</body>
		</html>';
		$result = str_replace(array("\t", "\r\n", "\n"), "", $result);
		$expected = str_replace(array("\t", "\r\n", "\n"), "", $expected);
		$this->assertEquals($expected, $result);

		App::build(array(
			'View' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS)
		));
		$Controller = new Controller($request);
		$Controller->response = $this->getMock('CakeResponse', array('_sendHeader'));
		$Controller->flash('this should work', '/flash', 1, 'ajax2');
		$result = $Controller->response->body();
		$this->assertRegExp('/Ajax!/', $result);
		App::build();
	}

/**
 * testControllerSet method
 *
 * @return void
 */
	public function testControllerSet() {
		$request = new CakeRequest('controller_posts/index');
		$Controller = new Controller($request);

		$Controller->set('variable_with_underscores', null);
		$this->assertTrue(array_key_exists('variable_with_underscores', $Controller->viewVars));

		$Controller->viewVars = array();
		$viewVars = array('ModelName' => array('id' => 1, 'name' => 'value'));
		$Controller->set($viewVars);
		$this->assertTrue(array_key_exists('ModelName', $Controller->viewVars));

		$Controller->viewVars = array();
		$Controller->set('variable_with_underscores', 'value');
		$this->assertTrue(array_key_exists('variable_with_underscores', $Controller->viewVars));

		$Controller->viewVars = array();
		$viewVars = array('ModelName' => 'name');
		$Controller->set($viewVars);
		$this->assertTrue(array_key_exists('ModelName', $Controller->viewVars));

		$Controller->set('title', 'someTitle');
		$this->assertSame($Controller->viewVars['title'], 'someTitle');

		$Controller->viewVars = array();
		$expected = array('ModelName' => 'name', 'ModelName2' => 'name2');
		$Controller->set(array('ModelName', 'ModelName2'), array('name', 'name2'));
		$this->assertSame($expected, $Controller->viewVars);

		$Controller->viewVars = array();
		$Controller->set(array(3 => 'three', 4 => 'four'));
		$Controller->set(array(1 => 'one', 2 => 'two'));
		$expected = array(3 => 'three', 4 => 'four', 1 => 'one', 2 => 'two');
		$this->assertEquals($expected, $Controller->viewVars);
	}

/**
 * testRender method
 *
 * @return void
 */
	public function testRender() {
		App::build(array(
			'View' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS)
		), App::RESET);
		ClassRegistry::flush();
		$request = new CakeRequest('controller_posts/index');
		$request->params['action'] = 'index';

		$Controller = new Controller($request, new CakeResponse());
		$Controller->viewPath = 'Posts';

		$result = $Controller->render('index');
		$this->assertRegExp('/posts index/', (string)$result);

		$Controller->view = 'index';
		$result = $Controller->render();
		$this->assertRegExp('/posts index/', (string)$result);

		$result = $Controller->render('/Elements/test_element');
		$this->assertRegExp('/this is the test element/', (string)$result);
		$Controller->view = null;

		$Controller = new TestController($request, new CakeResponse());
		$Controller->uses = array('ControllerAlias', 'TestPlugin.ControllerComment', 'ControllerPost');
		$Controller->helpers = array('Html');
		$Controller->constructClasses();
		$Controller->ControllerComment->validationErrors = array('title' => 'tooShort');
		$expected = $Controller->ControllerComment->validationErrors;

		$Controller->viewPath = 'Posts';
		$result = $Controller->render('index');
		$View = $Controller->View;
		$this->assertTrue(isset($View->validationErrors['ControllerComment']));
		$this->assertEquals($expected, $View->validationErrors['ControllerComment']);

		$expectedModels = array(
			'ControllerAlias' => array('plugin' => null, 'className' => 'ControllerAlias'),
			'ControllerComment' => array('plugin' => 'TestPlugin', 'className' => 'ControllerComment'),
			'ControllerPost' => array('plugin' => null, 'className' => 'ControllerPost')
		);
		$this->assertEquals($expectedModels, $Controller->request->params['models']);

		ClassRegistry::flush();
		App::build();
	}

/**
 * test that a component beforeRender can change the controller view class.
 *
 * @return void
 */
	public function testComponentBeforeRenderChangingViewClass() {
		App::build(array(
			'View' => array(
				CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS
			)
		), true);
		$Controller = new Controller($this->getMock('CakeRequest'), new CakeResponse());
		$Controller->uses = array();
		$Controller->components = array('Test');
		$Controller->constructClasses();
		$Controller->Test->viewclass = 'Theme';
		$Controller->viewPath = 'Posts';
		$Controller->theme = 'TestTheme';
		$result = $Controller->render('index');
		$this->assertRegExp('/default test_theme layout/', (string)$result);
		App::build();
	}

/**
 * test that a component beforeRender can change the controller view class.
 *
 * @return void
 */
	public function testComponentCancelRender() {
		$Controller = new Controller($this->getMock('CakeRequest'), new CakeResponse());
		$Controller->uses = array();
		$Controller->components = array('Test2');
		$Controller->constructClasses();
		$result = $Controller->render('index');
		$this->assertInstanceOf('CakeResponse', $result);
	}

/**
 * test                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                