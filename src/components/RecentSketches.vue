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
			<SketchImage v-for="r in sketches"
				:key="r"
				:url="getRecentUrl(r)"
				:is-link="false"
				:is-small="true"
				@click.native="$emit('submit', r)" />
		</div>
		<div class="footer">
			<NcButton @click="$emit('cancel')">
				{{ t('sketch_picker', 'Cancel') }}
			</NcButton>
		</div>
	</div>
</template>

<script>
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import SketchImage from './SketchImage.vue'

import { generateUrl } from '@nextcloud/router'

export default {
	name: 'RecentSketches',

	components: {
		SketchImage,
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
	}

	.footer {
		display: flex;
		align-items: center;
		justify-content: end;
	}
}
</style>
