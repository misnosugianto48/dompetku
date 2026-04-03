<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $settings = Setting::where('user_id', $user->id)->pluck('value', 'key')->toArray();

        $reportsEnabled = $settings['reports_enabled'] ?? '0';
        $reportsSendDate = $settings['reports_send_date'] ?? 'end_of_month'; // 'end_of_month' or 'start_of_month'

        return view('settings.index', compact('reportsEnabled', 'reportsSendDate'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'reports_enabled' => 'nullable|boolean',
            'reports_send_date' => 'required|in:end_of_month,start_of_month',
        ]);

        $reportsEnabled = $request->has('reports_enabled') ? '1' : '0';

        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'reports_enabled'],
            ['value' => $reportsEnabled]
        );

        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'reports_send_date'],
            ['value' => $validated['reports_send_date']]
        );

        return redirect()->route('settings.index')->with('success', 'Application settings updated securely.');
    }
}
