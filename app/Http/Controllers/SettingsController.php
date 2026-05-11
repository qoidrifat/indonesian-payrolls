<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Settings/Index', [
            'settings' => [
                'company_name' => Setting::get('company_name', config('app.name')),
                'company_address' => Setting::get('company_address', 'Kabupaten Bangkalan, Jawa Timur, Indonesia'),
                'company_email' => Setting::get('company_email', ''),
                'company_phone' => Setting::get('company_phone', ''),
                'pay_day' => Setting::get('pay_day', 25, 'int'),
                'currency' => Setting::get('currency', 'IDR'),
                'default_working_hours' => Setting::get('default_working_hours', 8, 'int'),
                'bpjs_kesehatan_employee_rate' => Setting::get('bpjs_kesehatan_employee_rate', 1.0, 'float'),
                'bpjs_kesehatan_company_rate' => Setting::get('bpjs_kesehatan_company_rate', 4.0, 'float'),
                'bpjs_jht_employee_rate' => Setting::get('bpjs_jht_employee_rate', 2.0, 'float'),
                'bpjs_jht_company_rate' => Setting::get('bpjs_jht_company_rate', 3.7, 'float'),
                'bpjs_jp_employee_rate' => Setting::get('bpjs_jp_employee_rate', 1.0, 'float'),
                'bpjs_jp_company_rate' => Setting::get('bpjs_jp_company_rate', 2.0, 'float'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:64'],
            'pay_day' => ['required', 'integer', 'min:1', 'max:28'],
            'currency' => ['required', 'string', 'max:8'],
            'default_working_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'bpjs_kesehatan_employee_rate' => ['required', 'numeric', 'min:0', 'max:10'],
            'bpjs_kesehatan_company_rate' => ['required', 'numeric', 'min:0', 'max:10'],
            'bpjs_jht_employee_rate' => ['required', 'numeric', 'min:0', 'max:10'],
            'bpjs_jht_company_rate' => ['required', 'numeric', 'min:0', 'max:10'],
            'bpjs_jp_employee_rate' => ['required', 'numeric', 'min:0', 'max:10'],
            'bpjs_jp_company_rate' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);

        $types = [
            'pay_day' => 'int',
            'default_working_hours' => 'int',
            'bpjs_kesehatan_employee_rate' => 'float',
            'bpjs_kesehatan_company_rate' => 'float',
            'bpjs_jht_employee_rate' => 'float',
            'bpjs_jht_company_rate' => 'float',
            'bpjs_jp_employee_rate' => 'float',
            'bpjs_jp_company_rate' => 'float',
        ];
        foreach ($data as $key => $value) {
            Setting::set($key, $value, $types[$key] ?? 'string', 'company');
        }

        return back()->with('success', 'Settings saved');
    }
}
