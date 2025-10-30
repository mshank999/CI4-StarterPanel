<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Users extends Seeder
{
	public function run()
	{
		// 建立選單資料 (階層式結構)
		$this->db->table('user_menu_category')->insertBatch([
			// 第一層選單
			[
				'id'			=> 1,
				'parent_id' 	=> null,
				'title' 		=> 'Dashboard',
				'url'    		=> 'dashboard',
				'icon'    		=> 'home',
				'created_at'    => date('Y-m-d H:i:s'),
				'updated_at'    => date('Y-m-d H:i:s')
			],
			[
				'id'			=> 2,
				'parent_id' 	=> null,
				'title' 		=> 'Users',
				'url'    		=> 'users',
				'icon'    		=> 'user',
				'created_at'    => date('Y-m-d H:i:s'),
				'updated_at'    => date('Y-m-d H:i:s')
			],
			[
				'id'			=> 3,
				'parent_id' 	=> null,
				'title' 		=> 'Menu Management',
				'url'    		=> 'menu-management',
				'icon'    		=> 'command',
				'created_at'    => date('Y-m-d H:i:s'),
				'updated_at'    => date('Y-m-d H:i:s')
			],
		]);

		// Database seeding for user role
		$this->db->table('user_role')->insert([
			'id'    			=>  1,
			'role_name'    		=>  'Developer'
		]);

		// Database seeding for users
		$this->db->table('users')->insert([
			'fullname' 		=> 'Developer',
			'username'    	=> 'developer@mail.io',
			'password'    	=>  password_hash('123456', PASSWORD_DEFAULT),
			'role'    		=>  1,
			'created_at'    =>  date('Y-m-d H:i:s'),
			'updated_at'    =>  date('Y-m-d H:i:s')
		]);

		// Database seeding for user access (簡化版)
		// menu_item_id 指向 user_menu_category.id
		$this->db->table('user_access')->insertBatch([
			[
				'role_id'    		=>  1,
				'menu_item_id'  	=>  1  // Dashboard
			],
			[
				'role_id'    		=>  1,
				'menu_item_id'  	=>  2  // Users
			],
			[
				'role_id'    		=>  1,
				'menu_item_id'  	=>  3  // Menu Management
			],
		]);
	}
}
