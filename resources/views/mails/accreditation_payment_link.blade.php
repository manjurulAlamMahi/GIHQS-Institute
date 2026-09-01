<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accreditation Payment Request</title>
</head>
<body style="font-family: 'Outfit', 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937; background-color: #f3f4f6; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 40px 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                    Payment Invoice & Checkout
                </h1>
                <p style="color: #bfdbfe; margin: 5px 0 0 0; font-size: 14px;">
                    Accreditation Reference: {{ $application->reference_number }}
                </p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin-top: 0; font-size: 16px; color: #374151;">Dear <strong>{{ $application->primary_contact_person }}</strong>,</p>
                
                <p style="font-size: 15px; color: #4b5563;">
                    Our accreditation committee has reviewed your application for <strong>{{ $application->program_name }}</strong> (Category: {{ $application->applicant_category }}) for <strong>{{ $application->applicant_name }}</strong>.
                </p>
                <p style="font-size: 15px; color: #4b5563;">
                    Based on your program details, the accreditation processing fee has been set. Please complete your payment using the secure Stripe Checkout link below to proceed with your accreditation process.
                </p>

                <!-- Fee Invoice Box -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin: 25px 0;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Institution / Applicant:</td>
                            <td style="padding: 8px 0; color: #0f172a; text-align: right; font-weight: 600;">{{ $application->applicant_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Program Name:</td>
                            <td style="padding: 8px 0; color: #0f172a; text-align: right;">{{ $application->program_name }}</td>
                        </tr>
                        <tr style="border-top: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1;">
                            <td style="padding: 12px 0; color: #1e3a8a; font-size: 16px; font-weight: 700;">Total Fee Amount:</td>
                            <td style="padding: 12px 0; color: #059669; font-size: 20px; font-weight: 800; text-align: right;">
                                ${{ number_format($amount, 2) }} {{ strtoupper($application->payment_currency ?? 'USD') }}
                            </td>
                        </tr>
                    </table>

                    @if(!empty($paymentDescription))
                        <div style="margin-top: 15px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Payment Details & Notes:</p>
                            <p style="margin: 5px 0 0 0; font-size: 14px; color: #334155; white-space: pre-line;">{{ $paymentDescription }}</p>
                        </div>
                    @endif
                </div>

                <!-- Payment CTA Button -->
                <div style="text-align: center; margin: 35px 0;">
                    <a href="{{ $paymentLink }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; padding: 16px 36px; font-size: 16px; font-weight: 700; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">
                        Complete Payment via Stripe &rarr;
                    </a>
                </div>

                <p style="font-size: 13px; color: #6b7280; text-align: center; margin-bottom: 25px;">
                    If the button above does not work, copy and paste this payment link into your browser:<br>
                    <a href="{{ $paymentLink }}" style="color: #2563eb; word-break: break-all;">{{ $paymentLink }}</a>
                </p>

                <!-- Support Contact -->
                <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Support Contact</h3>
                <p style="font-size: 14px; color: #4b5563; margin-top: 0; margin-bottom: 5px;">If you have any questions or require assistance with your invoice, please contact us:</p>
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
                <p style="margin: 5px 0 0 0; font-size: 11px; color: #d1d5db;">This is an automated notification from GIHQS Accreditation Services.</p>
            </td>
        </tr>
    </table>
</body>
</html>
