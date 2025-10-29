<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    // Company Profile
    public function company()
    {
        $company = $this->getCompanySettings();
        
        return view('settings.company', compact('company'));
    }

    public function updateCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $settings = [];
        
        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo
            $oldLogo = $this->getSetting('company_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $file = $request->file('company_logo');
            $fileName = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('company', $fileName, 'public');
            
            $settings['company_logo'] = $filePath;
        }
        
        $settings['company_name'] = $request->company_name;
        $settings['company_email'] = $request->company_email;
        $settings['company_phone'] = $request->company_phone;
        $settings['company_address'] = $request->company_address;
        $settings['company_website'] = $request->company_website;
        
        // Save settings
        foreach ($settings as $key => $value) {
            \DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
        
        activity()
            ->causedBy(auth()->user())
            ->log('Updated company profile settings');
        
        return response()->json([
            'success' => true,
            'message' => 'Company profile updated successfully!',
            'redirect' => route('settings.company')
        ]);
    }

    // General Settings
    public function general()
    {
        $settings = $this->getGeneralSettings();
        
        return view('settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'items_per_page' => 'required|integer|min:5|max:100',
        ]);

        $settings = [
            'app_name' => $request->app_name,
            'timezone' => $request->timezone,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
            'currency' => $request->currency,
            'currency_symbol' => $request->currency_symbol,
            'items_per_page' => $request->items_per_page,
        ];
        
        // Save settings
        foreach ($settings as $key => $value) {
            \DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
        
        activity()
            ->causedBy(auth()->user())
            ->log('Updated general settings');
        
        return response()->json([
            'success' => true,
            'message' => 'General settings updated successfully!',
            'redirect' => route('settings.general')
        ]);
    }

    // Helper methods
    private function getCompanySettings()
    {
        return [
            'company_name' => $this->getSetting('company_name', 'CheckMate Events'),
            'company_email' => $this->getSetting('company_email', 'info@checkmate.com'),
            'company_phone' => $this->getSetting('company_phone', ''),
            'company_address' => $this->getSetting('company_address', ''),
            'company_website' => $this->getSetting('company_website', ''),
            'company_logo' => $this->getSetting('company_logo', ''),
        ];
    }

    private function getGeneralSettings()
    {
        return [
            'app_name' => $this->getSetting('app_name', 'CheckMate CRM'),
            'timezone' => $this->getSetting('timezone', 'Asia/Dhaka'),
            'date_format' => $this->getSetting('date_format', 'Y-m-d'),
            'time_format' => $this->getSetting('time_format', 'H:i'),
            'currency' => $this->getSetting('currency', 'BDT'),
            'currency_symbol' => $this->getSetting('currency_symbol', '৳'),
            'items_per_page' => $this->getSetting('items_per_page', 10),
        ];
    }

    private function getSetting($key, $default = null)
    {
        $setting = \DB::table('settings')->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
