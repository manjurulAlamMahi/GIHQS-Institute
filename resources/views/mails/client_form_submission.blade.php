<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Received - {{ $requestType }}</title>
</head>
<body style="font-family: 'Outfit', 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937; background-color: #f3f4f6; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 40px 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">Submission Received</h1>
                <p style="color: #bfdbfe; margin: 5px 0 0 0; font-size: 14px;">Thank you for contacting GIHQS</p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin-top: 0; font-size: 16px; color: #374151;">Dear <strong>{{ $clientName }}</strong>,</p>
                <p style="font-size: 15px; color: #4b5563;">We have successfully received your submission for <strong>{{ $requestType }}</strong>. Our team has logged your request, and it is currently being processed.</p>

                <!-- Reference Badge -->
                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px; text-align: center; margin: 25px 0;">
                    <span style="display: block; font-size: 12px; text-transform: uppercase; color: #166534; font-weight: 600; letter-spacing: 0.5px;">Reference Number</span>
                    <span style="font-size: 20px; font-weight: 700; color: #14532d; letter-spacing: 1px;">{{ $referenceNumber }}</span>
                </div>

                <!-- Info Table -->
                <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Submission Details</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; width: 40%;"><strong>Submission Date</strong></td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #1f2937;">{{ $submissionDate }}</td>
                    </tr>
                    @foreach($summaryData as $key => $value)
                        <tr>
                            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; vertical-align: top;"><strong>{{ $key }}</strong></td>
                            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #1f2937; white-space: pre-line;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>

                <!-- Next Steps -->
                <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Next Steps</h3>
                <p style="font-size: 14px; color: #4b5563; margin-top: 0; background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 0 8px 8px 0;">
                    {{ $nextSteps }}
                </p>

                <!-- Support Contact -->
                <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Contact Information</h3>
                <p style="font-size: 14px; color: #4b5563; margin-top: 0; margin-bottom: 5px;">If you need to provide additional details or make amendments to your submission, please reach out to us:</p>
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
                    @if(!empty($supportContact['email']))
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280; width: 25%;">Email:</td>
                            <td style="padding: 5px 0;"><a href="mailto:{{ $supportContact['email'] }}" style="color: #2563eb; text-decoration: none;">{{ $supportContact['email'] }}</a></td>
                        </tr>
                    @endif
                    @if(!empty($supportContact['phone']))
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;">Phone:</td>
                            <td style="padding: 5px 0; color: #1f2937;">{{ $supportContact['phone'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($supportContact['whatsapp']))
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;">WhatsApp:</td>
                            <td style="padding: 5px 0; color: #1f2937;">{{ $supportContact['whatsapp'] }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #f3f4f6; text-align: center; border-radius: 0 0 12px 12px;">
                <p style="margin: 0; font-size: 12px; color: #9ca3af;">&copy; {{ date('Y') }} {{ config('app.name', 'GIHQS') }}. All rights reserved.</p>
                <p style="margin: 5px 0 0 0; font-size: 11px; color: #d1d5db;">This is an automated confirmation email. Please do not reply directly to this message.</p>
            </td>
        </tr>
    </table>
</body>
</html>
