<?php

class controller {

	/**
	 * 
	 * @var string
	 * @access private
	 */
	var $action;
	var $controller;
	var $param = array();
	var $value = array();
	var $layout = true;
	var $layout_name = "default";
	var $title = "undefined";
	var $view;
	var $menu;
	var $msg_flash = array();
	var $javascript = array();
	var $code_javascript = array();
	var $js;
	var $data = array();
	var $ariane;
	var $ajax = false;
	var $error;
	var $html;

	/**
	 * Short description of method okh
	 *
	 * @access public
	 * @author Aurélien LEQUOY, <aurelien.lequoy@esysteme.com>

	 * @param string construct of controller
	 * @return boolean Success
	 * @access public
	 */
	function __construct($controller, $action, $param) {

		if (empty($_SERVER["argc"]))
		{
			if (empty($GLOBALS['_SYSTEM']['acl'][$GLOBALS['_SITE']['id_group']][$controller][$action])
				|| $GLOBALS['_SYSTEM']['acl'][$GLOBALS['_SITE']['id_group']][$controller][$action] != 1)
			{
				if ($controller !== "" && $action !== "")
				{


					$this->error = __("Acess denied") . " : $controller/$action";
					return;

					/*
					  $calledFrom = debug_backtrace();
					  echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
					  echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)<br />';
					  die("ACCESS DENIED in : $controller/$action");
					 */
				}
			}
		}


		$this->controller = $controller;
		$this->action = $action;
		$this->param = $param;
		$this->view = $action;
		$this->recursive = false;
	}

	function get_controller() {
		if (empty($this->controller)) // certainement un meilleur maniere de procéder
		{
			return;
		}
		include_once APP_DIR . DS . "controller" . DS . $this->controller . ".controller.php";

		$page = new $this->controller($this->controller, $this->action, $this->param);
		$this->param = json_decode($this->param);

		$this->title = $this->controller;
		$action = $this->action;

		$page->$action($this->param);
		$this->ajax = $page->ajax;
		$this->js = $page->get_javascript();
		$this->layout_name = $page->layout_name;
		$this->view = $page->view;
		$this->menu = $page->menu;


		if ($page->title !== "undefined")
		{
			$this->title = $page->title;
			$GLOBALS['_SITE']['title_page'] = $this->title;
		}
		if (!empty($page->ariane))
		{
			$this->ariane = $page->ariane;
			$GLOBALS['_SITE']['ariane'] = strip_tags($this->ariane);
		}
		$tab = $page->get();

		foreach ($tab as $key => $val)
		{
			${$key} = $val;
		}

		if (!$this->recursive)
		{

			$_VAR = Singleton::getInstance("variable");

			if (!$_VAR->_open)
			{
				ob_start(); //TODO 
			}

			if ($this->view)
			{
				include APP_DIR . DS . "view" . DS . $this->controller . DS . $this->view . ".view.php";
			}



			if (!$_VAR->_open)
			{
				$this->html = ob_get_contents();
				ob_clean();
			}
		}
		else
		{
			if ($this->view)
			{
				include APP_DIR . DS . "view" . DS . $this->controller . DS . $this->view . ".view.php";
			}
		}


		$GLOBALS['_DEBUG']->save($this->controller . "/" . $this->action);
	}

	function display() {


		if (empty($this->controller)) // certainement une meilleur maniere de procéder
		{
			return;
		}

		echo $this->html;
	}

	function set_layout() {
		$_VAR = Singleton::getInstance("variable");



		if (empty($this->html)) // certainement une meilleur maniere de procéder
		{

			set_flash("error", "Access denied", $this->error);
			header("location :" . LINK . "user/register/");
			return;
			die();
		}

		global $_LG, $_SITE;

		//$this->html = $_LG->getTranslation($this->html);

		$GLIALE_CONTENT = $this->html;
		$GLIALE_TITLE = $this->title;
		$GLIALE_ARIANE = $this->ariane;

		$_VAR = Singleton::getInstance("variable");

		ob_implicit_flush(false);
		ob_start();
		$_VAR->_open = true;
		include APP_DIR . DS . "layout" . DS . $this->layout_name . ".layout.php";

		if (!$this->ajax)
		{
			echo $this->js;
		}
		echo "</html>\n"; //TODO a mettre ailleurs

		$_VAR->_html = ob_get_clean();
		echo $_LG->getTranslation($_VAR->_html);
	}

	function get_javascript() {
		$js = "";

		foreach ($this->javascript as $script)
		{

			if (stristr($script, 'http://'))
			{
				$js .="<script type=\"text/javascript\" src=\"" . $script . "\"></script>\n";
			}
			else
			{
				$js .="<script type=\"text/javascript\" src=\"" . JS . $script . "\"></script>\n";
			}
		}

		$js .= "<script type=\"text/javascript\">\n";
		foreach ($this->code_javascript as $script)
		{
			$js .= $script;
		}

		$js .= "</script>\n";

		return $js;
	}

	function set($var, $valeur) {
		$this->value[$var] = $valeur;
	}

	function get() {
		return $this->value;
	}

}

?>
