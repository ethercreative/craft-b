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
		if (!$stream->test(Token::STRING_TYPE))
			throw new SyntaxError('You must specify an atom name');

		$attributes['handle'] = $stream->getCurrent()->getValue();
		$stream->next();

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
		$tagNames = array_keys(\Craft::$app->getView()->getTwig()->getTags());

		try {
			$count = 0;
			$openers = [];
			$all = [];

			while (true)
			{
				$token = $stream->look(++$count);

				$all[] = $token->getValue();

				// Is this a name token
				if ($token->getType() === Token::NAME_TYPE)
				{
					$value = $token->getValue();

					// Is this an end token?
					if (strpos($value, 'end') !== false)
					{
						// Get index of most recent atom token
						$i = array_search($this->getTag(), array_reverse($openers, true), true);

						// Do we have an opening atom token
						if ($i !== false)
						{
							// Remove nested atom from openers list
							array_splice($openers, $i, 1);
							continue;
						}

						// Is this an atom end token?
						if ($token->test('end' . $this->getTag()))
							return true;

						// Is this another end token?
						if (!in_array(str_replace('end', '', $value), $openers))
							return false;
					}
					// Add all other tags to openers
					else if (in_array($value, $tagNames)) $openers[] = $value;
				}
			}
		} catch (Exception $e) {}

		return false;
	}

}
