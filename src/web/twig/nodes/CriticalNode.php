<?php


namespace ether\craftb\web\twig\nodes;

use ether\craftb\CraftB;
use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeCaptureInterface;

class CriticalNode extends Node implements NodeCaptureInterface
{

	public function compile (Compiler $compiler)
	{
		$handle = $this->getAttribute('handle');

		$compiler
			->addDebugInfo($this)
			->write(CraftB::class . '::renderCritical(\'' . $handle . '\');' . PHP_EOL);
	}

}