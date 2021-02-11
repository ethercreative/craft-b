<?php


namespace ether\craftb;

use Craft;
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
		if (file_exists(CRAFT_CONFIG_PATH . '/b.php'))
		{
			self::$_config = array_merge(
				self::$_config,
				require CRAFT_CONFIG_PATH . '/b.php'
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
	 * @return string
	 * @throws Exception
	 */
	public static function renderAtom (
		string $handle,
		array $variables = [],
		string $children = null
	) : string {
		$template = self::$_config['atoms'] . '/' . $handle;
		$variables['children'] = $children;

		if (!Craft::$app->view->doesTemplateExist($template))
		{
			Craft::error(
				"Error locating template: {$template}",
				__METHOD__
			);

			return '';
		}

		return Craft::$app->getView()->render($template, $variables);
	}

	/**
	 * Injects critical css
	 *
	 * @param string $handle
	 *
	 * @return string
	 * @throws LoaderError
	 * @throws SyntaxError
	 */
	public static function renderCritical (string $handle) : string
	{
		if (getenv('CRITICAL') === 'false')
			return;

		$critical = Craft::$app->view->renderString(
			'{{ source("_critical/' . $handle . '.css", ignore_missing=true) }}'
		);

		Craft::$app->view->registerCss($critical);
	}

}