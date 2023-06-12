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

use OC\User\NoUserException;
use OCP\Files\File;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;

class SketchService {

	public function __construct(
		string $appName,
		private IRootFolder $root
	) {
	}

	/**
	 * @param string $userId
	 * @param string $name
	 * @return File|null
	 * @throws NoUserException
	 * @throws NotFoundException
	 * @throws NotPermittedException
	 */
	public function getSketchFile(string $userId, string $name): ?File {
		$userFolder = $this->root->getUserFolder($userId);
		$sketchFolder = $userFolder->get('Sketches');
		$safeName = basename($name);
		if ($sketchFolder->nodeExists($safeName)) {
			$file = $sketchFolder->get($safeName);
			if ($file instanceof File) {
				return $file;
			}
		}
		return null;
	}

	/**
	 * @param string $userId
	 * @param string $content
	 * @param string $mimeType
	 * @return string[]|null
	 * @throws NoUserException
	 * @throws NotFoundException
	 * @throws NotPermittedException
	 */
	public function addSketch(string $userId, string $content, string $mimeType): ?array {
		$userFolder = $this->root->getUserFolder($userId);
		if (!$userFolder->nodeExists('Sketches')) {
			$sketchFolder = $userFolder->newFolder('Sketches');
		} else {
			$sketchFolder = $userFolder->get('Sketches');
			if ($sketchFolder instanceof File) {
				throw new \Exception('/Sketches is a file');
			}
		}

		$mimeToExt = [
			'image/png' => 'png',
			'image/jpeg' => 'jpeg',
			'image/webp' => 'webp',
		];
		$extension = $mimeToExt[$mimeType];

		$i = 1;
		$base = md5($content);
		if ($sketchFolder->nodeExists($base . '.' . $extension)) {
			while ($sketchFolder->nodeExists($base . '-' . $i . '.' . $extension)) {
				$i++;
			}
			$fileName = $base . '-' . $i . '.' . $extension;
		} else {
			$fileName = $base . '.' . $extension;
		}
		$sketchFolder->newFile($fileName, $content);
		return [
			'name' => $fileName,
			'userId' => $userId,
		];
	}
}
