<?php
/**
 * Vocabulary plugin for Craft CMS 3.x or later
 *
 * Vocabulary is the easiest way to create and manage your own glossary of terms in Craft CMS.
 *
 * @link      https://delaneymethod.com
 * @copyright Copyright (c) 2022 DelaneyMethod
 */

namespace delaneymethod\vocabulary;

use Craft;
use yii\base\Event;
use craft\base\Plugin;
use craft\services\Fields;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use delaneymethod\vocabulary\fields\Vocabulary as VocabularyField;

/**
 * @author    DelaneyMethod
 * @package   Vocabulary
 * @since     1.0.0
 */
class Vocabulary extends Plugin
{
	/**
	 * @var Vocabulary
	 */
	public static Vocabulary $plugin;

	/**
	 * @var string
	 */
	public $schemaVersion = '1.0.0';

	/**
	 * @var bool
	 */
	public $hasCpSettings = false;

	/**
	 * @var bool
	 */
	public $hasCpSection = false;

	/**
	 * @return void
	 */
	public function init()
	{
		parent::init();

		self::$plugin = $this;

		Event::on(
			Fields::class,
			Fields::EVENT_REGISTER_FIELD_TYPES,
			function (RegisterComponentTypesEvent $event) {
				$event->types[] = VocabularyField::class;
			}
		);

		Event::on(
			Plugins::class,
			Plugins::EVENT_AFTER_ENABLE_PLUGIN,
			function (PluginEvent $event) {
				if ($event->plugin === $this) {
					// or run /actions/vocabulary/vocabulary/install-examples in your browser
					Craft::$app->controllerNamespace = 'delaneymethod\\vocabulary\\controllers';
					Craft::$app->runAction('vocabulary/vocabulary/install-examples');
				}
			}
		);

		Craft::info(
			Craft::t(
				'vocabulary',
				'{name} plugin loaded',
				['name' => $this->name]
			),
			__METHOD__
		);
	}
}
