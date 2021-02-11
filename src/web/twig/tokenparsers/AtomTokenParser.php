<?php


namespace ether\craftb\web\twig\tokenparsers;

use ether\craftb\web\twig\nodes\AtomNode;
use Exception;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenStream;

class AtomTokenParser extends AbstractTokenParser
{

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Token $token
	 *
	 * @return Node
	 * @throws SyntaxError
	 */
	public function parse (Token $token)
	{
		$parser = $this->parser;
		$lineNo = $token->getLine();
		$stream = $parser->getStream();
		$expressionParser = $parser->getExpressionParser();
		$nodes = [
			'value' => new Node(),
		];
		$attributes = [
			'data' => new ArrayExpression([], $lineNo),
		];

		// Is this a tag pair?
		$capture = $this->_lookForClosing($stream);

		// Get the file name
		$stream->next();
		$attributes['handle'] = $stream->getCurrent()->getValue();

		// Are any variables defined?
		if ($stream->test(Token::PUNCTUATION_TYPE))
		{
			$attributes['data'] = $expressionParser->parseHashExpression();
		}

		// Capture the contents
		if ($capture)
		{
			$stream->expect(Token::BLOCK_END_TYPE);
			$nodes['value'] = $parser->subparse([$this, 'decideBlockEnd'], true);
		}

		// Close out the tag
		$stream->expect(Token::BLOCK_END_TYPE);

		return new AtomNode(
			$nodes,
			$attributes,
			$lineNo,
			$this->getTag()
		);
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag ()
	{
		return 'atom';
	}

	/**
	 * @param Token $token
	 *
	 * @return bool
	 */
	public function decideBlockEnd (Token $token)
	{
		return $token->test('end' . $this->getTag());
	}

	// Helpers
	// =========================================================================

	/**
	 * Check to see if there is a closing tag up ahead.
	 *
	 * @param TokenStream $stream
	 *
	 * @return bool
	 */
	private function _lookForClosing (TokenStream $stream)
	{
		try {
			$count = 0;

			while (true)
				if ($stream->look(++$count)->test('end' . $this->getTag()))
					return true;
		} catch (Exception $e) {}

		return false;
	}

}