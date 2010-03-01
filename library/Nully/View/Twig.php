<?php
/**
 * TwigをZend Frameworkで使うためのViewクラス
 *
 */
class Nully_View_Twig extends Zend_View_Abstract
{
	/**
	 *　Twig の設定配列
	 *
	 * @access private
	 * @var    Array
	 */
	private $_twigOptions = array(
		"debug"       => false,
		"trim_blocks" => false,
		"charset"     => "UTF-8",
		"base_template_class" => "Twig_Template",
		"auto_reload" => true,
		"cache"       => null
	);


	/**
	 * @var  Twig_Environment
	 */
	private $_twig = null;


	public function __construct($options = array())
	{
		if(!isset($options["zf"])) {
			$options["zf"] = array();
		}
		if(!isset($options["twig"])) {
			$options["twig"] = array();
		}

		parent::__construct($options["zf"]);
		$this->_twig = new Twig_Environment(null, array_merge($this->_twigOptions, $options["twig"]));


		if(isset($options["twig"]["extensions"])) {
			$exts = (array)$options["twig"]["extensions"];
			$this->setExtensions($exts);
		}
	}


	/**
	 * Twig_Extensionを継承したサブクラスを追加する
	 *
	 * @access public
	 * @param  Twig_Extension
	 * @return Nully_View_Twig
	 */
	public function addExtension(Twig_Extension $ext)
	{
		$this->_twig->addExtension($ext);
		return $this;
	}


	/**
	 * Twig_Extensionを継承したサブクラスを追加
	 *
	 * @access public
	 * @param  Array
	 * @return Nully_View_Twig
	 */
	public function addExtensions(array $exts)
	{
		foreach($exts as $key => $ext) {
			$this->addExtension($ext);
		}
		return $this;
	}


	/**
	 * 表示するためのファイルを組み立て
	 *
	 * @access protected
	 */
	protected function _run()
	{
		// 引数のファイルパスをview scriptまでのパスとcontroller/action.:suffixの形式に分断する
		$paths = $this->_parseFilePath(func_get_arg(0));

		// view scriptまでのパスを指定
		$this->_twig->setLoader(new Twig_Loader_Filesystem($paths["path"]));

		// 登録した配列の０番目のViewScriptをテンプレートとして読み込み
		$template = $this->_twig->loadTemplate($paths["file"]);

		// APPLICATION_ENVをTwigから利用出来るようにするため変数に入れておく
		$this->env = APPLICATION_ENV;
		echo $template->render($this->getVars());
	}


	/**
	 * 渡された引数を view scriptパスとcontroller/action.:suffix形式に分断する
	 *
	 * @access private
	 * @param  String
	 */
	private function _parseFilePath($path)
	{
		// view script パス
		$viewScriptPath = rtrim(realpath($path. "/../../"), DIRECTORY_SEPARATOR). "/";

		// ファイル名
		$fileName = ltrim(str_replace($viewScriptPath, "", $path));
		return array(
			"path" => $viewScriptPath,
			"file" => $fileName
		);
	}
}

