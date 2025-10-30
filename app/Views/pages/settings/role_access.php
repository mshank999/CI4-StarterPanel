<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<div class="container">
    <h1 class="h3 mb-3"><strong><?= $role['role_name']; ?></strong> Access Menu </h1>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Role Access Menu List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover my-0">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Url</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // 區分第一層選單和有父選單的子選單
                        $rootMenus = [];
                        $childMenus = [];
                        foreach ($MenuCategories as $menu) {
                            if (empty($menu['parent_id'])) {
                                $rootMenus[] = $menu;
                            } else {
                                $childMenus[] = $menu;
                            }
                        }

                        // 處理第一層選單
                        foreach ($rootMenus as $menu) :
                            // 取得此選單的子選單
                            $children = [];
                            foreach ($childMenus as $child) {
                                if ($child['parent_id'] == $menu['id']) {
                                    $children[] = $child;
                                }
                            }
                        ?>
                            <tr>
                                <td colspan="2" class="fw-bold"><?= $menu['title']; ?></td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input menu_category_permission" type="checkbox" <?= check_menuCategory_access($role['id'], $menu['id']) ?> data-role="<?= $role['id'] ?>" data-menucategory="<?= $menu['id'] ?>">
                                        <label class="form-check-label">
                                            <?= (check_menuCategory_access($role['id'], $menu['id']) == 'checked') ? 'Access Granted' : 'Access Not Granted' ?>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <?php foreach ($children as $subMenu) : ?>
                                <tr>
                                    <td> &emsp; <?= $subMenu['title']; ?></td>
                                    <td class="d-none d-md-table-cell"><?= $subMenu['url'] ? '/' . $subMenu['url'] : '-'; ?></td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input menu_permission" type="checkbox" <?= check_menu_access($role['id'], $subMenu['id']) ?> data-role="<?= $role['id'] ?>" data-menu="<?= $subMenu['id'] ?>">
                                            <label class="form-check-label">
                                                <?= (check_menu_access($role['id'], $subMenu['id']) == 'checked') ? 'Access Granted' : 'Access Not Granted' ?>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('javascript'); ?>
<script>
    $('.menu_category_permission').on('click', function() {
        const menuCategoryId = $(this).data('menucategory');
        const roleId = $(this).data('role');
        $.ajax({
            url: "<?= base_url('users/change-menu-category-permission'); ?>",
            type: 'post',
            data: {
                menuCategoryID: menuCategoryId,
                roleID: roleId
            },
            success: function() {
                location.reload();
            }
        });
    });
    $('.menu_permission').on('click', function() {
        const menuId = $(this).data('menu');
        const roleId = $(this).data('role');
        $.ajax({
            url: "<?= base_url('users/change-menu-permission'); ?>",
            type: 'post',
            data: {
                menuID: menuId,
                roleID: roleId
            },
            success: function() {
                location.reload();
            }
        });
    });
</script>
<?= $this->endSection(); ?>
