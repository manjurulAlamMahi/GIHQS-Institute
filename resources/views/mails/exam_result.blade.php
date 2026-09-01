<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result Update</title>
</head>
<body style="font-family: 'Outfit', 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937; background-color: #f3f4f6; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <!-- Header -->
        <tr>
            <td style="background: {{ $result->status === 'passed' ? 'linear-gradient(135deg, #059669 0%, #10b981 100%)' : 'linear-gradient(135deg, #dc2626 0%, #ef4444 100%)' }}; padding: 40px 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                    {{ $result->status === 'passed' ? 'Exam Passed!' : 'Exam Attempt Failed' }}
                </h1>
                <p style="color: {{ $result->status === 'passed' ? '#a7f3d0' : '#fecaca' }}; margin: 5px 0 0 0; font-size: 14px;">
                    Exam: {{ $exam->exam_title }}
                </p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin-top: 0; font-size: 16px; color: #374151;">Dear <strong>{{ $user->full_name }}</strong>,</p>
                
                @if($result->status === 'passed')
                    <p style="font-size: 15px; color: #4b5563;">Congratulations! You have successfully passed the exam for <strong>{{ $exam->exam_title }}</strong>.</p>
                    
                    @if(!empty($result->certificate_serial_number))
                        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px; text-align: center; margin: 25px 0;">
                            <span style="display: block; font-size: 12px; text-transform: uppercase; color: #166534; font-weight: 600; letter-spacing: 0.5px;">Certificate Serial Number</span>
                            <span style="font-size: 18px; font-weight: 700; color: #14532d; letter-spacing: 0.5px;">{{ $result->certificate_serial_number }}</span>
                        </div>
                    @endif

                    @if(!empty($actionLink))
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ $actionLink }}" style="display: inline-block; background-color: #059669; color: #ffffff; padding: 14px 28px; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #047857;">
                                Download Certificate
                            </a>
                        </div>
                    @endif
                @else
                    <p style="font-size: 15px; color: #4b5563;">We are writing to inform you of the results of your recent exam attempt for <strong>{{ $exam->exam_title }}</strong>.</p>
                    <p style="font-size: 15px; color: #4b5563;">Unfortunately, your score did not reach the passing threshold this time.</p>

                    <!-- Lockout Callout -->
                    <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 15px; margin: 25px 0; border-left: 4px solid #f59e0b;">
                        <span style="display: block; font-size: 13px; font-weight: 700; color: #92400e; margin-bottom: 5px;">Retake Window Wait Policy</span>
                        <span style="font-size: 14px; color: #78350f;">As per the policy for our certification exams, you are required to wait 3 months before booking a retake. Your next eligible retake date is <strong>{{ \Carbon\Carbon::parse($result->created_at)->addMonths(3)->format('F j, Y') }}</strong>. The retake booking option in your dashboard will remain locked until this date.</span>
                    </div>

                    @if(!empty($actionLink))
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ $actionLink }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 14px 28px; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #1d4ed8;">
                                View Dashboard
                            </a>
                        </div>
                    @endif
                @endif

                <!-- Exam Performance Summary -->
                <h3 style="color: #1e3a8a; font-size: 16px; margin: 30px 0 10px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">Performance Summary</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; width: 55%;"><strong>Score Scored</strong></td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #1f2937;">{{ $result->score }} / {{ $result->points_available }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;"><strong>Your Percentage</strong></td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #1f2937;">{{ $result->percentage }}%</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;"><strong>Passing Score Required</strong></td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #1f2937;">{{ $result->percentage_passmark }}%</td>
                    </tr>
                    @if(!empty($result->duration))
                        <tr>
                            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;"><strong>Duration</strong></td>
                            <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #1f2937;">{{ $result->duration }}</td>
                        </tr>
                    @endif
                </table>

                <!-- Support Contact -->
                <p style="font-size: 14px; color: #4b5563; margin-top: 30px;">If you have any technical issues or inquiries about your results, please contact our exam coordinator:</p>
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280; width: 25%;">Email:</td>
                        <td style="padding: 5px 0;"><a href="mailto:support@gihqs.org" style="color: #2563eb; text-decoration: none;">support@gihqs.org</a></td>
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
