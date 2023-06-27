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
	<div class="sketch-wrapper">
		<div v-if="!isLoaded" class="loading-icon">
			<NcLoadingIcon
				:size="44"
				:title="t('sketch_picker', 'Loading sketch')" />
		</div>
		<div v-if="isLink">
			<a v-show="isLoaded"
				:href="url"
				target="_blank">
				<img
					class="image"
					:class="{ big: !isSmall, small: isSmall }"
					:src="url"
					@load="isLoaded = true">
			</a>
		</div>
		<div v-else>
			<img v-show="isLoaded"
				class="image"
				:class="{ big: !isSmall, small: isSmall }"
				:src="url"
				@load="isLoaded = true">
		</div>
	</div>
</template>

<script>
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js'

export default {
	name: 'SketchImage',

	components: {
		NcLoadingIcon,
	},

	props: {
		url: {
			type: String,
			required: true,
		},
		isLink: {
			type: Boolean,
			default: true,
		},
		isSmall: {
			type: Boolean,
			default: true,
		},
	},

	data() {
		return {
			isLoaded: false,
		}
	},

	computed: {
	},

	methods: {
	},
}
</script>

<style scoped lang="scss">
.sketch-wrapper {
	display: flex;
	align-items: center;
	justify-content: center;
	position: relative;

	.image {
		border-radius: var(--border-radius);
		cursor: pointer;

		&.small {
			height: 100px;
		}
		&.big {
			max-height: 300px;
			max-width: 100%;
		}
	}
}
</style>
