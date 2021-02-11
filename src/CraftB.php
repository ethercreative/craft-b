<?php


namespace ether\craftb;

use Craft;
use craft\web\View;
use ether\craftb\web\twig\Extension;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\Module;

class CraftB extends Module
{

	private static $_config = [];

	public function init ()
	{
		parent::init();

		self::setInstance($this);

		self::$_config = require __DIR__ . '/config.php';
		if (file_exists(CRAFT_BASE_PATH . '/config/b.php'))
		{
			self::$_config = array_merge(
				self::$_config,
				require CRAFT_BASE_PATH . '/config/b.php'
			);
		}

		Craft::$app->getView()->registerTwigExtension(
			new Extension()
		);
	}

	/**
	 * Renders an atom
	 *
	 * @param string      $handle
	 * @param array       $variables
	 * @param string|null $children
	 *
	 * @throws Exception
	 */
	public static function renderAtom (
		string $handle,
		array $variables = [],
		string $children = null
	) : void {
		$view = Craft::$app->getView();

		$template = self::$_config['atoms'] . '/' . $handle;
		if (strpos($template, '.twig') === false) $template .= '.twig';

		$variables['children'] = new Markup($children, 'utf8');

		if (!$view->doesTemplateExist($template))
		{
			Craft::error(
				"Error locating template: {$template}",
				__METHOD__
			);

			return;
		}

		echo $view->renderTemplate($template, $variables);
	}

	/**
	 * Injects critical css
	 *
	 * @param string $handle
	 *
	 * @throws LoaderError
	 * @throws SyntaxError
	 */
	public static function renderCritical (string $handle): void
	{
		if (getenv('CRITICAL') === 'false')
			return;

		$view = Craft::$app->getView();

		$critical = $view->renderString(
			'{{ source("' . self::$_config['critical'] . '/' . $handle . '.css", ignore_missing=true) }}'
		);

		$view->registerCss($critical);
	}

}
