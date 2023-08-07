<template>
	<div>
		<div class="content-header">
			<div class="container-fluid">
				<div class="row">
					<div class="col-sm-6">
						<h5 class="m-0 text-dark">{{ title }}</h5>
					</div>
					<div class="col-sm-6">
						<div class="float-sm-right">
							<button class="btn btn-info btn-sm" @click="showDialogCreateMenu()">
								<i class="fa fa-plus"></i>
								Tạo mới
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="row mt-1">
							<div class="col-md-8">
								<div class="form-group row">
									<button
										type="button"
										class="btn btn-secondary btn-sm ml-3 mt-1"
										@click="expandAll()"
									>
										<i class="fas fa-plus-square"> Mở rộng tất cả</i>
									</button>
									<button
										type="button"
										class="btn btn-secondary btn-sm ml-3 mt-1"
										@click="collapseAll()"
									>
										<i class="fas fa-minus-square"> Thu gọn tất cả </i>
									</button>
								</div>
							</div>
							<div class="col-md-4">
								<div class="row mt-1">
									<div class="input-group input-group-sm col-12">
										<!-- <input type="search" class="form-control form-control-navbar" placeholder="Search" aria-label="Search"> -->
										<input
											class="form-control form-control-navbar mb-1"
											id="search"
											type="search"
											placeholder="Nhập thông tin tìm kiếm.."
											aria-label="Search"
										/>
										<span class="input-group-append">
											<button
												type="button"
												class="btn btn-default btn-flat mb-1"
											>
												<i class="fas fa-search"></i>
											</button>
											<!-- <button type="button" @click="searchContact()" class="btn btn-default btn-flat"><i class="fas fa-search"></i></button> -->
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<DraggableTree
								ref="nested_tree_menus"
								:data="this.current_tree"
								draggable
								cross-tree
								:indent="30"
								:space="0"
								@change="updateTree"
							>
								<div slot-scope="slotProps">
									<template v-if="!slotProps.data.isDragPlaceHolder">
										<i
											v-if="slotProps.data.icon"
											:class="slotProps.data.icon"
											style="width: 25px"
										/>

										<b
											v-if="
												slotProps.data.children &&
												slotProps.data.children.length
											"
											@click="slotProps.store.toggleOpen(slotProps.data)"
										>
											<i
												:class="
													slotProps.data.open
														? 'fas fa-minus'
														: 'fas fa-plus'
												"
												style="
													font-size: 1rem;
													color: black;
													margin-right: 0.3rem;
												"
											/>
											{{ slotProps.data.text }}
										</b>
										<b v-else>{{ slotProps.data.text }}</b>
										<span style="float: right">
											<b v-if="slotProps.data.route">
												<i class="fas fa-cube" />
												{{ slotProps.data.route.name }} [{{
													slotProps.data.route.component
												}}]
											</b>
											<b
												v-else-if="slotProps.data.children.length == 0"
												class="text-danger"
											>
												No route
											</b>
											{{ slotProps.data.children.length == 0 ? ' | ' : '' }}
											<a v-if="slotProps.data.link == '#'">#</a>
											<a
												v-else
												target="_blank"
												:href="
													slotProps.data.link +
													(slotProps.data.query_string
														? '?' + slotProps.data.query_string
														: '')
												"
											>
												<i class="fas fa-link" />
												{{ slotProps.data.link
												}}{{
													slotProps.data.query_string != ''
														? '?' + slotProps.data.query_string
														: ''
												}}
											</a>
											<button
												class="btn btn-xs"
												@click="editItem(slotProps.data)"
											>
												<i class="fas fa-edit text-blue"></i>
											</button>

											<button
												class="btn btn-xs"
												@click="deleteItem(slotProps.data)"
											>
												<i class="fas fa-trash text-red"></i>
											</button>
										</span>
									</template>
								</div>
							</DraggableTree>
						</div>
					</div>
					<div class="center overlay" v-if="loading">
						<i class="fa fa-spinner fa-spin fa-4x fa-fw gray center"></i>
						<span class="sr-only">Loading...</span>
					</div>
				</div>
			</div>
		</div>

		<dialog-add-update-menu-router
			:menu="menu"
			:tree_routes="tree_routes"
			:is_editing="is_editing"
			@onCreateMenu="onCreateMenu"
			@onUpdateMenu="onUpdateMenu"
		>
		</dialog-add-update-menu-router>
	</div>
</template>

<script>
	import { DraggableTree } from 'vue-draggable-nested-tree';
	import Treeselect from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	import * as th from 'tree-helper';
	import DialogAddUpdateMenuRouter from './dialogs/DialogAddUpdateMenuRouter.vue';

	export default {
		props: {
			title: '',
		},
		components: {
			DraggableTree,
			Treeselect,
			DialogAddUpdateMenuRouter,
		},
		data() {
			return {
				menu: {},
				list_menus: [],
				tree_menus: [],
				root_tree_menus: [],

				tree_routes: [],

				current_tree: [],

				is_editing: false,
				loading: false,

				token: '',
				page_url_menu: '/api/master/menu-routers/configs',
				api_save_menu_routers: '/api/master/menu-routers/save-configs',
				api_delete_menu_routers: '/api/master/menu-routers/',
				api_get_tree_routes: '/api/routes?format=treeselect',
			};
		},
		created() {
			this.token = 'Bearer ' + window.Laravel.access_token;
			this.fetchMenus();
			this.fetchTreeRoutes();
		},

		methods: {
			onCreateMenu(menu) {
				this.current_tree.unshift({
					text: menu.title,
					title: menu.title,
					icon: menu.icon,
					link: menu.link,
					query_string: menu.query_string,
					route_id: menu.route_id,
					route: menu.route,
				});
				this.saveMenus();
			},
			onUpdateMenu(menu) {
				console.log(menu);
				th.breadthFirstSearch(this.current_tree, (node) => {
					if (node.id == menu.id) {
						node.text = menu.title;
						node.title = menu.title;
						node.icon = menu.icon;
						node.link = menu.link;
						node.query_string = menu.query_string;
						node.route_id = menu.route_id;
						node.route = menu.route;
						return false;
					}
				});
				this.saveMenus();
			},
			fetchMenus() {
				this.loading = true;

				var page_url = this.page_url_menu;
				fetch(page_url, { headers: { Authorization: this.token } })
					.then((res) => res.json())
					.then((res) => {
						this.list_menus = res.data.list_menus;
						this.tree_menus = res.data.tree_menus;
						this.root_tree_menus = res.data.root_tree_menus;
						this.loading = false;
					})
					.catch((err) => {
						console.log(err);
						this.loading = false;
					});
				this.is_editing = false;
			},
			fetchTreeRoutes() {
				this.loading = true;

				var page_url = this.api_get_tree_routes;
				fetch(page_url, { headers: { Authorization: this.token } })
					.then((res) => res.json())
					.then((res) => {
						this.tree_routes = res.data;
						this.loading = false;
					})
					.catch((err) => {
						console.log(err);
						this.loading = false;
					});
			},
			saveMenus() {
				let data = this.$refs.nested_tree_menus.getPureData();

				this.loading = true;
				fetch(this.api_save_menu_routers, {
					method: 'POST',
					body: JSON.stringify(data),
					headers: {
						Authorization: this.token,
						'content-type': 'application/json',
					},
				})
					.then((res) => res.json())
					.then((data) => {
						this.loading = false;
						if (data.success) {
							toastr.success(data.message, 'Thành công');
						} else {
							toastr.danger(data.message, 'Thất bại');
						}
					})
					.catch((err) => {
						console.log(err);
						this.loading = false;
					});
			},
			expandAll() {
				th.breadthFirstSearch(this.current_tree, (node) => {
					node.open = true;
				});
			},
			collapseAll() {
				th.breadthFirstSearch(this.current_tree, (node) => {
					node.open = false;
				});
			},
			updateTree(node, targetTree) {
				//this.current_tree = targetTree.getPureData();
				this.saveMenus();
			},
			showDialogCreateMenu() {
				this.is_editing = false;
				this.menu = {};

				$('#DialogAddUpdateMenuRouter').modal('show');
			},
			editItem(menu) {
				this.is_editing = true;
				this.menu = menu;
				//console.log(this.$refs.nested_tree_menus.getPureData());
				$('#DialogAddUpdateMenuRouter').modal('show');
			},
			deleteItem(menu) {
				if (confirm('Bạn có chắc chắn muốn xóa menu này?')) {
					th.breadthFirstSearch(this.current_tree, (node) => {
						if (node.id == menu.id) {
							fetch(this.api_delete_menu_routers + menu.id, {
								method: 'DELETE',
								headers: {
									Authorization: this.token,
									'content-type': 'application/json',
								},
							})
								.then((res) => res.json())
								.then((data) => {
									this.loading = false;
									if (data.success) {
										toastr.success(data.message, 'Thành công');
										this.fetchMenus();
									} else {
										toastr.danger(data.message, 'Thất bại');
									}
								})
								.catch((err) => {
									console.log(err);
									this.loading = false;
								});
							return false;
						}
					});
				}
			},
			convertToTree(menu_id, childs_id) {
				let menu = this.list_menus[menu_id];
				if (menu) {
					var data = {
						id: parseInt(menu_id),
						text: menu.title,
						title: menu.title,
						icon: menu.icon,
						link: menu.link,
						query_string: menu.query_string,
						route_id: menu.route_id,
						route: menu.route,
					};

					if (childs_id && childs_id.length > 0) {
						data.children = [];

						childs_id.forEach((child_menu_id) => {
							let nested_childrens_id = this.tree_menus[child_menu_id];

							data.children.push(
								this.convertToTree(child_menu_id, nested_childrens_id),
							);
						});
					}
					return data;
				}

				return null;
			},
		},
		watch: {
			root_tree_menus() {
				this.current_tree = [];
				this.root_tree_menus.forEach((menu_id) => {
					let menu_childs = [];
					if (this.tree_menus.hasOwnProperty(menu_id)) {
						menu_childs = Object.values(this.tree_menus[menu_id]);
					}
					let menu = this.convertToTree(menu_id, menu_childs);

					if (menu) {
						menu.open = false;
						this.current_tree.push(menu);
					}
				});
			},
		},
	};
</script>
<style lang="scss">
	.he-tree {
		display: block;
		border: 0px solid #000;
		width: 100%;
		padding: 20px;
	}

	.tree-node {
		margin-bottom: 10px;
	}

	.tree-node-handle {
		display: block;
		min-height: 38px;
		margin: 5px 0;
		padding: 8px 12px;
		background: #f8faff;
		border: 1px solid #dae2ea;
		color: #7c9eb2;
		text-decoration: none;
		font-weight: 700;
		box-sizing: border-box;
	}

	.tree-node-handle2 {
		left: 0;
		top: 0;
		width: 36px;
		margin: 0;
		text-align: center;
		padding: 0 !important;
		line-height: 38px;
		height: 38px;
		background: #ebedf2;
		border: 1px solid #dee4ea;
		cursor: pointer;
		overflow: hidden;
		position: absolute;
		z-index: 1;
	}

	.tree-node-inner {
		cursor: pointer;
		margin-bottom: 10px;

		min-height: 38px;
		margin: 5px 0;
		padding: 8px 12px;
		background: #f8faff;
		color: #7c9eb2;
		text-decoration: none;
		font-weight: 700;
	}

	.tree-node-inner:hover {
		background-color: rgba(98, 168, 209, 0.1);
	}

	.draggable-placeholder {
	}

	.draggable-placeholder-inner {
		border: 1px dashed #0088f8;
		box-sizing: border-box;
		background: rgba(0, 136, 249, 0.09);
		color: #0088f9;
		text-align: center;
		padding: 0;
		display: flex;
		align-items: center;
	}
</style>
