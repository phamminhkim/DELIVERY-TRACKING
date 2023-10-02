<template>
	<Bar
		:chart-options="chartOptions"
		:chart-data="chartData"
		:chart-id="chartId"
		:dataset-id-key="datasetIdKey"
		:plugins="plugins"
		:css-classes="cssClasses"
		:styles="styles"
		:width="width"
		:height="height"
	/>
</template>

<script>
	import { Bar } from 'vue-chartjs/legacy';

	import {
		Chart as ChartJS,
		Title,
		Tooltip,
		Legend,
		BarElement,
		CategoryScale,
		LinearScale,
	} from 'chart.js';

	ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

	export default {
		name: 'BarChart',
		components: {
			Bar,
		},
		props: {
			chartId: {
				type: String,
				default: 'bar-chart',
			},
			datasetIdKey: {
				type: String,
				default: 'label',
			},
			width: {
				type: Number,
				default: 400,
			},
			height: {
				type: Number,
				default: 400,
			},
			cssClasses: {
				default: '',
				type: String,
			},
			styles: {
				type: Object,
				default: () => {},
			},
			plugins: {
				type: Array,
				default: () => [],
			},

			labels: {
				type: Array,
				default: () => [],
			},
			data: {
				type: Array,
				default: () => [],
			},

			label: {
				type: String,
				default: '',
			},
		},
		data() {
			return {
				chartData: {
					labels: this.labels,
					datasets: [
						{
							label: this.label,
							backgroundColor: this.generateRandomColor(),
							data: this.data,
						},
					],
				},
				chartOptions: {
					responsive: true,
					maintainAspectRatio: false,
					onClick: this.handleClickEnvent,
					// plugins: {
					// 	legend: {
					// 		position: 'bottom',
					// 	},
					// },
				},
			};
		},
		watch: {
			data(val) {
				this.chartData.datasets[0].data = val;
			},
			labels(val) {
				this.chartData.labels = val;
			},
		},
		methods: {
			generateRandomColor() {
				let code = 'rgb(';
				for (let i = 0; i < 3; i++) {
					if (i > 0) {
						code += ',';
					}
					code += Math.floor(Math.random() * 256);
				}
				return `${code})`;
			},
			handleClickEnvent(event, item) {
				if (item.length) {
					const {
						// datasetIndex,
						index,
					} = item[0];
					// const { label } = this.chartData.datasets[datasetIndex];
					// const { labels } = this.chartData;
					// const { data } = this.chartData.datasets[datasetIndex];
					this.$emit('on-click', {
						// label,
						// labels,
						// data,
						index,
					});
				}
			},
		},
		// computed: {
		// 	backgroundColors() {
		// 		return this.data.map(() => {
		// 			let color = this.generateRandomColor();
		// 			console.log(color);
		// 			return color;
		// 		});
		// 	},
		// },
	};
</script>
