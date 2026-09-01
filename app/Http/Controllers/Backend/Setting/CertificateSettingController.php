<?php

namespace App\Http\Controllers\Backend\Setting;

use Illuminate\Http\Request;
use App\Models\CertificateSetting;
use App\Http\Controllers\Controller;
use App\Helpers\MiaHelper;

class CertificateSettingController extends Controller
{
    public function edit()
    {
        $setting = CertificateSetting::first();
        if (!$setting) {
            $setting = CertificateSetting::create([
                'certificate_template' => null,
                'chairman_name' => null,
                'chairman_signature' => null,
                'executive_director_name' => null,
                'executive_director_signature' => null,
            ]);
        }
        return view('backend.layouts.settings.certificate_settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'certificate_template'          => 'nullable|file|mimes:html,txt|max:2048',
            'chairman_name'                  => 'nullable|string|max:255',
            'chairman_signature'             => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'executive_director_name'        => 'nullable|string|max:255',
            'executive_director_signature'   => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $setting = CertificateSetting::first();
        if (!$setting) {
            $setting = new CertificateSetting();
        }

        // Handle removals
        if ($request->remove_certificate_template == 1 && $setting->certificate_template) {
            MiaHelper::deleteFile($setting->certificate_template);
            $setting->certificate_template = null;
        }

        if ($request->remove_chairman_signature == 1 && $setting->chairman_signature) {
            MiaHelper::deleteFile($setting->chairman_signature);
            $setting->chairman_signature = null;
        }

        if ($request->remove_executive_director_signature == 1 && $setting->executive_director_signature) {
            MiaHelper::deleteFile($setting->executive_director_signature);
            $setting->executive_director_signature = null;
        }

        // Handle uploads
        if ($request->hasFile('certificate_template')) {
            if ($setting->certificate_template) {
                $setting->certificate_template = MiaHelper::updateFile($setting->certificate_template, $request->file('certificate_template'), 'certificate-settings');
            } else {
                $setting->certificate_template = MiaHelper::uploadFile($request->file('certificate_template'), 'certificate-settings');
            }
        }

        if ($request->hasFile('chairman_signature')) {
            if ($setting->chairman_signature) {
                $setting->chairman_signature = MiaHelper::updateFile($setting->chairman_signature, $request->file('chairman_signature'), 'certificate-settings');
            } else {
                $setting->chairman_signature = MiaHelper::uploadFile($request->file('chairman_signature'), 'certificate-settings');
            }
        }

        if ($request->hasFile('executive_director_signature')) {
            if ($setting->executive_director_signature) {
                $setting->executive_director_signature = MiaHelper::updateFile($setting->executive_director_signature, $request->file('executive_director_signature'), 'certificate-settings');
            } else {
                $setting->executive_director_signature = MiaHelper::uploadFile($request->file('executive_director_signature'), 'certificate-settings');
            }
        }

        $setting->chairman_name = $request->chairman_name;
        $setting->executive_director_name = $request->executive_director_name;

        $setting->save();

        return redirect()->back()->with('t-success', 'Certificate Settings updated successfully.');
    }
}
