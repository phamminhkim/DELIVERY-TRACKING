<template>
	<div
		:ref="`draggable-div`"
		v-drag
		@v-drag-end="elementDraged"
		:style="`top: ${value.top}; left: ${value.left}; position: absolute;`"
	>
		<slot :class="`drag-handler`"></slot>
	</div>
</template>

<script>
	export default {
		name: 'DraggableDiv',
		props: {
			value: Object,
		},
		methods: {
			elementDraged: function (event) {
				event.preventDefault();
				const top = this.convertPxToCm(this.$refs[`draggable-div`].style.top);
				const left = this.convertPxToCm(this.$refs[`draggable-div`].style.left);
				this.value.top = top;
				this.value.left = left;
				this.$emit('input', this.value);
			},
			convertPxToCm(pixcelStyle) {
				const pixcel = Number(pixcelStyle.slice(0, pixcelStyle.indexOf('px')));
				return `${pixcel * 0.026458333333333}cm`;
			},
		},
	};
</script>
