<template>
	<div class="container-fluid">
		<b-tabs content-class="mt-3">
			<b-tab title="Thống kê" active>
				<StatisticDashboard />
			</b-tab>
			<b-tab title="Báo cáo">
				<ReportDashboard />
			</b-tab>
		</b-tabs>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import APIHandler, { APIRequest } from './ApiHandler';
	import { saveExcel } from '@progress/kendo-vue-excel-export';
	import StatisticDashboard from './dashboard/StatisticDashboard.vue';
	import ReportDashboard from './dashboard/ReportDashboard.vue';
	export default {
		components: {
			Treeselect,
			ReportDashboard,
			StatisticDashboard,
		},
	};
</script>

<style>
	.title {
		font-size: 1.5rem;
		font-weight: 500;
	}
	.statistic {
		margin: 5px 10px 5px 10px !important;
	}
	.card-header {
		background-color: transparent;
	}
</style>
