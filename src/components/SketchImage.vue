<!--
  - SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
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
import NcLoadingIcon from '@nextcloud/vue/components/NcLoadingIcon'

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
