<?php


namespace ether\craftb\web\twig;

use ether\craftb\web\twig\tokenparsers\AtomTokenParser;
use ether\craftb\web\twig\tokenparsers\CriticalTokenParser;
use Twig\Extension\AbstractExtension;

class Extension extends AbstractExtension
{

	public function getTokenParsers ()
	{
		return [
			new AtomTokenParser(),
			new CriticalTokenParser(),
		];
	}

}