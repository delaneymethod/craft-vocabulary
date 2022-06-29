<?php

/**
 * Vocabulary plugin for Craft CMS 3.x or later
 *
 * Vocabulary is the easiest way to create and manage your own glossary of terms in Craft CMS.
 *
 * @link      https://delaneymethod.com
 * @copyright Copyright (c) 2022 DelaneyMethod
 */

namespace delaneymethod\vocabulary\controllers;

use Craft;
use Throwable;
use yii\web\Response;
use yii\base\Exception;
use craft\fields\Table;
use craft\web\Controller;
use craft\models\Section;
use craft\elements\Entry;
use craft\fields\PlainText;
use yii\base\InvalidConfigException;
use craft\models\Section_SiteSettings;
use craft\errors\SiteNotFoundException;
use craft\errors\ElementNotFoundException;
use craft\errors\SectionNotFoundException;
use craft\errors\EntryTypeNotFoundException;
use delaneymethod\vocabulary\fields\Vocabulary as VocabularyField;

/**
 * @author    DelaneyMethod
 * @package   Vocabulary
 * @since     1.0.0
 */
class VocabularyController extends Controller
{
	/**
	 * @var bool
	 */
	public $enableCsrfValidation = false;

	/**
	 * @var bool
	 */
	protected $allowAnonymous = true;

	/**
	 * @return Response
	 * @throws Exception
	 */
	public function actionInstallExamples(): Response
	{
		try {
			$success = $this->createFieldType();
			$success = $this->createExampleSection();
			$success = $this->createExampleEntryType();
			$success = $this->createExampleEntry();
			$success = $this->createExampleTemplates();

			$message = $success
				? 'Vocabulary examples installed.'
				: "Couldn't install Vocabulary examples.";

			return $this->asJson([
				'success' => $success,
				'message' => $message
			]);
		} catch (Throwable $exception) {
			$message = $exception->getMessage();

			Craft::error($message, 'vocabulary');

			throw new Exception($message);
		}
	}

	/**
	 * @return bool
	 * @throws Throwable
	 */
	private function createFieldType(): bool
	{
		$success = false;

		$field = Craft::$app->getFields()->getFieldByHandle('vocabulary');

		if (! $field) {
			$field = Craft::$app->getFields()->createField([
				'type' => VocabularyField::class,
				'groupId' => 1,
				'name' => 'Vocabulary',
				'handle' => 'vocabulary',
				'settings' => [
					'minBlocks' => '',
					'maxBlocks' => '',
					'localizeBlocks' => false,
					'blockTypes' => [
						'new1' => [
							'name' => 'Glossary',
							'handle' => 'glossary',
							'fields' => [
								'new1' => [
									'id' => null,
									'type' => PlainText::class,
									'name' => 'Heading',
									'handle' => 'glossaryHeading',
									'required' => true,
									'typesettings' => [
										'byteLimit' => null,
										'charLimit' => null,
										'columnType' => null,
										'initialRows' => '4',
										'multiline' => '',
										'placeholder' => null,
										'uiMode' => 'normal'
									]
								],
								'new2' => [
									'id' => null,
									'type' => Table::class,
									'name' => 'Item',
									'handle' => 'glossaryItem',
									'required' => false,
									'typesettings' => [
										'addRowLabel' => 'Add a row',
										'columnType' => 'text',
										'columns' => [
											'col1' => [
												'heading' => 'Title',
												'handle' => 'title',
												'width' => '',
												'type' => 'singleline'
											],
											'col2' => [
												'heading' => 'Body',
												'handle' => 'body',
												'width' => '',
												'type' => 'multiline'
											]
										],
										'maxRows' => '',
										'minRows' => ''
									]
								]
							]
						]
					]
				]
			]);

			$success = Craft::$app->getFields()->saveField($field);
		}

		return $success;
	}

	/**
	 * @return bool
	 * @throws SectionNotFoundException
	 * @throws SiteNotFoundException
	 * @throws Throwable
	 */
	private function createExampleSection(): bool
	{
		$success = false;

		$section = Craft::$app->getSections()->getSectionByHandle('glossaryExample');

		if (! $section) {
			$siteSettings = new Section_SiteSettings([
				'hasUrls' => true,
				'uriFormat' => '{slug}',
				'enabledByDefault' => true,
				'template' => 'glossary-example.twig',
				'siteId' => Craft::$app->getSites()->getPrimarySite()->id
			]);

			$section = new Section([
				'name' => 'Glossary Example',
				'handle' => 'glossaryExample',
				'type' => Section::TYPE_SINGLE,
				'siteSettings' => [
					$siteSettings
				]
			]);

			$success = Craft::$app->getSections()->saveSection($section);
		}

		return $success;
	}

	/**
	 * @return bool
	 * @throws EntryTypeNotFoundException
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	private function createExampleEntryType(): bool
	{
		$success = false;

		$field = Craft::$app->getFields()->getFieldByHandle('vocabulary');

		$section = Craft::$app->getSections()->getSectionByHandle('glossaryExample');

		if ($field && $section) {
			// We only need to add the Vocabulary field to the first entry type and its first tab...
			$entryTypes = $section->getEntryTypes();

			$fieldLayout = $entryTypes[0]->getFieldLayout();

			$tabs = $fieldLayout->getTabs();

			$fields = $tabs[0]->getFields();

			$fields = array_unique(array_merge($fields, [
				$field
			]));

			$tabs[0]->setFields($fields);

			$fieldLayout->setTabs($tabs);

			$entryTypes[0]->setFieldLayout($fieldLayout);

			$success = Craft::$app->getSections()->saveEntryType($entryTypes[0]);
		}

		return $success;
	}

	/**
	 * Adds an A-Z example
	 *
	 * @return bool
	 * @throws Exception
	 * @throws Throwable
	 * @throws ElementNotFoundException
	 */
	private function createExampleEntry(): bool
	{
		$success = false;

		$entry = Entry::find()
			->section('glossaryExample')
			->slug('glossary-example')
			->one();

		if ($entry) {
			// Always work with a clean surface
			$entry->setFieldValue('vocabulary', []);

			Craft::$app->getElements()->saveElement($entry, false, false);

			// Now build our Glossary
			$glossaries = [];

			// A
			$glossary = [];
			$glossary['enabled'] = true;
			$glossary['type'] = 'glossary';
			$glossary['fields'] = [
				'glossaryHeading' => 'A',
				'glossaryItem' => [
					[
						'title' => 'Address Resolution Protocol',
						'body' => 'A TCP/IP protocol to query MAC address of the target device by its IP address, so as to ensure right communication services.'
					], [
						'title' => 'Adjacent Channel Selectivity',
						'body' => 'A measurement of a receiver’s ability to process a desired signal while rejecting a strong signal in an adjacent channel.'
					], [
						'title' => 'AIE',
						'body' => 'Air Interface Encryption, an encryption method used in TETRA on the Air Interface signalling only (control signalling and user payload).'
					], [
						'title' => 'Air Interface',
						'body' => 'In wireless communication, the air interface is the radio-based communication link between the mobile station and the active base station.'
					]
				]
			];

			$glossaries[] = $glossary;

			// B
			$glossary = [];
			$glossary['enabled'] = true;
			$glossary['type'] = 'glossary';
			$glossary['fields'] = [
				'glossaryHeading' => 'B',
				'glossaryItem' => [
					[
						'title' => 'BS',
						'body' => 'Base Station'
					], [
						'title' => 'Base Station',
						'body' => 'In wireless communication, a base station is a wireless communication station installed at a fixed location and used as part of a two-way radio system.'
					], [
						'title' => 'Bit Error Rate',
						'body' => 'The number of received bits that have been altered due to noise, interference, and distortion, divided by the total number of transferred bits during a studied time interval locking. A measure of the receiver\'s ability to resist the strong interference signals.'
					]
				]
			];

			$glossaries[] = $glossary;

			// C
			$glossary = [];
			$glossary['enabled'] = true;
			$glossary['type'] = 'glossary';
			$glossary['fields'] = [
				'glossaryHeading' => 'C',
				'glossaryItem' => [
					[
						'title' => 'Call Out',
						'body' => 'Standardized paging and resource management method for TETRA systems.'
					]
				]
			];

			$glossaries[] = $glossary;

			// Now lets populate the rest but empty.
			$letters = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

			foreach ($letters as $letter) {
				$glossary = [];
				$glossary['enabled'] = true;
				$glossary['type'] = 'glossary';
				$glossary['fields'] = [
					'glossaryHeading' => $letter,
					'glossaryItem' => []
				];

				$glossaries[] = $glossary;
			}

			// Loop over our glossaries and build up our Matrix block structure
			$newBlocks['blocks'] = [];

			foreach ($glossaries as $key => $glossary) {
				$newBlocks['blocks']['new:'.($key + 1)] = $glossary;
			}

			$existingBlocks = $entry->getFieldValue('vocabulary')->ids();

			$sortOrder = array_merge($existingBlocks, array_keys($newBlocks['blocks']));

			$newBlocks['sortOrder'] = $sortOrder;

			// Set the field value and save
			$entry->setFieldValue('vocabulary', $newBlocks);

			$success = Craft::$app->getElements()->saveElement($entry);
		}

		return $success;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	private function createExampleTemplates(): bool
	{
		return copy(Craft::$app->getPlugins()->getPlugin('vocabulary')->getBasePath().'/templates/glossary-example.twig', Craft::$app->getPath()->getSiteTemplatesPath().'/glossary-example.twig');
	}
}
