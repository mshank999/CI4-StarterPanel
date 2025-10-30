<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="<?= base_url(); ?> ">
            <span class="align-middle"><i>Starter Panel</i></span>
        </a>
        <ul class="sidebar-nav">
            <?php 
            // 區分第一層選單和有父選單的子選單
            $rootMenus = [];
            $childMenus = [];
            foreach ($MenuCategory as $menu) {
                if (empty($menu['parent_id'])) {
                    $rootMenus[] = $menu;
                } else {
                    $childMenus[] = $menu;
                }
            }

            // 處理第一層選單
            foreach ($rootMenus as $menu) :
                // 檢查是否有子選單
                $hasChildren = false;
                $children = [];
                foreach ($childMenus as $child) {
                    if ($child['parent_id'] == $menu['id']) {
                        $hasChildren = true;
                        $children[] = $child;
                    }
                }

                if ($hasChildren) :
            ?>
                <li class="sidebar-item <?= ($segment == $menu['url']) ? 'active' : ''; ?>">
                    <a data-bs-target="#<?= $menu['url'] ?>" data-bs-toggle="collapse" class="sidebar-link collapsed" aria-expanded="<?= ($segment == $menu['url']) ? 'true' : 'false'; ?>">
                        <i class="align-middle" data-feather="<?= $menu['icon']; ?>"></i> <span class="align-middle"><?= $menu['title']; ?></span>
                    </a>
                    <ul id="<?= $menu['url'] ?>" class="sidebar-dropdown list-unstyled collapse <?= ($segment == $menu['url']) ? ' show' : ''; ?>" data-bs-parent="#sidebar">
                        <?php foreach ($children as $subMenu) : ?>
                            <li class="sidebar-item <?= ($subsegment == $subMenu['url']) ? 'active' : ''; ?>">
                                <a class="sidebar-link" href="<?= base_url($menu['url'] . '/' . $subMenu['url']); ?>">
                                    <?= $subMenu['title']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php else : ?>
                <li class="sidebar-item <?= ($segment == $menu['url']) ? 'active' : ''; ?>">
                    <a class="sidebar-link" href="<?= base_url($menu['url']); ?>">
                        <i class="align-middle" data-feather="<?= $menu['icon']; ?>"></i> <span class="align-middle"><?= $menu['title']; ?></span>
                    </a>
                </li>
            <?php 
                endif;
            endforeach; 
            ?>
        </ul>
    </div>
</nav>