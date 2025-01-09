<?php

/**
 * Nextcloud - SketchPicker
 *
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2023
 */

namespace OCA\SketchPicker\AppInfo;

use OCA\SketchPicker\Listener\SketchPickerReferenceListener;
use OCA\SketchPicker\Reference\SketchPickerReferenceProvider;
use OCP\AppFramework\App;

use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Collaboration\Reference\RenderReferenceEvent;

class Application extends App implements IBootstrap {

	public const APP_ID = 'sketch_picker';

	public const MAX_RECENT_SKETCHES_PER_USER = 10;

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerReferenceProvider(SketchPickerReferenceProvider::class);
		$context->registerEventListener(RenderReferenceEvent::class, SketchPickerReferenceListener::class);
	}

	public function boot(IBootContext $context): void {
	}
}
