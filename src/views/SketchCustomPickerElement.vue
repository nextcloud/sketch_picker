<template>
	<div class="sketch-picker-content">
		<h2>
			{{ t('sketch_picker', 'Draw a sketch') }}
		</h2>
		<NcButton @click="onOpenFile">
			<template #icon>
				<FolderIcon />
			</template>
			{{ t('sketch_picker', 'Open from Files') }}
		</NcButton>
		<ImageEditor
			:src="initialImageUrl"
			@submit="onEditorSubmit" />
	</div>
</template>

<script>
import FolderIcon from 'vue-material-design-icons/Folder.vue'

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import axios from '@nextcloud/axios'
import { generateOcsUrl, generateUrl, imagePath } from '@nextcloud/router'
import { showError, getFilePickerBuilder, FilePickerType } from '@nextcloud/dialogs'

import ImageEditor from '../components/ImageEditor.vue'

export default {
	name: 'SketchCustomPickerElement',

	components: {
		ImageEditor,
		NcButton,
		FolderIcon,
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
		onOpenFile() {
			const picker = getFilePickerBuilder(t('sketch_picker', 'Choose a file to draw a sketch on'))
				.setMultiSelect(false)
				.setMimeTypeFilter(['image/jpeg', 'image/png', 'image/webp'])
				.setModal(true)
				.setType(FilePickerType.Choose)
				.allowDirectories(false)
				.build()

			return picker
				.pick()
				.then((path) => {
					const cleanPath = path.replace(/^\//, '')
					const fileUrl = generateUrl('/remote.php/webdav/' + cleanPath).replace(/\/index\.php\//, '/')
					this.initialImageUrl = fileUrl
				})
				.catch((e) => console.error('not saved', { error: e }))
		},
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
						this.onSubmit(response.data.ocs.data.name)
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
		onSubmit(fileName) {
			const internalLink = window.location.protocol + '//' + window.location.host
				+ generateUrl('/apps/sketch_picker/sketches/{fileName}', { fileName })
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
