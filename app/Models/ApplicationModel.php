<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    /**
     * 取得所有選單項目 (階層式)
     * @param int|false $menuCategoryID 選單ID，false表示取得全部
     * @return array
     */
    public function getMenuCategory($menuCategoryID = false)
    {
        if ($menuCategoryID) {
            return $this->db->table('user_menu_category')
                ->where(['id' => $menuCategoryID['id']])
                ->where('deleted_at IS NULL')
                ->get()->getRowArray();
        }
        return $this->db->table('user_menu_category')
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * 取得所有選單項目 (保留此方法以相容舊程式碼，現在直接回傳 getMenuCategory)
     * @param int|false $menuID 選單ID
     * @return array
     */
    public function getMenu($menuID = false)
    {
        if ($menuID) {
            return $this->db->table('user_menu_category')
                ->where(['id' => $menuID['menu_id']])
                ->where('deleted_at IS NULL')
                ->get()->getRowArray();
        }
        return $this->db->table('user_menu_category')
            ->where('parent_id IS NULL')
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * 取得子選單項目
     * @param int|null $parentID 父選單ID，null表示取得所有子選單
     * @return array
     */
    public function getSubmenu($parentID = null)
    {
        $query = $this->db->table('user_menu_category')->where('deleted_at IS NULL');
        if ($parentID !== null) {
            $query->where('parent_id', $parentID);
        } else {
            $query->where('parent_id IS NOT NULL');
        }
        return $query->orderBy('id', 'ASC')->get()->getResultArray();
    }

    /**
     * 建立選單項目
     * @param array $dataMenuCategory 選單資料
     * @return bool
     */
    public function createMenuCategory($dataMenuCategory)
    {
        $this->db->transBegin();
        $insertData = [
            'title'      => $dataMenuCategory['inputMenuCategory'],
            'parent_id'  => isset($dataMenuCategory['inputParentID']) ? $dataMenuCategory['inputParentID'] : null,
            'url'        => isset($dataMenuCategory['inputMenuURL']) ? $dataMenuCategory['inputMenuURL'] : null,
            'icon'       => isset($dataMenuCategory['inputMenuIcon']) ? $dataMenuCategory['inputMenuIcon'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('user_menu_category')->insert($insertData);
        $menuCategoryID = $this->db->insertID();
        // 預設給 Developer 權限
        $this->db->table('user_access')->insert([
            'role_id'      => 1,
            'menu_item_id' => $menuCategoryID,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    /**
     * 更新選單項目
     * @param array $menuCategoryData 選單資料
     * @return bool
     */
    public function updateMenuCategory($menuCategoryData)
    {
        $updateData = [
            'title'      => $menuCategoryData['inputMenuCategory'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if (isset($menuCategoryData['inputParentID'])) {
            $updateData['parent_id'] = $menuCategoryData['inputParentID'];
        }
        if (isset($menuCategoryData['inputMenuURL'])) {
            $updateData['url'] = $menuCategoryData['inputMenuURL'];
        }
        if (isset($menuCategoryData['inputMenuIcon'])) {
            $updateData['icon'] = $menuCategoryData['inputMenuIcon'];
        }
        return $this->db->table('user_menu_category')->update($updateData, ['id' => $menuCategoryData['id']]);
    }

    /**
     * 建立選單項目 (保留相容性，現在等同於 createMenuCategory)
     * @param array $dataMenu 選單資料
     * @return bool
     */
    public function createMenu($dataMenu)
    {
        $this->db->transBegin();
        $insertData = [
            'parent_id'  => null,  // 第一層選單沒有父選單
            'title'      => $dataMenu['inputMenuTitle'],
            'url'        => isset($dataMenu['inputMenuURL']) ? $dataMenu['inputMenuURL'] : null,
            'icon'       => isset($dataMenu['inputMenuIcon']) ? $dataMenu['inputMenuIcon'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('user_menu_category')->insert($insertData);
        $menuID = $this->db->insertID();
        // 預設給 Developer 權限
        $this->db->table('user_access')->insert([
            'role_id'      => 1,
            'menu_item_id' => $menuID,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    /**
     * 建立子選單項目 (保留相容性)
     * @param array $dataSubmenu 子選單資料
     * @return bool
     */
    public function createSubMenu($dataSubmenu)
    {
        $this->db->transBegin();
        $insertData = [
            'parent_id'  => $dataSubmenu['inputMenu'],
            'title'      => $dataSubmenu['inputSubmenuTitle'],
            'url'        => $dataSubmenu['inputSubmenuURL'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('user_menu_category')->insert($insertData);
        $submenuID = $this->db->insertID();
        // 預設給 Developer 權限
        $this->db->table('user_access')->insert([
            'role_id'      => 1,
            'menu_item_id' => $submenuID,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    /**
     * 依據 URL 取得選單項目
     * @param string $menuUrl 選單URL
     * @return array|null
     */
    public function getMenuByUrl($menuUrl)
    {
        return $this->db->table('user_menu_category')->where(['url' => $menuUrl])->get()->getRowArray();
    }

    /**
     * 取得使用者資料
     * @param string|false $username 帳號
     * @param int|false $userID 使用者ID
     * @return array
     */
    public function getUser($username = false, $userID = false)
    {
        if ($username) {
            return $this->db->table('users')
                ->select('*,users.id AS userID,user_role.id AS role_id')
                ->join('user_role', 'users.role = user_role.id')
                ->where(['username' => $username])
                ->where('users.deleted_at IS NULL')
                ->get()->getRowArray();
        } elseif ($userID) {
            return $this->db->table('users')
                ->select('*,users.id AS userID,user_role.id AS role_id')
                ->join('user_role', 'users.role = user_role.id')
                ->where(['users.id' => $userID])
                ->where('users.deleted_at IS NULL')
                ->get()->getRowArray();
        } else {
            return $this->db->table('users')
                ->select('*,users.id AS userID,user_role.id AS role_id')
                ->join('user_role', 'users.role = user_role.id')
                ->where('users.deleted_at IS NULL')
                ->get()->getResultArray();
        }
    }

    /**
     * 取得使用者可存取的選單分類 (階層式)
     * @param int $role 角色ID
     * @return array
     */
    public function getAccessMenuCategory($role)
    {
        // 取得使用者有權限的選單ID
        $accessibleMenus = $this->db->table('user_access')
            ->where(['role_id' => $role])
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();

        $menuIDs = array_column($accessibleMenus, 'menu_item_id');
        
        if (empty($menuIDs)) {
            return [];
        }

        // 取得所有有權限的選單項目
        return $this->db->table('user_menu_category')
            ->whereIn('id', $menuIDs)
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * 取得使用者可存取的選單項目 (保留相容性)
     * @param int $role 角色ID
     * @return array
     */
    public function getAccessMenu($role)
    {
        $accessibleMenus = $this->db->table('user_access')
            ->where(['role_id' => $role])
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();

        $menuIDs = array_column($accessibleMenus, 'menu_item_id');
        
        if (empty($menuIDs)) {
            return [];
        }

        return $this->db->table('user_menu_category')
            ->whereIn('id', $menuIDs)
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * 取得使用者角色
     * @param int|false $role 角色ID
     * @return array
     */
    public function getUserRole($role = false)
    {
        if ($role) {
            return $this->db->table('user_role')
                ->where(['id' => $role])
                ->where('deleted_at IS NULL')
                ->get()->getRowArray();
        }
        return $this->db->table('user_role')
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();
    }

    /**
     * 建立新使用者
     * @param array $dataUser 使用者資料
     * @return bool
     */
    public function createUser($dataUser)
    {
        return $this->db->table('users')->insert([
            'fullname'    => $dataUser['inputFullname'],
            'username'    => $dataUser['inputUsername'],
            'password'    => password_hash($dataUser['inputPassword'], PASSWORD_DEFAULT),
            'role'        => $dataUser['inputRole'],
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 更新使用者資料
     * @param array $dataUser 使用者資料
     * @return bool
     */
    public function updateUser($dataUser)
    {
        if ($dataUser['inputPassword']) {
            $password = password_hash($dataUser['inputPassword'], PASSWORD_DEFAULT);
        } else {
            $user     = $this->getUser(userID: $dataUser['userID']);
            $password = $user['password'];
        }
        return $this->db->table('users')->update([
            'fullname'    => $dataUser['inputFullname'],
            'username'    => $dataUser['inputUsername'],
            'password'    => $password,
            'role'        => $dataUser['inputRole'],
            'updated_at'  => date('Y-m-d H:i:s')
        ], ['id' => $dataUser['userID']]);
    }

    /**
     * 刪除使用者（軟刪除）
     * @param int $userID 使用者ID
     * @return bool
     */
    public function deleteUser($userID)
    {
        return $this->db->table('users')->update([
            'deleted_at' => date('Y-m-d H:i:s')
        ], ['id' => $userID]);
    }

    /**
     * 建立新角色
     * @param array $dataRole 角色資料
     * @return bool
     */
    public function createRole($dataRole)
    {
        return $this->db->table('user_role')->insert([
            'role_name'  => $dataRole['inputRoleName'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 更新角色
     * @param array $dataRole 角色資料
     * @return bool
     */
    public function updateRole($dataRole)
    {
        return $this->db->table('user_role')->update([
            'role_name'  => $dataRole['inputRoleName'],
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $dataRole['roleID']]);
    }

    /**
     * 刪除角色（軟刪除）
     * @param int $role 角色ID
     * @return bool
     */
    public function deleteRole($role)
    {
        return $this->db->table('user_role')->update([
            'deleted_at' => date('Y-m-d H:i:s')
        ], ['id' => $role]);
    }

    /**
     * 檢查使用者是否有選單存取權限 (簡化版)
     * @param array $dataAccess 包含 roleID 和 menuItemID
     * @return int
     */
    public function checkUserAccess($dataAccess)
    {
        return $this->db->table('user_access')
            ->where([
                'role_id'      => $dataAccess['roleID'],
                'menu_item_id' => $dataAccess['menuItemID']
            ])
            ->where('deleted_at IS NULL')
            ->countAllResults();
    }

    /**
     * 檢查權限記錄是否存在（包含已刪除）
     * @param array $dataAccess 包含 roleID 和 menuItemID
     * @return int
     */
    public function checkMenuPermissionExists($dataAccess)
    {
        return $this->db->table('user_access')
            ->where([
                'role_id'      => $dataAccess['roleID'],
                'menu_item_id' => $dataAccess['menuItemID']
            ])
            ->countAllResults();
    }

    /**
     * 新增選單存取權限
     * @param array $dataAccess 包含 roleID 和 menuItemID
     * @return bool
     */
    public function insertMenuPermission($dataAccess)
    {
        // 先檢查記錄是否存在（包含已刪除）
        $exists = $this->checkMenuPermissionExists($dataAccess);
        
        if ($exists > 0) {
            // 記錄存在，還原軟刪除
            return $this->db->table('user_access')->update([
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ], [
                'role_id'      => $dataAccess['roleID'],
                'menu_item_id' => $dataAccess['menuItemID']
            ]);
        } else {
            // 記錄不存在，新增
            return $this->db->table('user_access')->insert([
                'role_id'      => $dataAccess['roleID'],
                'menu_item_id' => $dataAccess['menuItemID'],
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * 刪除選單存取權限（軟刪除）
     * @param array $dataAccess 包含 roleID 和 menuItemID
     * @return bool
     */
    public function deleteMenuPermission($dataAccess)
    {
        return $this->db->table('user_access')->update([
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], [
            'role_id'      => $dataAccess['roleID'],
            'menu_item_id' => $dataAccess['menuItemID']
        ]);
    }

    // === 以下方法為向後相容，將舊的方法對應到新的簡化版 ===

    /**
     * 檢查使用者是否有選單分類存取權限 (向後相容)
     * @param array $dataAccess 包含 roleID 和 menuCategoryID
     * @return int
     */
    public function checkUserMenuCategoryAccess($dataAccess)
    {
        return $this->checkUserAccess([
            'roleID'     => $dataAccess['roleID'],
            'menuItemID' => $dataAccess['menuCategoryID']
        ]);
    }

    /**
     * 檢查使用者是否有子選單存取權限 (向後相容)
     * @param array $dataAccess 包含 roleID 和 submenuID
     * @return int
     */
    public function checkUserSubmenuAccess($dataAccess)
    {
        return $this->checkUserAccess([
            'roleID'     => $dataAccess['roleID'],
            'menuItemID' => $dataAccess['submenuID']
        ]);
    }

    /**
     * 新增選單分類權限 (向後相容)
     * @param array $dataAccess 包含 roleID 和 menuCategoryID
     * @return bool
     */
    public function insertMenuCategoryPermission($dataAccess)
    {
        return $this->insertMenuPermission([
            'roleID'     => $dataAccess['roleID'],
            'menuItemID' => $dataAccess['menuCategoryID']
        ]);
    }

    /**
     * 刪除選單分類權限 (向後相容)
     * @param array $dataAccess 包含 roleID 和 menuCategoryID
     * @return bool
     */
    public function deleteMenuCategoryPermission($dataAccess)
    {
        return $this->deleteMenuPermission([
            'roleID'     => $dataAccess['roleID'],
            'menuItemID' => $dataAccess['menuCategoryID']
        ]);
    }

    /**
     * 新增子選單權限 (向後相容)
     * @param array $dataAccess 包含 roleID 和 submenuID
     * @return bool
     */
    public function insertSubmenuPermission($dataAccess)
    {
        return $this->insertMenuPermission([
            'roleID'     => $dataAccess['roleID'],
            'menuItemID' => $dataAccess['submenuID']
        ]);
    }

    /**
     * 刪除子選單權限 (向後相容)
     * @param array $dataAccess 包含 roleID 和 submenuID
     * @return bool
     */
    public function deleteSubmenuPermission($dataAccess)
    {
        return $this->deleteMenuPermission([
            'roleID'     => $dataAccess['roleID'],
            'menuItemID' => $dataAccess['submenuID']
        ]);
    }
}
