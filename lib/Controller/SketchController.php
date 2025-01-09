<?php

/**
 * Nextcloud - SketchPicker
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2023
 */

namespace OCA\SketchPicker\Controller;

use Exception;
use OCA\SketchPicker\Service\SketchService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use Throwable;

class SketchController extends OCSController {

	public function __construct(
		string $appName,
		IRequest $request,
		private SketchService $sketchService,
		private ?string $userId,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @param string $base64Content
	 * @param string $mimeType
	 * @return DataResponse
	 */
	#[NoAdminRequired]
	public function addSketch(string $base64Content, string $mimeType): DataResponse {
		try {
			$binaryContent = base64_decode(str_replace('data:' . $mimeType . ';base64,', '', $base64Content));
			$sketchInfo = $this->sketchService->addSketch($binaryContent, $mimeType);
			return new DataResponse($sketchInfo);
		} catch (Exception|Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @return DataResponse
	 */
	#[NoAdminRequired]
	public function getRecentlySeenSketches(): DataResponse {
		try {
			$sketches = $this->sketchService->getRecentlySeenSketches($this->userId);
			return new DataResponse($sketches);
		} catch (Exception|Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}
}
