<?php
/**
 * Vocabulary plugin for Craft CMS 3.x or later
 *
 * Vocabulary is the easiest way to create and manage your own glossary of terms in Craft CMS.
 *
 * @link      https://delaneymethod.com
 * @copyright Copyright (c) 2022 DelaneyMethod
 */

namespace delaneymethod\vocabulary\fields;

use Craft;
use yii\base\Exception;
use craft\fields\Matrix;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @author    DelaneyMethod
 * @package   Vocabulary
 * @since     1.0.0
 */
class Vocabulary extends Matrix
{
	/**
	 * Returns the display name of this class.
	 *
	 * @return string The display name of this class.
	 */
	public static function displayName(): string
	{
		return Craft::t('vocabulary', 'Vocabulary');
	}

	/**
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 * @throws Exception
	 */
	public function getSettingsHtml(): string
	{
		return Craft::$app->getView()->renderTemplate('vocabulary/_components/fields/Vocabulary_settings', [
			'field' => $this
		]);
	}
}
