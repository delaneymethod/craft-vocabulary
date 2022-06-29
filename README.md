# Vocabulary plugin for Craft CMS 3.x or later

Vocabulary is the easiest way to create and manage your own glossary of terms in Craft CMS.

![Screenshot](resources/Glossary-Example-Template.png)

![Screenshot](resources/Vocabulary-Field-Type.png)

![Screenshot](resources/Glossary-Example-Single-Entry.png)

## Requirements

This plugin requires Craft CMS 3.x or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

   ```
   cd /path/to/project
   ```

2. Then tell Composer to load the plugin:

   ```
   composer require delaneymethod/craft-vocabulary
   ```

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Vocabulary.

## Configuration

By default the plugin is already configured, creating a new `Vocabulary` Field Type and can be used like any other field

## Examples

During installation a new Section, Entry Type, Single entry (pre-populated with some dummy data) and a Template where created to help get you started.

The Template can be found in the templates directory:

   ```
   /templates/glossary-example.twig
   ```

and can be viewed at:

   ```
   /glossary-example
   ```
Please feel free to delete these or rename them as you wish.

Brought to you by [DelaneyMethod](https://delaneymethod.com)
