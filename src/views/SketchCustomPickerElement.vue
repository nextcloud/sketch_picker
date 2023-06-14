<template>
	<div class="sketch-picker-content">
		<h2>
			{{ t('sketch_picker', 'Draw a sketch') }}
		</h2>
		<ImageEditor
			:src="initialImageUrl"
			@submit="onEditorSubmit" />
	</div>
</template>

<script>
import ImageEditor from '../components/ImageEditor.vue'

import axios from '@nextcloud/axios'
import { generateOcsUrl, generateUrl, imagePath } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
	name: 'SketchCustomPickerElement',

	components: {
		ImageEditor,
	},

	props: {
		providerId: {
			type: String,
			required: true,
		},
		accessible: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			initialImageUrl: imagePath('sketch_picker', 'white.png'),
		}
	},

	computed: {
	},

	watch: {
	},

	mounted() {
	},

	beforeDestroy() {
	},

	methods: {
		async onEditorSubmit({ fileName, mimeType, blob }) {
			try {
				this.loading = true
				const content = await this.readBlob(blob)
				const params = {
					base64Content: content,
					mimeType,
				}
				const url = generateOcsUrl('apps/sketch_picker/api/v1/sketches')
				return axios.post(url, params)
					.then((response) => {
						this.onSubmit(response.data.ocs.data.userId, response.data.ocs.data.name)
					})
					.catch((error) => {
						console.debug('sketch_picker request error', error)
						showError(
							t('sketch_picker', 'Failed to save image')
							+ ': ' + (
								error.response?.data?.body?.error?.message
								|| error.response?.data?.body?.error?.code
								|| error.response?.data?.error
								|| t('sketch_picker', 'Unknown Sketch Picker API error')
							)
						)
					})
					.then(() => {
						this.loading = false
					})
			} catch (e) {
				console.error('image reading error', e.message)
			}
		},
		readBlob(blob) {
			const reader = new FileReader()
			return new Promise((resolve) => {
				reader.addEventListener('load', () => {
					resolve(reader.result)
				})
				reader.readAsDataURL(blob)
			})
		},
		onSubmit(userId, fileName) {
			const internalLink = window.location.protocol + '//' + window.location.host
				+ generateUrl('/apps/sketch_picker/sketches/{userId}/{fileName}', { userId, fileName })
			this.$emit('submit', internalLink)
		},
	},
}
</script>

<style scoped lang="scss">
.sketch-picker-content {
	width: 100%;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	overflow-y: auto;
	max-height: 800px;
	padding: 12px 16px 0 16px;

	h2 {
		display: flex;
		align-items: center;
	}

}
</style>
