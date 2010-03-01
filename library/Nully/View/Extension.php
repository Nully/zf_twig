<?php
/**
 * Twigで利用するための自作拡張
 *
 */
class Nully_View_Extension extends Twig_Extension
{
	/**
	 * 文字列を一定の長さで切り取る
	 * 切り取った後は、指定のマーカーを付与する
	 *
	 * @access public
	 * @param  Twig_Environment
	 * @param  String
	 * @param  Int      切り出す開始位置
	 * @param  Int      切り出す終了位置
	 * @param  String   切り出した文字列の後に付加する文字
	 * @return String
	 */
	public static function trancate(Twig_Environment $env, $string, $start = 0, $end = 30, $marker = "[...]")
	{
		return mb_strimwidth($string, $start, $end, $marker, $env->getCharset());
	}


	/**
	 * Twigで利用できるフィルタを返す
	 * 配列は
	 *    フィルタ名 => new Twig_Filter_Function(実行したい関数名)
	 *    フィルタ名 => new Twig_Filter_Method(クラスオブジェクト, 実行する関数名)
	 * とする。
	 * Twig_Filter_Functionは関数を、Twig_Filter_Methodはオブジェクトメソッドを呼び出すことでができる。
	 *
	 *  例) Twig_Filter_Function
	 *  "dump" => new Twig_Filter_Function("Zend_Debugdump")
	 *
	 *  例) Twig_Filter_Method
	 *  "dump" => new Twig_Filter_Method($this, "dump")
	 *
	 * 必要に応じて以下の配列を指定する
	 *   needs_environment => (Bool) --- Twig_environmentをフィルタの実行時に引数に取るか
	 *   is_escaper        => (Bool) --- もし自動エスケープが利用できる状態で、
	 *                                   非エスケープ状態の引数が必要な場合に true を指定する
	 *   例)
	 *      "dump" => new Twig_Filter_Function("Zend_Debug::dump", array(
	 *          "needs_environment" => true
	 *      ));
	 *
	 * @access public
	 * @return Array
	 */
	public function getFilters()
	{
		return array(
			"dump" => new Twig_Filter_Function("Zend_Debug::dump"),
			"trancate" => new Twig_Filter_Method(
				$this, "trancate",
				array("needs_environment" => true, "is_escaper" => false)
			),
		);
	}


	/**
	 * 自作したTokenParserを返却する
	 *
	 * @access public
	 * @return Array
	 */
	public function getTokenParsers()
	{
		return array(
			new Nully_View_TokenParser()
		);
	}


	/**
	 * Extension名を返却
	 *
	 * @access public
	 * @return String
	 */
	public function getName()
	{
		return "nully";
	}
}



