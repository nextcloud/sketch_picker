<?php
/**
 * @copyright Copyright (c) 2023 Julien Veyssier <julien-nc@posteo.net>
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\SketchPicker\Reference;

use OCP\Collaboration\Reference\ADiscoverableReferenceProvider;
use OC\Collaboration\Reference\ReferenceManager;
use OCA\SketchPicker\AppInfo\Application;
use OCP\Collaboration\Reference\IReference;
use OCP\Collaboration\Reference\Reference;
use OCP\IL10N;

use OCP\IURLGenerator;

class SketchPickerReferenceProvider extends ADiscoverableReferenceProvider {
	private const RICH_OBJECT_TYPE = Application::APP_ID . '_sketch';

	public function __construct(
		private IL10N $l10n,
		private IURLGenerator $urlGenerator,
		private ReferenceManager $referenceManager,
		private ?string $userId
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string	{
		return 'sketch_picker-draw';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle(): string {
		return $this->l10n->t('Draw a sketch');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(): int	{
		return 10;
	}

	/**
	 * @inheritDoc
	 */
	public function getIconUrl(): string {
		return $this->urlGenerator->getAbsoluteURL(
			$this->urlGenerator->imagePath(Application::APP_ID, 'app-dark.svg')
		);
	}

	/**
	 * @inheritDoc
	 */
	public function matchReference(string $referenceText): bool {
		return $this->getSketchInfoFromUrl($referenceText) !== null;
	}

	/**
	 * @inheritDoc
	 */
	public function resolveReference(string $referenceText): ?IReference {
		if ($this->matchReference($referenceText)) {
			$info = $this->getSketchInfoFromUrl($referenceText);
			if ($info === null) {
				return null;
			}

			$reference = new Reference($referenceText);
			$richObjectInfo = [
				'url' => $referenceText,
				'userId' => $info['userId'],
				'fileName' => $info['fileName'],
			];
			$reference->setRichObject(
				self::RICH_OBJECT_TYPE,
				$richObjectInfo,
			);
			return $reference;
		}

		return null;
	}

	/**
	 * @param string $url
	 * @return array|null
	 */
	private function getSketchInfoFromUrl(string $url): ?array {
		$start = $this->urlGenerator->getAbsoluteURL('/apps/' . Application::APP_ID);
		$startIndex = $this->urlGenerator->getAbsoluteURL('/index.php/apps/' . Application::APP_ID);

		// link example: https://nextcloud.local/index.php/apps/sketch_picker/sketches/user1/2d040f2e825de9242da9336bd9053125.webp
		preg_match('/^' . preg_quote($start, '/') . '\/sketches\/([^\/?&]+)\/([^\/?&]+)$/i', $url, $matches);
		if (count($matches) > 1) {
			return [
				'userId' => $matches[1],
				'fileName' => $matches[2],
			];
		}

		preg_match('/^' . preg_quote($startIndex, '/') . '\/sketches\/([^\/?&]+)\/([^\/?&]+)$/i', $url, $matches);
		if (count($matches) > 1) {
			return [
				'userId' => $matches[1],
				'fileName' => $matches[2],
			];
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getCachePrefix(string $referenceId): string {
		return $this->userId ?? '';
	}

	/**
	 * We don't use the userId here but rather a reference unique id
	 * @inheritDoc
	 */
	public function getCacheKey(string $referenceId): ?string {
		return $referenceId;
	}

	/**
	 * @param string $userId
	 * @return void
	 */
	public function invalidateUserCache(string $userId): void {
		$this->referenceManager->invalidateCache($userId);
	}
}
