<!--
  - SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<div class="sketch-reference">
		<SketchImage
			:url="richObject.url"
			:is-link="true"
			:is-small="false" />
	</div>
</template>

<script>
import SketchImage from '../components/SketchImage.vue'

export default {
	name: 'SketchReferenceWidget',

	components: {
		SketchImage,
	},

	props: {
		richObjectType: {
			type: String,
			default: '',
		},
		richObject: {
			type: Object,
			default: null,
		},
		accessible: {
			type: Boolean,
			default: true,
		},
	},

	data() {
		return {
		}
	},

	computed: {
	},

	methods: {
	},
}
</script>

<style scoped lang="scss">
.sketch-reference {
	width: 100%;
	padding: 12px;
}
</style>
