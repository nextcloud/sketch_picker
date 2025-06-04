<?php

/**
 * Nextcloud - SketchPicker
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier
 * @copyright Julien Veyssier 2023
 */

namespace OCA\SketchPicker\Service;

use OCA\SketchPicker\Db\RecentSketchMapper;
use OCP\DB\Exception;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Files\SimpleFS\ISimpleFile;

class SketchService {

	public function __construct(
		string $appName,
		private IAppData $appData,
		private RecentSketchMapper $recentSketchMapper,
	) {
	}

	/**
	 * @param string $name
	 * @return ISimpleFile|null
	 * @throws NotFoundException
	 */
	public function getSketchFile(string $name): ?ISimpleFile {
		try {
			$sketchFolder = $this->appData->getFolder('sketches');
		} catch (NotFoundException $e) {
			return null;
		}
		$safeName = basename($name);
		if ($sketchFolder->fileExists($safeName)) {
			return $sketchFolder->getFile($safeName);
		}
		return null;
	}

	/**
	 * @param string $content
	 * @param string $mimeType
	 * @return string[]|null
	 * @throws NotPermittedException
	 */
	public function addSketch(string $content, string $mimeType): ?array {
		try {
			$sketchFolder = $this->appData->getFolder('sketches');
		} catch (NotFoundException $e) {
			$sketchFolder = $this->appData->newFolder('sketches');
		}

		$mimeToExt = [
			'image/png' => 'png',
			'image/jpeg' => 'jpeg',
			'image/webp' => 'webp',
		];
		$extension = $mimeToExt[$mimeType];

		$i = 1;
		$base = md5($content);
		if ($sketchFolder->fileExists($base . '.' . $extension)) {
			while ($sketchFolder->fileExists($base . '-' . strval($i) . '.' . $extension)) {
				$i++;
			}
			$fileName = $base . '-' . strval($i) . '.' . $extension;
		} else {
			$fileName = $base . '.' . $extension;
		}
		$sketchFolder->newFile($fileName, $content);
		return [
			'name' => $fileName,
		];
	}

	/**
	 * @param string $userId
	 * @return array
	 * @throws Exception
	 */
	public function getRecentlySeenSketches(string $userId): array {
		try {
			$sketchFolder = $this->appData->getFolder('sketches');
		} catch (NotFoundException $e) {
			return [];
		}

		$existingSketches = [];
		$dbSketches = $this->recentSketchMapper->getRecentSketchesOfUser($userId);
		foreach ($dbSketches as $sketch) {
			if ($sketchFolder->fileExists($sketch->getFileName())) {
				$existingSketches[] = $sketch->getFileName();
			}
		}
		return $existingSketches;
	}
}
