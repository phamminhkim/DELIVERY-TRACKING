<template>
	<div
		:ref="`draggable-div-${div_id}`"
		v-drag="{ handle: `.drag-handler-${div_id}` }"
		@v-drag-end="elementDraged"
		:style="`wdiv_idth: fit-content; top: ${value.top}; left: ${value.left};`"
	>
		<slot :class="`drag-handler-${div_id}`"></slot>
	</div>
</template>

<script>
	export default {
		name: 'DraggableDiv',
		props: {
			value: Object,
			div_id: String,
		},
		methods: {
			elementDraged: function (event) {
				event.preventDefault();
				const top = this.convertPxToCm(this.$refs[`draggable-div-${div_id}`].style.top);
				const left = this.convertPxToCm(this.$refs[`draggable-div-${div_id}`].style.left);
				this.value.top = top;
				this.value.left = left;
				this.$emit('input', this.value);
			},
			convertPxToCm(pixcelStyle) {
				const pixcel = Number(pixcelStyle.slice(0, pixcelStyle.indexOf('px')));
				return `${pixcel * 0.02645833}cm`;
			},
		},
	};
</script>
