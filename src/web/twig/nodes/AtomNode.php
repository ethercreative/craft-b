<?php


namespace ether\craftb\web\twig\nodes;


use ether\craftb\CraftB;
use Twig\Compiler;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Twig\Node\NodeCaptureInterface;

class AtomNode extends Node implements NodeCaptureInterface
{

	public function compile (Compiler $compiler)
	{
		$handle = $this->getAttribute('handle');
		/** @var ArrayExpression $data */
		$data = $this->getAttribute('data');
		$value = $this->getNode('value');

		$compiler
			->addDebugInfo($this)
			->write('ob_start();' . PHP_EOL)
			->subcompile($value)
			->write(CraftB::class . '::renderAtom(\'' . $handle . '\', ')
			->raw($data->compile($compiler) . ', ')
			->raw('ob_get_clean());' . PHP_EOL);
	}

}