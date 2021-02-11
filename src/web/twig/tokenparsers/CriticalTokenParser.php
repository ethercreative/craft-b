<?php


namespace ether\craftb\web\twig\tokenparsers;

use ether\craftb\web\twig\nodes\CriticalNode;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class CriticalTokenParser extends AbstractTokenParser
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
		$attributes = [];

		// Get the file name
		$stream->next();
		$attributes['handle'] = $stream->getCurrent()->getValue();

		// Close out the tag
		$stream->expect(Token::BLOCK_END_TYPE);

		return new CriticalNode(
			[],
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
		return 'critical';
	}

}