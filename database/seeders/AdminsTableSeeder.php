<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $password = Hash::make('123456');
       $admin = new Admin;
       $admin->name = 'El Kostali Hicham';
       $admin->role = 'admin';
       $admin->mobile = '0666666666';
       $admin->email = 'admin@admin.com';
       $admin->password = $password;
       $admin->status = 1;
       $admin->save();

       $admin = new Admin;
         $admin->name = 'Samir El Kostali';
         $admin->role = 'subadmin';
         $admin->mobile = '0766666666';
         $admin->email = 'samir@admin.com';
         $admin->password = $password;
         $admin->status = 1;
            $admin->save();

        $admin = new Admin;
         $admin->name = 'Fillali Omar';
         $admin->role = 'subadmin';
         $admin->mobile = '0776666666';
         $admin->email = 'omar@admin.com';
         $admin->password = $password;
         $admin->status = 1;
            $admin->save();
    }
}
