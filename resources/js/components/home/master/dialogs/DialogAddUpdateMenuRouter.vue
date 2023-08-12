<template>
	<div class="modal fade" id="DialogAddUpdateMenuRouter" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form @submit.prevent="submitDataForm">
					<div class="modal-header">
						<h4 class="modal-title">
							<span v-if="!is_editing">Tạo mới</span>
							<span v-else>Cập nhật</span>
							menu
						</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-group" v-if="menu.id">
							<label>ID</label>
							<input v-model="menu.id" type="text" class="form-control" readonly />
						</div>
						<div class="form-group">
							<label
								>Tiêu đề menu
								<small class="text-red">( * )</small>
							</label>
							<input
								v-model="menu.title"
								type="text"
								class="form-control"
								placeholder="Nhập tiêu đề menu"
								:rules="['rules.required,rules.min']"
								required
							/>
						</div>
						<div class="form-group">
							<label
								>Đường dẫn
								<small class="text-red">( * )</small>
							</label>
							<input
								v-model="menu.link"
								type="text"
								class="form-control"
								placeholder="Nhập đường dẫn menu"
								:rules="['rules.required,rules.min']"
								required
							/>
						</div>
						<div class="form-group">
							<label>Query string </label>
							<input
								v-model="menu.query_string"
								type="text"
								class="form-control"
								placeholder="Nhập query string (nếu có)"
								:rules="['rules.required,rules.min']"
							/>
						</div>
						<div class="form-group">
							<label
								>Icon
								<small class="text-red">( * )</small>
							</label>
							<input
								v-model="menu.icon"
								type="text"
								class="form-control"
								placeholder="Nhập icon menu"
							/>
						</div>
						<div v-if="!menu.children || menu.children.length == 0" class="form-group">
							<label>Route </label>
							<treeselect
								placeholder="No route"
								:disable-branch-nodes="true"
								v-model="menu.route_id"
								@input="updateMenuRoute"
								:options="tree_routes"
							/>
						</div>
						<div class="form-group">
							<label>Roles </label>
							<treeselect
								placeholder="Any roles"
								:multiple="true"
								:disable-branch-nodes="true"
								v-model="menu.role_ids"
								@input="updateMenuRoute"
								:options="tree_roles"
							/>
						</div>

						<div class="form-group">
							<label for="name">Hiển thị thực tế </label>
							<div class="tree-node-inner">
								<div>
									<i :class="menu.icon" style="width: 25px"></i>
									<b>
										{{ menu.title }}
									</b>
									<span style="float: right">
										<a v-if="menu.link == '#'">#</a>
										<a v-else target="_blank" :href="menu.link">{{
											menu.link
										}}</a>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer justify-content-between">
						<button type="submit" class="btn btn-primary mr-auto">Lưu</button>

						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							Đóng
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>
<script>
	import Treeselect from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';

	export default {
		components: {
			Treeselect,
		},
		props: {
			menu: Object,
			tree_routes: Array,
			tree_roles: Array,
			is_editing: Boolean,
		},
		methods: {
			updateMenuRoute(value) {
				let route = this.tree_routes.find((item) => item.id == value);
				if (route) {
					this.menu.route = route.object;
				} else {
					this.menu.route = null;
				}
			},
			submitDataForm() {
				if (this.is_editing) {
					this.$emit('onUpdateMenu', this.menu);
				} else {
					this.$emit('onCreateMenu', this.menu);
				}
				$('#DialogAddUpdateMenuRouter').modal('hide');
			},
		},
	};
</script>
