<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisory Request Update</title>
</head>
<body style="font-family: 'Outfit', 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937; background-color: #f3f4f6; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <!-- Header -->
        <tr>
            <td style="background: {{ $status === 'accepted' ? 'linear-gradient(135deg, #059669 0%, #10b981 100%)' : 'linear-gradient(135deg, #dc2626 0%, #ef4444 100%)' }}; padding: 40px 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                    {{ $status === 'accepted' ? 'Request Accepted!' : 'Request Canceled' }}
                </h1>
                <p style="color: {{ $status === 'accepted' ? '#a7f3d0' : '#fecaca' }}; margin: 5px 0 0 0; font-size: 14px;">
                    Reference Number: {{ $advisoryRequest->reference_number }}
                </p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin-top: 0; font-size: 16px; color: #374151;">Dear <strong>{{ $advisoryRequest->full_name }}</strong>,</p>
                
                @if($status === 'accepted')
                    <p style="font-size: 15px; color: #4b5563;">Thank you for request. We have accepted your Advisory Consultation Request for <strong>{{ $advisoryRequest->service_of_interest }}</strong>.</p>
                    <p style="font-size: 15px; color: #4b5563;">Our advisory board members are reviewing your organizational details and will contact you directly to schedule the first consultation session.</p>

                    <!-- Dashboard Button -->
                    @if(!empty($actionLink))
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ $actionLink }}" style="display: inline-block; background-color: #059669; color: #ffffff; padding: 14px 28px; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #047857;">
                                View Request Details
                            </a>
                        </div>
                    @endif
                @else
                    <p style="font-size: 15px; color: #4b5563;">We have reviewed your advisory consultation request for <strong>{{ $advisoryRequest->service_of_interest }}</strong>.</p>
                    <p style="font-size: 15px; color: #4b5563;">Unfortunately, we are unable to accept your advisory request at this time.</p>
                @endif

                @if(!empty($adminNotes))
                    <!-- Admin Notes -->
                    <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Advisory Committee Notes</h3>
                    <p style="font-size: 14px; color: #4b5563; margin-top: 0; background-color: #f9fafb; border-left: 4px solid #9ca3af; padding: 15px; border-radius: 0 8px 8px 0; white-space: pre-line;">
                        {{ $adminNotes }}
                    </p>
                @endif

                <!-- Support Contact -->
                <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Support Contact</h3>
                <p style="font-size: 14px; color: #4b5563; margin-top: 0; margin-bottom: 5px;">If you have any questions or require clarification regarding your request, please reach out to us:</p>
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280; width: 25%;">Email:</td>
                        <td style="padding: 5px 0;"><a href="mailto:info@gihqs.org" style="color: #2563eb; text-decoration: none;">info@gihqs.org</a></td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #f3f4f6; text-align: center; border-radius: 0 0 12px 12px;">
                <p style="margin: 0; font-size: 12px; color: #9ca3af;">&copy; {{ date('Y') }} {{ config('app.name', 'GIHQS') }}. All rights reserved.</p>
                <p style="margin: 5px 0 0 0; font-size: 11px; color: #d1d5db;">This is an automated notification. Please do not reply directly to this email.</p>
            </td>
        </tr>
    </table>
</body>
</html>
