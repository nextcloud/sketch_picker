<!--
  - @copyright Copyright (c) 2023 Julien Veyssier <julien-nc@posteo.net>
  -
  - @author 2023 Julien Veyssier <julien-nc@posteo.net>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
	<div class="recent-sketches">
		<div class="images">
			<img v-for="r in sketches"
				:key="r"
				class="image"
				:src="getRecentUrl(r)"
				@click="$emit('submit', r)">
		</div>
		<NcButton @click="$emit('cancel')">
			{{ t('sketch_picker', 'Cancel') }}
		</NcButton>
	</div>
</template>

<script>
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import { generateUrl } from '@nextcloud/router'

export default {
	name: 'RecentSketches',

	components: {
		NcButton,
	},

	props: {
		sketches: {
			type: Array,
			required: true,
		},
	},

	emits: [
		'submit',
		'cancel',
	],

	data() {
		return {
		}
	},

	computed: {
	},

	methods: {
		getRecentUrl(name) {
			return generateUrl('apps/sketch_picker/sketches/{name}', { name })
		},
	},
}
</script>

<style scoped lang="scss">
.recent-sketches {
	display: flex;
	flex-direction: column;
	gap: 16px;
	.images {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		align-items: center;
		.image {
			height: 100px;
			border-radius: var(--border-radius);
			cursor: pointer;
		}
	}
}
</style>
