<!--
  - SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
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
import NcButton from '@nextcloud/vue/components/NcButton'

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
