<?php
class Nully_View_Node extends Twig_Node
{
	/**
	 * @var    String     ViewHelper名
	 * @access private
	 */
	private $_method;


	/**
	 * @var    array     TokenParserで抽出した引数の配列
	 * @access private
	 */
	private $_params = array();


	/**
	 * constructor
	 *
	 * @access public
	 * @param  String    ViewHelper名
	 * @param  Array     TokenParserで抽出下引数の配列
	 * @param  Int       ノードの行番号
	 */
	public function __construct($method, $params, $line)
	{
		parent::__construct($line);
		$this->_method = $method;
		$this->_params = $params;
	}


	/**
	 * 設定された値をもとにノードを形成する
	 *
	 * @access public
	 * @param  Twig_Compiler
	 */
	public function compile($compiler)
	{
		// 設定された引数を配列にする
		$params = $this->_parseParams();

		// $paramsを文字列に変換する
		$prepared = $this->_array2String($params);

		// デバッグ情報の追加（行番号などをつけるだけ）
		$compiler->addDebugInfo($this);

		// Twig_Compiler に、以下の文字列の記述を行わせる
		$compiler->write("echo Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view->{$this->_method}(");
			$compiler->raw($prepared);
		$compiler->raw(");\n");
	}


	/**
	 * _paramsをパースする
	 *
	 * @access private
	 * @return Array
	 */
	private function _parseParams()
	{
		$return = array();
		foreach($this->_params as $key => $exp) {
			$return[] = $this->_getValue($exp);
		}
		return $return;
	}


	/**
	 * 再帰的にデータを抽出する
	 *
	 * @access private
	 * @param  Twig_Node_Expression
	 * @return Mix
	 */
	private function _getValue($exp)
	{
		// Nameは変数なので、$contextを追加して返却
		if($exp instanceof Twig_Node_Expression_Name) {
			return '$context['. $this->_prepare($exp->getName()). ']';
		}
		elseif($exp instanceof Twig_Node_Expression_GetAttr) {
			// @TODO parse GetAttr Expression
		}
		elseif(!$exp instanceof Twig_Node_Expression_Array) {
			return $exp->getValue();
		}

		$params = array();
		if($exp instanceof Twig_Node_Expression_Array) {
			foreach($exp->getElements() as $name => $const) {
				$params[$name] = $this->_getValue($const);
			}
			return $params;
		}
	}


	/**
	 * 引数を文字列に変換する
	 *
	 * @access private
	 * @param  Array
	 * @return String
	 */
	private function _array2String($params)
	{
		$data = array();
		foreach($params as $value) {
			$data[] = $this->_prepare($value);
		}
		return rtrim(implode(", ", $data), ",");
	}


	/**
	 * 型にあわせてサニタイズを行う
	 *
	 * @access private
	 * @param  Mix
	 * @return Mix
	 */
	private function _prepare($d)
	{
		if(is_bool($d)) {
			return ($d == "true") ? true: false;
		}
		elseif(is_numeric($d)) {
			return (int)$d;
		}
		elseif(is_string($d)) {
			// Twig内の意外はすべて文字列で返却
			if(strpos($d, '$') !== 0)
				return "'". addslashes($d). "'";
			else
				return $d;
		}
		elseif (is_array($d)) {
			return $this->_prepareArray($d);
		}
	}


	/**
	 * 配列を再帰的に文字列に変換する
	 *
	 * @access private
	 * @param  Array
	 * @return String
	 */
	private function _prepareArray($data)
	{
		$prep = array();
		foreach($data as $key => $value) {
			if(is_array($value)) {
				$this->_prepareArray($value);
			}
			$prep []= $this->_prepare($key) ." => ". $this->_prepare($value);
		}
		return "array(". implode(", ", $prep) .")";
	}
}