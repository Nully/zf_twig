<?php
class IndexController extends Zend_Controller_Action
{
    public function init() {}


    public function indexAction()
    {
    	$this->view->name = "<br />ぬりーぬりーぬりーぬりーぬりーぬりーぬりーぬりー";
    }


    public function secondAction()
    {
    	$this->view->name = "my name is Nully !";
    }
}

