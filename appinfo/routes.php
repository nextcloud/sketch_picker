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

$requirements = [
	'apiVersion' => 'v1',
//	'token' => '^[a-z0-9]{4,30}$',
];

return [
	'routes' => [
		['name' => 'config#setConfig', 'url' => '/config', 'verb' => 'PUT'],
		['name' => 'config#setAdminConfig', 'url' => '/admin-config', 'verb' => 'PUT'],
		['name' => 'file#getSketchFile', 'url' => '/sketches/{id}', 'verb' => 'GET'],
	],

	'ocs' => [
		['name' => 'sketch#addSketch', 'url' => '/api/{apiVersion}/sketches', 'verb' => 'POST', 'requirements' => $requirements],
		['name' => 'sketch#getRecentlySeenSketches', 'url' => '/api/{apiVersion}/recently-seen', 'verb' => 'GET', 'requirements' => $requirements],
	],
];
