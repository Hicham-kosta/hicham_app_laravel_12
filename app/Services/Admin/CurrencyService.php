<?php 

namespace App\Services\Admin;

use App\Models\Currency;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CurrencyService
{
        public function currencies(){
        $currencies = Currency::orderBy('is_base', 'desc')->orderBy('code')->get();
        $admin = Auth::guard('admin')->user();
        $status = 'success'; 
        $message = ""; 
        $currenciesModule = [];
        
        // FIX: Use comparison operator == instead of assignment =
        if($admin->role == "admin"){
            $currenciesModule = ['view_access' => 1, 'edit_access' => 1, 'full_access' => 1];
        }else{
            // FIX: Use string 'currencies' instead of variable $currencies
            $cnt = AdminsRole::where(['subadmin_id'=>$admin->id, 'module' => 'currencies'])->count();
            if($cnt == 0){
                $status = 'error';
                $message = 'This feature is restrected for you';
            }else{
                $currenciesModule = AdminsRole::where(['subadmin_id' => $admin->id, 
                'module' => 'currencies']) // FIX: Use string here too
                ->first()
                ->toArray();
            }
        }
        
        return [
            'currencies' => $currencies,
            'currenciesModule' => $currenciesModule,
            'status' => $status,
            'message' => $message
        ];
    }

    public function addEditCurrency($request){
        $data = $request->all();
        if(!empty($data['id'])){
            $currency = Currency::find($data['id']);
            $message = "Currency updated successfully!";
        }else{
            $currency = new Currency;
            $message = "Currency added successfully!";
        }
        $currency->code = strtoupper(trim($data['code'] ?? $currency->code));
        $currency->name = $data['name'] ?? $currency->name;
        $currency->symbol = $data['symbol'] ?? $currency->symbol;
        $isBase = !empty($data['is_base']) ? 1 : 0;

        if($isBase){
            Currency::where('is_base', true)->where('id', '!=', $currency->id)->update(['is_base'=>false]);
            $currency->is_base = 1;
            $currency->rate = 1.00000000;
        }else{
            if(isset($data['rate'])){
                $currency->rate = (float)$data['rate'];
            }
            $currency->is_base = 0;
        }
        $currency->status = isset($data['status']) ? (int)$data['status'] : ($currency->status ?? 1);

        // Flag upload -> public/front/images/flags/
        if($request->hasFile('flag') && $request->file('flag')->isValid()){
            $destination = public_path('front/images/flags/');
            if (!File::exists($destination)) {
                File::makeDirectory(public_path('front/images/flags'), 0755, true, true);

            }
            // Rmove old
            if(!empty($currency->flag) && File::exists($destination . $currency->flag)){
                @unlink($destination . $currency->flag);
            }
            $file = $request->file('flag');
            $ext = $file->getClientOriginalExtension();
            $filename = Str::lower($currency->code ? : 'flag').'-'.time().'.'.$ext;
            $file->move($destination, $filename);
            $currency->flag = $filename;
        }elseif(!empty($data['flag'])){
            $currency->flag = $data['flag'];
        }
        $currency->save();
        return $message;
    }

        public function updateCurrencyStatus($data)
    {
        $currency = Currency::findOrFail($data['currency_id'] ?? $data['id']);
        
        // FIX: Properly prevent disabling base currency
        if($currency->is_base && isset($data['status']) && (int)$data['status'] === 0){
            throw new \Exception("Cannot disable base currency");
        }
        
        if(isset($data['status'])) {
            $status = (int)$data['status'];
        } else {
            $status = $currency->status ? 0 : 1;
        }
        
        $currency->status = $status;
        $currency->save();
        return $currency->status;
    }


    public function deleteCurrency($id)
    {
        $currency = Currency::findOrFail($id);
        if($currency->is_base) return ['status' => false, 'message' => "Cannot delete base currency"];
        // Remove flag from public folder
        $path = public_path('front/images/flags/'.$currency->flag);
        if(!empty($currency->flag) && File::exists($path)){
            @unlink($path);
        }
        $currency->delete();
        return ['status' => true, 'message' => "Currency deleted successfully"];
    }
}