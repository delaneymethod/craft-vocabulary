<?php

declare(strict_types=1);

use craft\ecs\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function(ECSConfig $ecsConfig): void {
	$ecsConfig->paths([__DIR__ . '/src', __FILE__]);
	$ecsConfig->indentation('tab');
	$ecsConfig->parallel();
	$ecsConfig->sets([SetList::CRAFT_CMS_4]);

	$parameters = $ecsConfig->parameters();
	$parameters->set(Option::CACHE_DIRECTORY, '.ecs_cache');
};
