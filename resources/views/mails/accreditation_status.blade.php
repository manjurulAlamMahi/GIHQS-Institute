<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accreditation Application Status Update</title>
</head>
<body style="font-family: 'Outfit', 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937; background-color: #f3f4f6; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <!-- Header -->
        @php
            $isApproved = in_array(strtolower($status), ['valid', 'accepted']);
            $isUnderReview = strtolower($status) === 'under_review';
            $isRevoked = strtolower($status) === 'revoked';
            $isExpired = strtolower($status) === 'expired';

            $headerBg = 'linear-gradient(135deg, #4b5563 0%, #6b7280 100%)';
            $headerTitle = 'Accreditation Status Update';
            if ($isApproved) {
                $headerBg = 'linear-gradient(135deg, #059669 0%, #10b981 100%)';
                $headerTitle = 'Accreditation Approved & Valid!';
            } elseif ($isUnderReview) {
                $headerBg = 'linear-gradient(135deg, #0284c7 0%, #38bdf8 100%)';
                $headerTitle = 'Application Under Review';
            } elseif ($isRevoked || $isExpired) {
                $headerBg = 'linear-gradient(135deg, #dc2626 0%, #ef4444 100%)';
                $headerTitle = $isRevoked ? 'Accreditation Revoked' : 'Accreditation Expired';
            }
        @endphp
        <tr>
            <td style="background: {{ $headerBg }}; padding: 40px 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                    {{ $headerTitle }}
                </h1>
                <p style="color: #ffffff; margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">
                    Verification ID: {{ $application->verification_code ?: $application->reference_number }}
                </p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin-top: 0; font-size: 16px; color: #374151;">Dear <strong>{{ $application->applicant_name }}</strong>,</p>

                @if($isApproved)
                    <p style="font-size: 15px; color: #4b5563;">We are pleased to inform you that your accreditation application for <strong>{{ $application->program_name }}</strong> (Category: {{ $application->applicant_category }}) has been officially <strong>APPROVED</strong> by the GIHQS Accreditation Council.</p>
                    
                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px; margin: 20px 0;">
                        <p style="margin: 0; font-size: 14px; color: #166534;"><strong>Verification ID:</strong> {{ $application->verification_code }}</p>
                        <p style="margin: 5px 0 0 0; font-size: 14px; color: #166534;"><strong>Issue Date:</strong> {{ $application->issued_at ? $application->issued_at->format('M d, Y') : now()->format('M d, Y') }}</p>
                        <p style="margin: 5px 0 0 0; font-size: 14px; color: #166534;"><strong>Valid Until:</strong> {{ $application->expires_at ? $application->expires_at->format('M d, Y') : '1 Year' }}</p>
                    </div>

                    <p style="font-size: 14px; color: #4b5563;">Your official PDF Accreditation Certificate is attached to this email. You may also download it or verify its validity anytime on our public verification page.</p>

                    <!-- Dashboard Button -->
                    @if(!empty($actionLink))
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ $actionLink }}" style="display: inline-block; background-color: #059669; color: #ffffff; padding: 14px 28px; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #047857;">
                                Go to Applicant Dashboard
                            </a>
                        </div>
                    @endif
                @elseif($isUnderReview)
                    <p style="font-size: 15px; color: #4b5563;">Your accreditation application for <strong>{{ $application->program_name }}</strong> is currently <strong>Under Review</strong> by our auditing committee.</p>
                @elseif($isRevoked)
                    <p style="font-size: 15px; color: #4b5563;">Notice: The accreditation status for <strong>{{ $application->program_name }}</strong> has been marked as <strong>Revoked / Canceled</strong>.</p>
                @elseif($isExpired)
                    <p style="font-size: 15px; color: #4b5563;">Notice: The 1-year validity period for your accreditation of <strong>{{ $application->program_name }}</strong> has <strong>Expired</strong>. You may log in to your dashboard to submit a re-application for renewal.</p>
                @else
                    <p style="font-size: 15px; color: #4b5563;">We have completed the review of your accreditation application for <strong>{{ $application->program_name }}</strong>.</p>
                    <p style="font-size: 15px; color: #4b5563;">Status: <strong>{{ ucfirst(str_replace('_', ' ', $status)) }}</strong></p>
                @endif

                @if(!empty($adminNotes))
                    <!-- Admin Notes -->
                    <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Accreditation Committee Notes</h3>
                    <p style="font-size: 14px; color: #4b5563; margin-top: 0; background-color: #f9fafb; border-left: 4px solid #9ca3af; padding: 15px; border-radius: 0 8px 8px 0; white-space: pre-line;">
                        {{ $adminNotes }}
                    </p>
                @endif

                <!-- Support Contact -->
                <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Support Contact</h3>
                <p style="font-size: 14px; color: #4b5563; margin-top: 0; margin-bottom: 5px;">If you have any questions regarding your application status, please reach out to us:</p>
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
