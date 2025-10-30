<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong><?= $title; ?></strong> </h1>
<div class="container-fluid">
	<div class="card">
		<div class="card-header">
			<h5 class="card-title mb-0">選單管理 (階層式) </h5>
		</div>
		<div class="card-body">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu" type="button" role="tab" aria-controls="menu" aria-selected="true">第一層選單</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="submenu-tab" data-bs-toggle="tab" data-bs-target="#submenu" type="button" role="tab" aria-controls="submenu" aria-selected="false">子選單</button>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<!-- 第一層選單 -->
				<div class="tab-pane fade show active" id="menu" role="tabpanel" aria-labelledby="menu-tab">
					<div class="mt-3">
						<div class="row">
							<div class="col-sm-6">
								<table class="table">
									<thead>
										<tr>
											<th>#</th>
											<th>選單標題</th>
											<th>網址</th>
											<th>圖示</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$no = 1;
										foreach ($MenuCategories as $menu) :
											// 只顯示第一層選單 (parent_id IS NULL)
											if (empty($menu['parent_id'])) :
										?>
											<tr>
												<td><?= $no++; ?></td>
												<td><?= $menu['title']; ?></td>
												<td><?= $menu['url'] ?? '-'; ?></td>
												<td><i class="align-middle" data-feather="<?= $menu['icon'] ?? ''; ?>"></i></td>
											</tr>
										<?php 
											endif;
										endforeach; 
										?>
									</tbody>
								</table>
							</div>
							<div class="col-sm-6">
								<h5 class="fw-bold text-primary">建立第一層選單</h5>
								<hr>
								<form action="<?= base_url('menu-management/create-menu'); ?>" method="post">
									<div class="mb-3">
										<label for="inputMenuTitle" class="form-label">選單標題</label>
										<input type="text" class="form-control <?= ($validation->hasError('inputMenuTitle')) ? 'is-invalid' : ''; ?>" autofocus value="<?= old('inputMenuTitle'); ?>" id="inputMenuTitle" name="inputMenuTitle" required>
										<div class="invalid-feedback">
											<?= $validation->getError('inputMenuTitle'); ?>
										</div>
									</div>
									<div class="mb-3">
										<label for="inputMenuURL" class="form-label">選單網址</label>
										<input type="text" class="form-control <?= ($validation->hasError('inputMenuURL')) ? 'is-invalid' : ''; ?>" value="<?= old('inputMenuURL'); ?>" id="inputMenuURL" name="inputMenuURL">
										<div class="invalid-feedback">
											<?= $validation->getError('inputMenuURL'); ?>
										</div>
									</div>
									<div class="mb-3">
										<label for="inputMenuIcon" class="form-label">圖示 <a href="https://feathericons.com/" target="_blank" rel="noopener noreferrer">(查詢圖示)</a></label>
										<input type="text" class="form-control <?= ($validation->hasError('inputMenuIcon')) ? 'is-invalid' : ''; ?>" value="<?= old('inputMenuIcon'); ?>" id="inputMenuIcon" name="inputMenuIcon">
										<div class="invalid-feedback">
											<?= $validation->getError('inputMenuIcon'); ?>
										</div>
									</div>
									<div class="text-end mt-3">
										<button class="btn btn-primary">儲存選單</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- 子選單 -->
				<div class="tab-pane fade" id="submenu" role="tabpanel" aria-labelledby="submenu-tab">
					<div class="mt-3">
						<div class="row">
							<div class="col-sm-6">
								<table class="table">
									<thead>
										<tr>
											<th>#</th>
											<th>父選單</th>
											<th>子選單標題</th>
											<th>子選單網址</th>
										</tr>
									</thead>
									<tbody>
										<?php
										// 取得所有有子選單的父選單
										$parentMenus = [];
										foreach ($MenuCategories as $menu) {
											if (!empty($menu['parent_id'])) {
												$parentMenus[] = $menu;
											}
										}

										// 取得第一層選單 (用來顯示父選單名稱)
										$rootMenus = [];
										foreach ($MenuCategories as $menu) {
											if (empty($menu['parent_id'])) {
												$rootMenus[$menu['id']] = $menu['title'];
											}
										}

										$no = 1;
										foreach ($parentMenus as $submenu) :
										?>
											<tr>
												<td><?= $no++; ?></td>
												<td><?= $rootMenus[$submenu['parent_id']] ?? '-'; ?></td>
												<td><?= $submenu['title']; ?></td>
												<td><?= $submenu['url'] ?? '-'; ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<div class="col-sm-6">
								<h5 class="fw-bold text-primary">建立子選單</h5>
								<hr>
								<form action="<?= base_url('menu-management/create-submenu'); ?>" method="post">
									<div class="mb-3">
										<label for="inputMenu" class="form-label">父選單</label>
										<select name="inputMenu" id="inputMenu" class="form-control <?= ($validation->hasError('inputMenu')) ? 'is-invalid' : ''; ?>" required>
											<option value=""> -- 選擇父選單 --</option>
											<?php foreach ($MenuCategories as $menu) : 
												// 只顯示第一層選單作為父選單選項
												if (empty($menu['parent_id'])) :
											?>
												<option value="<?= $menu['id']; ?>"><?= $menu['title']; ?></option>
											<?php 
												endif;
											endforeach; ?>
										</select>
										<div class="invalid-feedback">
											<?= $validation->getError('inputMenu'); ?>
										</div>
									</div>
									<div class="mb-3">
										<label for="inputSubmenuTitle" class="form-label">子選單標題</label>
										<input type="text" class="form-control <?= ($validation->hasError('inputSubmenuTitle')) ? 'is-invalid' : ''; ?>" autofocus value="<?= old('inputSubmenuTitle'); ?>" id="inputSubmenuTitle" name="inputSubmenuTitle" required>
										<div class="invalid-feedback">
											<?= $validation->getError('inputSubmenuTitle'); ?>
										</div>
									</div>
									<div class="mb-3">
										<label for="inputSubmenuURL" class="form-label">子選單網址</label>
										<input type="text" class="form-control <?= ($validation->hasError('inputSubmenuURL')) ? 'is-invalid' : ''; ?>" value="<?= old('inputSubmenuURL'); ?>" id="inputSubmenuURL" name="inputSubmenuURL">
										<div class="invalid-feedback">
											<?= $validation->getError('inputSubmenuURL'); ?>
										</div>
									</div>
									<div class="text-end">
										<button class="btn btn-primary">儲存子選單</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>
