<?php
class Nully_View_TokenParser extends Twig_TokenParser
{
	public function getTag()
	{
		return "helper";
	}


	public function parse(Twig_Token $token)
	{
		// parserから現在読み込んでいるストリームを取得する
		$strm = $this->parser->getStream();

		// 行番号の取得
		$lineno = $token->getLine();

		// ViewHelper名を取得する
		$method = $strm->expect(Twig_Token::NAME_TYPE)->getValue();

		// ストリームから引数などをすべて取得する
		$params = $this->_autoParse($strm);

		// 取得後、ブロックの終了宣言を行う
		$strm->expect(Twig_Token::BLOCK_END_TYPE);

		// 新規ノードを返す
		return new Nully_View_Node($method, $params, $lineno);
	}


	/**
	 * Twig_Tokenのタイプを判別し、値を返却する
	 *
	 * @access private
	 * @param  Twig_TokenStream
	 * @return Mix
	 */
	private function _getNode($strm)
	{
		// 数値型のテスト
		if($strm->test(Twig_Token::NUMBER_TYPE)) {
			return $strm->expect(Twig_Token::NUMBER_TYPE);
		}
		// オペレーター型のテスト
		else if($strm->test(Twig_Token::OPERATOR_TYPE, "{") || $strm->test(Twig_Token::OPERATOR_TYPE, "[")) {
			return $strm->expect(Twig_Token::OPERATOR_TYPE);
		}
		// 変数型のテスト
		else if($strm->test(Twig_Token::NAME_TYPE)) {
			return $strm->expect(Twig_Token::NAME_TYPE);
		}
		// TEXTタイプのテスト
		else if($strm->test(Twig_Token::TEXT_TYPE)) {
			return $strm->expect(Twig_Token::TEXT_TYPE);
		}
		else if($strm->test(Twig_Token::STRING_TYPE)) {
			return $strm->expect(Twig_Token::STRING_TYPE);
		}
	}


	/**
	 * Twig_TokenStreamからTokenを自動で解析する
	 *
	 * @access private
	 * @param  Twig_TokenStream
	 * @return Array
	 */
	private function _autoParse(Twig_TokenStream $s)
	{
		$return = array();
		while(!$s->test(Twig_Token::BLOCK_END_TYPE)) {
			if($s->test(Twig_Token::OPERATOR_TYPE)) {
				$params = array();
				// 自動的に配列Expressionを取得
				$expressions = $this->parser->getExpressionParser()->parseExpression();
				//$params = $this->_parseExpression($expressions);
				$return[] = $expressions;
			}
			else {
				$return[] = $this->_getNode($s);
			}
		}
		return $return;
	}


	/**
	 * 再帰的に内容を解析していく
	 *
	 * @access private
	 * @param  Twig_Node_Expression
	 * @return Array
	 */
	private function _parseExpression($expression)
	{
		$return = array();
		foreach($expression->getElements() as $name => $exp) {
			if($exp instanceof Twig_Node_Expression_Array) {
				$return[$name] = $this->_parseExpression($exp);
			}
			else if($exp instanceof Twig_Node_Expression_Name) {
				$return[$name] = $exp->getName();
			}
			else {
				$return[$name] = $exp->getValue();
			}
		}
		return $return;
	}
}
