<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2023, Julien Veyssier <julien-nc@posteo.net>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\SketchPicker\Db;

use DateTime;
use OCA\SketchPicker\AppInfo\Application;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @extends QBMapper<RecentSketch>
 */
class RecentSketchMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'sketch_pk_recent', RecentSketch::class);
	}

	/**
	 * @param int $id
	 * @return RecentSketch
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getRecentSketch(int $id): RecentSketch {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return RecentSketch
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getRecentSketchOfUser(int $id, string $userId): RecentSketch {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			)
			->andWhere(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @param string $fileName
	 * @return RecentSketch
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getRecentSketchOfUserByFileName(string $userId, string $fileName): RecentSketch {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('file_name', $qb->createNamedParameter($fileName, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @return RecentSketch[]
	 * @throws Exception
	 */
	public function getRecentSketchesOfUser(string $userId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->orderBy('timestamp', 'DESC')
			->setMaxResults(Application::MAX_RECENT_SKETCHES_PER_USER);

		return $this->findEntities($qb);
	}

	/**
	 * @param string $userId
	 * @param string $fileName
	 * @param int|null $timestamp
	 * @return RecentSketch|null
	 * @throws Exception
	 */
	public function createRecentSketch(string $userId, string $fileName, ?int $timestamp = null): ?RecentSketch {
		try {
			$recentSketch = $this->getRecentSketchOfUserByFileName($userId, $fileName);
		} catch (DoesNotExistException|MultipleObjectsReturnedException $e) {
			// only create it if it does not exist
			$recentSketch = new RecentSketch();
			$recentSketch->setFileName($fileName);
			$recentSketch->setUserId($userId);
			if ($timestamp === null) {
				$timestamp = (new DateTime())->getTimestamp();
			}
			$recentSketch->setTimestamp($timestamp);
			$insertedRecentSketch = $this->insert($recentSketch);

			$this->cleanupUserRecentSketches($userId);

			return $insertedRecentSketch;
		}

		return null;
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteUserRecentSketches(string $userId): void {
		$qb = $this->db->getQueryBuilder();
		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function cleanupUserRecentSketches(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		// get the last N rows in descending order
		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->orderBy('timestamp', 'DESC')
			->setMaxResults(Application::MAX_RECENT_SKETCHES_PER_USER);

		$req = $qb->executeQuery();

		$lastRecentTs = [];
		while ($row = $req->fetch()) {
			$lastRecentTs[] = (int)$row['timestamp'];
		}
		$req->closeCursor();
		$qb->resetQueryParts();

		// if we have at least MAX prompts stored, delete everything but the last 20 ones
		if (count($lastRecentTs) >= Application::MAX_RECENT_SKETCHES_PER_USER) {
			$firstTsToKeep = end($lastRecentTs);
			$qb->delete($this->getTableName())
				->where(
					$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
				)
				->andWhere(
					$qb->expr()->lt('timestamp', $qb->createNamedParameter($firstTsToKeep, IQueryBuilder::PARAM_INT))
				);
			$qb->executeStatement();
			$qb->resetQueryParts();
		}
	}
}
