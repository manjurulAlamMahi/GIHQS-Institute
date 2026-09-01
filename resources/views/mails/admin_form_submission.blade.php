<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Submission Notification</title>
</head>
<body style="font-family: 'Outfit', 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937; background-color: #f9fafb; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 650px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <!-- Banner Alert -->
        <tr>
            <td style="background-color: #dc2626; padding: 15px 30px; text-align: center;">
                <span style="color: #ffffff; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">⚠️ ACTION REQUIRED: NEW SUBMISSION REQUIRES REVIEW</span>
            </td>
        </tr>

        <!-- Header -->
        <tr>
            <td style="background-color: #111827; padding: 40px 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">{{ $requestType }}</h1>
                <p style="color: #9ca3af; margin: 5px 0 0 0; font-size: 14px;">Reference: <strong>{{ $referenceNumber }}</strong></p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <!-- Section: Client Contact Card -->
                <h3 style="color: #111827; font-size: 16px; margin: 0 0 15px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Client Details</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280; width: 30%;"><strong>Name:</strong></td>
                        <td style="padding: 5px 0; color: #111827;"><strong>{{ $clientInfo['name'] ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280;"><strong>Email Address:</strong></td>
                        <td style="padding: 5px 0;"><a href="mailto:{{ $clientInfo['email'] ?? '' }}" style="color: #2563eb; text-decoration: none;">{{ $clientInfo['email'] ?? 'N/A' }}</a></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280;"><strong>Phone Number:</strong></td>
                        <td style="padding: 5px 0; color: #111827;">{{ $clientInfo['phone'] ?? 'N/A' }}</td>
                    </tr>
                    @if(!empty($clientInfo['organization']))
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;"><strong>Organization:</strong></td>
                            <td style="padding: 5px 0; color: #111827;">{{ $clientInfo['organization'] }}</td>
                        </tr>
                    @endif
                </table>

                <!-- Section: Complete Fields -->
                <h3 style="color: #111827; font-size: 16px; margin: 0 0 15px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Complete Submission Data</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; border-collapse: collapse; margin-bottom: 30px;">
                    <tr>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb; color: #6b7280; width: 35%;"><strong>Submitted On</strong></td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb; color: #111827;">{{ $submissionDateTime }}</td>
                    </tr>
                    @foreach($completeData as $label => $value)
                        <tr>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #e5e7eb; color: #6b7280; vertical-align: top;"><strong>{{ $label }}</strong></td>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #e5e7eb; color: #111827; white-space: pre-line;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>

                <!-- Section: Attachments -->
                @if(count($uploadedAttachments) > 0)
                    <h3 style="color: #111827; font-size: 16px; margin: 0 0 15px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Uploaded Attachments</h3>
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; margin-bottom: 30px;">
                        @foreach($uploadedAttachments as $name => $url)
                            <tr>
                                <td style="padding: 8px 0; color: #111827; vertical-align: middle;">
                                    📁 <strong style="color: #374151;">{{ $name }}:</strong> 
                                    @if($url)
                                        <a href="{{ $url }}" target="_blank" style="color: #2563eb; text-decoration: none; margin-left: 10px;">View / Download</a>
                                    @else
                                        <span style="color: #9ca3af; margin-left: 10px;">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                <!-- CTA Button -->
                <div style="text-align: center; margin: 40px 0 10px 0;">
                    <a href="{{ $adminUrl }}" style="background-color: #1e3a8a; color: #ffffff; text-decoration: none; padding: 15px 30px; font-size: 15px; font-weight: 700; border-radius: 8px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(30,58,138,0.2);">
                        View & Manage Request in Control Panel
                    </a>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb; text-align: center; border-radius: 0 0 12px 12px;">
                <p style="margin: 0; font-size: 12px; color: #9ca3af;">&copy; {{ date('Y') }} {{ config('app.name', 'GIHQS') }} Admin Portal.</p>
            </td>
        </tr>
    </table>
</body>
</html>
