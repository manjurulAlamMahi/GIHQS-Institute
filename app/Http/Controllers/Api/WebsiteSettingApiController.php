<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use App\Traits\ApiResponse;
use Throwable;

class WebsiteSettingApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Website Settings.
     */
    public function getWebsiteSetting()
    {
        try {
            $setting = WebsiteSetting::first();

            if (!$setting) {
                return $this->errorResponse([], 'Website settings not found.', 404);
            }

            $formattedData = [
                'id'              => $setting->id,
                'logo'            => $setting->logo ? asset($setting->logo) : null,
                'favicon'         => $setting->favicon ? asset($setting->favicon) : null,
                'company_name'    => $setting->company_name,
                'tag_line'        => $setting->tag_line,
                'phone_number'    => $setting->phone_number,
                'whatsapp_number' => $setting->whatsapp_number,
                'primary_email'   => $setting->email,
                'support_email'   => $setting->support_email,
                'company_address' => $setting->company_address,
                'copyright_text'  => $setting->copyright_text,
            ];

            $response = [
                'website_setting' => $formattedData,
            ];

            return $this->successResponse($response, 'Website settings fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch website settings.', 500);
        }
    }
}
