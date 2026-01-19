<?php

namespace App\Services\Front;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class VendorService
{
    public function createVendor(array $data): Admin
    {
        return DB::transaction(function() use($data){
            return Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => 'vendor',
                'status' => 0,
                'confirm' => 'No'
            ]);
        });
    }

    public function activateVendor(string $email): Admin
    {
        $vendor = Admin::where('email', $email)
        ->where('role', 'vendor')
        ->firstOrFail();

        $vendor->update([
            'status' => 1,
            'confirm' => 'Yes'
        ]);

        return $vendor;

    }
}
