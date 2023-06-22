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
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\BruteForceProtection;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\IRequest;
use Throwable;

class FileController extends Controller {

	public function __construct(
		string $appName,
		IRequest $request,
		private SketchService $sketchService,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @param string $userId
	 * @param string $id
	 * @return DataDisplayResponse
	 */
	#[PublicPage]
	#[NoCSRFRequired]
	#[BruteForceProtection(action: 'getSketchFile')]
	public function getSketchFile(string $userId, string $id): DataDisplayResponse {
		try {
			$file = $this->sketchService->getSketchFile($userId, $id);
			if ($file === null) {
				throw new Exception('');
			}
			return new DataDisplayResponse($file->getContent(), Http::STATUS_OK, [
				'Content-Type' => $file->getMimeType(),
			]);
		} catch (Exception | Throwable $e) {
			$response = new DataDisplayResponse('', Http::STATUS_BAD_REQUEST);
			$response->throttle(['id' => $id]);
			return $response;
		}
	}
}
