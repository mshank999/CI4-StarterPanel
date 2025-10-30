<?php

if (!function_exists('getMenu')) {
    /**
     * 取得選單資料 (階層式選單)
     * @param int $parentID 父選單ID (null表示取得第一層選單)
     * @param int $role 角色ID
     * @return array
     */
    function getMenu($parentID = null, $role)
    {
        $db = \Config\Database::connect();
        
        // 取得使用者有權限的選單ID
        $accessibleMenus = $db->table('user_access')
            ->where(['role_id' => $role])
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();
        
        $menuIDs = array_column($accessibleMenus, 'menu_item_id');
        
        if (empty($menuIDs)) {
            return [];
        }
        
        $query = $db->table('user_menu_category')
            ->whereIn('id', $menuIDs)
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'ASC');
        
        if ($parentID === null) {
            $query->where('parent_id IS NULL');
        } else {
            $query->where('parent_id', $parentID);
        }
        
        return $query->get()->getResultArray();
    }
}

if (!function_exists('getSubMenu')) {
    /**
     * 取得子選單資料
     * @param int $parentID 父選單ID
     * @param int $role 角色ID
     * @return array
     */
    function getSubMenu($parentID, $role)
    {
        return getMenu($parentID, $role);
    }
}
