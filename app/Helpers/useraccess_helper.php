<?php

if (!function_exists('check_menu_access')) {
    /**
     * 檢查選單存取權限 (簡化版)
     * @param int $role_id 角色ID
     * @param int $menu_item_id 選單項目ID
     * @return string|null
     */
    function check_menu_access($role_id, $menu_item_id)
    {
        $db = \Config\Database::connect();
        $accessMenu = $db->table('user_access')
            ->where(['role_id' => $role_id, 'menu_item_id' => $menu_item_id])
            ->where('deleted_at IS NULL')
            ->countAllResults();
        if ($accessMenu > 0) {
            return "checked";
        }
        return null;
    }
}

// === 以下為向後相容的函數 ===

if (!function_exists('check_menuCategory_access')) {
    /**
     * 檢查選單分類存取權限 (向後相容)
     * @param int $role_id 角色ID
     * @param int $menu_category_id 選單分類ID
     * @return string|null
     */
    function check_menuCategory_access($role_id, $menu_category_id)
    {
        return check_menu_access($role_id, $menu_category_id);
    }
}

if (!function_exists('check_submenu_access')) {
    /**
     * 檢查子選單存取權限 (向後相容)
     * @param int $role_id 角色ID
     * @param int $submenu_id 子選單ID
     * @return string|null
     */
    function check_submenu_access($role_id, $submenu_id)
    {
        return check_menu_access($role_id, $submenu_id);
    }
}
