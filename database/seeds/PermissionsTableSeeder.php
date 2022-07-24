<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => '1',
                'title' => 'user_management_access',
            ],
            [
                'id'    => '2',
                'title' => 'permission_create',
            ],
            [
                'id'    => '3',
                'title' => 'permission_edit',
            ],
            [
                'id'    => '4',
                'title' => 'permission_show',
            ],
            [
                'id'    => '5',
                'title' => 'permission_delete',
            ],
            [
                'id'    => '6',
                'title' => 'permission_access',
            ],
            [
                'id'    => '7',
                'title' => 'role_create',
            ],
            [
                'id'    => '8',
                'title' => 'role_edit',
            ],
            [
                'id'    => '9',
                'title' => 'role_show',
            ],
            [
                'id'    => '10',
                'title' => 'role_delete',
            ],
            [
                'id'    => '11',
                'title' => 'role_access',
            ],
            [
                'id'    => '12',
                'title' => 'user_create',
            ],
            [
                'id'    => '13',
                'title' => 'user_edit',
            ],
            [
                'id'    => '14',
                'title' => 'user_show',
            ],
            [
                'id'    => '15',
                'title' => 'user_delete',
            ],
            [
                'id'    => '16',
                'title' => 'user_access',
            ],
            [
                'id'    => '17',
                'title' => 'status_create',
            ],
            [
                'id'    => '18',
                'title' => 'status_edit',
            ],
            [
                'id'    => '19',
                'title' => 'status_show',
            ],
            [
                'id'    => '20',
                'title' => 'status_delete',
            ],
            [
                'id'    => '21',
                'title' => 'status_access',
            ],
            [
                'id'    => '22',
                'title' => 'priority_create',
            ],
            [
                'id'    => '23',
                'title' => 'priority_edit',
            ],
            [
                'id'    => '24',
                'title' => 'priority_show',
            ],
            [
                'id'    => '25',
                'title' => 'priority_delete',
            ],
            [
                'id'    => '26',
                'title' => 'priority_access',
            ],
            [
                'id'    => '27',
                'title' => 'category_create',
            ],
            [
                'id'    => '28',
                'title' => 'category_edit',
            ],
            [
                'id'    => '29',
                'title' => 'category_show',
            ],
            [
                'id'    => '30',
                'title' => 'category_delete',
            ],
            [
                'id'    => '31',
                'title' => 'category_access',
            ],
            [
                'id'    => '32',
                'title' => 'ticket_create',
            ],
            [
                'id'    => '33',
                'title' => 'ticket_edit',
            ],
            [
                'id'    => '34',
                'title' => 'ticket_show',
            ],
            [
                'id'    => '35',
                'title' => 'ticket_delete',
            ],
            [
                'id'    => '36',
                'title' => 'ticket_access',
            ],
            [
                'id'    => '37',
                'title' => 'comment_create',
            ],
            [
                'id'    => '38',
                'title' => 'comment_edit',
            ],
            [
                'id'    => '39',
                'title' => 'comment_show',
            ],
            [
                'id'    => '40',
                'title' => 'comment_delete',
            ],
            [
                'id'    => '41',
                'title' => 'comment_access',
            ],
            [
                'id'    => '42',
                'title' => 'audit_log_show',
            ],
            [
                'id'    => '43',
                'title' => 'audit_log_access',
            ],
            [
                'id'    => '44',
                'title' => 'dashboard_access',
            ],
            [
                'id'    => '45',
                'title' => 'link_generate_create',
            ],
            [
                'id'    => '46',
                'title' => 'link_generate_edit',
            ],
            [
                'id'    => '47',
                'title' => 'link_generate_show',
            ],
            [
                'id'    => '48',
                'title' => 'link_generate_delete',
            ],
            [
                'id'    => '49',
                'title' => 'link_generate_access',
            ],
            [
                'id'    => '50',
                'title' => 'service_create',
            ],
            [
                'id'    => '51',
                'title' => 'service_edit',
            ],
            [
                'id'    => '52',
                'title' => 'service_show',
            ],
            [
                'id'    => '53',
                'title' => 'service_delete',
            ],
            [
                'id'    => '54',
                'title' => 'service_access',
            ],
            [
                'id'    => '55',
                'title' => 'dept_management_access',
            ],
            [
                'id'    => '56',
                'title' => 'dept_create',
            ],
            [
                'id'    => '57',
                'title' => 'dept_edit',
            ],
            [
                'id'    => '58',
                'title' => 'dept_show',
            ],
            [
                'id'    => '59',
                'title' => 'dept_delete',
            ],
            [
                'id'    => '60',
                'title' => 'dept_access',
            ],
            [
                'id'    => '61',
                'title' => 'bill_generate_create',
            ],
            [
                'id'    => '62',
                'title' => 'bill_generate_edit',
            ],
            [
                'id'    => '63',
                'title' => 'bill_generate_show',
            ],
            [
                'id'    => '64',
                'title' => 'bill_generate_delete',
            ],
            [
                'id'    => '65',
                'title' => 'bill_generate_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
