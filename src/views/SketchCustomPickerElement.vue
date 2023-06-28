<template>
	<div class="sketch-picker-content">
		<h2>
			{{ t('sketch_picker', 'Draw a sketch') }}
		</h2>
		<div v-if="!pickingRecent"
			class="header">
			<NcButton @click="onOpenFile">
				<template #icon>
					<FolderIcon />
				</template>
				{{ t('sketch_picker', 'Open from Files') }}
			</NcButton>
			<NcButton @click="pickingRecent = true">
				<template #icon>
					<HistoryIcon />
				</template>
				{{ t('sketch_picker', 'Open recently seen sketch') }}
			</NcButton>
		</div>
		<RecentSketches v-if="pickingRecent"
			class="recent"
			:sketches="recentlySeenSketches"
			@submit="onPickRecent"
			@cancel="pickingRecent = false" />
		<ImageEditor v-show="!pickingRecent"
			:src="initialImageUrl"
			@submit="onEditorSubmit"
			@cancel="$emit('cancel')" />
	</div>
</template>

<script>
import FolderIcon from 'vue-material-design-icons/Folder.vue'
import HistoryIcon from 'vue-material-design-icons/History.vue'

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import axios from '@nextcloud/axios'
import { generateOcsUrl, generateUrl, imagePath } from '@nextcloud/router'
import { showError, getFilePickerBuilder, FilePickerType } from '@nextcloud/dialogs'

import ImageEditor from '../components/ImageEditor.vue'
import RecentSketches from '../components/RecentSketches.vue'

export default {
	name: 'SketchCustomPickerElement',

	components: {
		RecentSketches,
		ImageEditor,
		NcButton,
		FolderIcon,
		HistoryIcon,
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
			recentlySeenSketches: [],
			pickingRecent: false,
		}
	},

	computed: {
	},

	watch: {
	},

	mounted() {
		this.getRecentlySeenSketches()
	},

	beforeDestroy() {
	},

	methods: {
		getRecentUrl(name) {
			return generateUrl('apps/sketch_picker/sketches/{name}', { name })
		},
		getRecentlySeenSketches() {
			const url = generateOcsUrl('apps/sketch_picker/api/v1/recently-seen')
			return axios.get(url)
				.then((response) => {
					this.recentlySeenSketches = response.data.ocs.data
				})
				.catch((error) => {
					console.debug('sketch_picker request error', error)
				})
		},
		onPickRecent(name) {
			this.initialImageUrl = this.getRecentUrl(name)
			this.pickingRecent = false
		},
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
	padding: 12px 16px 16px 16px;

	h2 {
		display: flex;
		align-items: center;
	}

	.header {
		display: flex;
		align-items: center;
		justify-content: center;
	}
}
</style>
