<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Twigの設定
	 *
	 */
    protected function _initTwigView()
    {
    	$options["zf"]   = array();
    	$options["twig"] = $this->getOption("twig");
        $renderer = Zend_Controller_Action_HelperBroker::getStaticHelper("viewRenderer");

		// TwigViewインスタンス
        $view = new Nully_View_Twig($options);
        $view->addExtension(new Twig_Extension_Escaper());
		$view->addExtension(new Nully_View_Extension(true));

		// view エンジンを上書き
        $renderer->setView($view)
                 ->setViewSuffix("html");
    }

}

