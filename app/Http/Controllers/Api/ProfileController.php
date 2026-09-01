<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\User;
use App\Helpers\MiaHelper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class ProfileController extends Controller
{
    use ApiResponse;
    public function profileInfo()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 403);
        }

        if ($user->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $response = [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'full_name'  => $user->full_name,
            'country'    => $user->country ?? '',
            'email'      => $user->email,
            'phone'      => $user->phone,
            'username'   => $user->username,
            'avatar'     => $user->avatar ? asset($user->avatar) : asset('user.jpg'),
            'role'       => $user->role,
            'address'    => $user->address ?? '',
            'city'       => $user->city    ?? '',
            'zip'        => $user->zip     ?? '',
            'bio'        => $user->bio     ?? '',
        ];

        return $this->successResponse($response, 'Profile fetched successfully.', 200);
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 403);
        }

        if ($user->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'country'    => 'nullable|string|max:255',
            // 'email'   => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'username'   => 'nullable|string|max:100|unique:users,username,' . $user->id,
            'avatar'     => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'address'    => 'nullable|string|max:500',
            'city'       => 'nullable|string|max:255',
            'zip'        => 'nullable|string|max:20',
            'bio'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }
        // Image upload
        if ($request->file('avatar')) {
            MiaHelper::deleteFile($user->avatar); // Delete old image if exists
            $user->avatar = MiaHelper::uploadImageResize($request->file('avatar'), 'user-avatars', 150, 150);
        }

        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name  = $request->last_name  ?? $user->last_name;
        $user->country    = $request->country    ?? $user->country;
        // $user->email   = $request->email      ?? $user->email;
        $user->phone      = $request->phone      ?? $user->phone;
        $user->username   = $request->username   ?? $user->username;
        $user->address    = $request->address    ?? $user->address;
        $user->city       = $request->city       ?? $user->city;
        $user->zip        = $request->zip        ?? $user->zip;
        $user->bio        = $request->bio        ?? $user->bio;
        $user->save();

        $response = [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'full_name'  => $user->full_name,
            'country'    => $user->country   ?? '',
            'email'      => $user->email,
            'phone'      => $user->phone,
            'username'   => $user->username,
            'avatar'     => $user->avatar ? asset($user->avatar) : asset('user.jpg'),
            'role'       => $user->role,
            'address'    => $user->address   ?? '',
            'city'       => $user->city      ?? '',
            'zip'        => $user->zip       ?? '',
            'bio'        => $user->bio       ?? '',
        ];

        return $this->successResponse($response, 'Profile updated successfully.', 200);
    }

    public function changePassword(Request $request)
    {
        // for sanctum Route::middleware('auth:sanctum') and $user = $request->user()
        // $user = Auth::user(); $user = auth()->user(); // works but not best practice

        $user = Auth::guard('api')->user(); //best practice

        // if (!$user) return $this->error([], 'Unauthenticated', 401); // optional

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), $validator->errors()->first(), 400);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->errorResponse([], 'Old password is incorrect', 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->successResponse([], 'Password updated successfully', 200);
    }

    public function changeAddress(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        $user->address = $request->address;
        $user->city = $request->city;
        $user->zip = $request->zip;
        $user->save();

        $response = [
            'address' => $user->address,
            'city' => $user->city,
            'zip' => $user->zip,
        ];

        return $this->successResponse($response, 'Address updated successfully.', 200);
    }

    public function profileDelete(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string',
            'email'    => 'required|email',
            'password' => 'required|string',
            'reason'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        if ($user->email !== $request->email) {
            return $this->errorResponse([], 'Email not matched', 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse([], 'Password not matched', 400);
        }

        // Optional: Save delete reason if needed
        // DeleteReason::create([
        //     'user_id' => $user->id,
        //     'reason'  => $request->reason,
        // ]);

        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            // Token invalidation failed (optional logging)
        }

        $user->delete();

        return $this->successResponse([], 'Profile deleted and logged out successfully', 200);
    }

    // This method need for app publications
    public function appAccountDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string',
            'email'    => 'required|email',
            'password' => 'required|string',
            'reason'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse([], 'Email or Password is incorrect', 400);
        }

        // Delete user account
        // $user->delete();
        // Hard delete the user
        $user->forceDelete();

        return $this->successResponse([], 'Profile deleted successfully', 200);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'user_latitude' => 'required|numeric|between:-90,90',
            'user_longitude' => 'required|numeric|between:-180,180',
        ]);

        $user = Auth::guard('api')->user();

        $user->user_latitude = $request->user_latitude;
        $user->user_longitude = $request->user_longitude;
        $user->save();

        $response = [
            'user_latitude' => $user->user_latitude,
            'user_longitude' => $user->user_longitude,
        ];

        return $this->successResponse($response, 'Location updated successfully', 200);
    }

    public function toggleLanguage(Request $request)
    {
        $user = auth()->user();

        // Toggle logic
        $newLang = $user->language === 'en' ? 'it' : 'en';

        $user->update([
            'language' => $newLang
        ]);

        $response = [
            'language' => $newLang,
        ];

        return $this->successResponse($response, 'Language switched successfully', 200);
    }

    public function dashboardStats()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            // 1. Get user's accessible catalogues (purchased + members only if active membership)
            $purchasedCatalogueIds = \App\Models\Purchase::where('user_id', $user->id)
                ->where('purchase_type', 'catalogue')
                ->where('payment_status', 'paid')
                ->pluck('catalogue_id')
                ->toArray();

            $membersOnlyCatalogueIds = [];
            if ($user->active_paid_membership) {
                $membersOnlyCatalogueIds = \App\Models\Catalogue::where('catalogue_type', 'members only')
                    ->pluck('id')
                    ->toArray();
            }

            $accessibleCatalogueIds = array_unique(array_merge($purchasedCatalogueIds, $membersOnlyCatalogueIds));

            // 2. Get completed catalogues (user passed ALL exams of the catalogue)
            $completedCatalogueIds = [];
            $accessibleCourses = \App\Models\Catalogue::whereIn('id', $accessibleCatalogueIds)
                ->where('service_type', '!=', 'Certification')
                ->get();

            foreach ($accessibleCourses as $course) {
                $courseExamIds = \App\Models\CatalogueExam::where('catalogue_id', $course->id)
                    ->pluck('id')
                    ->toArray();

                $totalExams = count($courseExamIds);

                if ($totalExams > 0) {
                    $passedExamsCount = \App\Models\UserExamResult::where('user_id', $user->id)
                        ->whereIn('catalogue_exam_id', $courseExamIds)
                        ->where('status', 'passed')
                        ->pluck('catalogue_exam_id')
                        ->unique()
                        ->count();

                    if ($passedExamsCount === $totalExams) {
                        $completedCatalogueIds[] = $course->id;
                    }
                }
            }

            $completedCoursesCount = count($completedCatalogueIds);

            // Active courses: total courses except certification (not completed)
            $activeCoursesCount = \App\Models\Catalogue::whereIn('id', $accessibleCatalogueIds)
                ->where('service_type', '!=', 'Certification')
                ->whereNotIn('id', $completedCatalogueIds)
                ->count();

            // Active certification: total certification
            $activeCertificationCount = \App\Models\Catalogue::whereIn('id', $accessibleCatalogueIds)
                ->where('service_type', 'Certification')
                ->count();

            // Exams Pending: All catalogues all exams not taken
            $takenExamIds = \App\Models\UserExamResult::where('user_id', $user->id)
                ->pluck('catalogue_exam_id')
                ->unique()
                ->toArray();

            $pendingExamsCount = \App\Models\CatalogueExam::whereIn('catalogue_id', $accessibleCatalogueIds)
                ->whereNotIn('id', $takenExamIds)
                ->count();

            // CE-Eligible Courses (accessible courses that have credit_earn > 0)
            $ceEligibleCoursesCount = \App\Models\Catalogue::whereIn('id', $accessibleCatalogueIds)
                ->where('credit_earn', '>', 0)
                ->count();

            $response = [
                'stats' => [
                    'active_courses' => $activeCoursesCount,
                    'active_certification' => $activeCertificationCount,
                    'completed_courses' => $completedCoursesCount,
                    'exams_pending' => $pendingExamsCount,
                    'ce_eligible_courses' => $ceEligibleCoursesCount,
                ]
            ];

            return $this->successResponse($response, 'Dashboard statistics fetched successfully.', 200);
        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch dashboard statistics: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Fetch user's dashboard overview (certifications, courses, accreditations sorted by pure recency).
     *
     * GET /profile/dashboard-overview
     */
    public function dashboardOverview()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            // 1. Fetch latest 1 Certification Application (pure recency: latest id)
            $latestCertApp = \App\Models\CertificationApplication::with('catalogue')
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('email', $user->email);
                })
                ->latest('id')
                ->first();

            // 2. Fetch latest 1 accessible Course (purchased or members-only)
            $purchasedCatalogueIds = \App\Models\Purchase::where('user_id', $user->id)
                ->where('purchase_type', 'catalogue')
                ->where('payment_status', 'paid')
                ->pluck('catalogue_id')
                ->toArray();

            $membersOnlyCatalogueIds = [];
            if ($user->active_paid_membership) {
                $membersOnlyCatalogueIds = \App\Models\Catalogue::where('catalogue_type', 'members only')
                    ->pluck('id')
                    ->toArray();
            }

            $accessibleCatalogueIds = array_unique(array_merge($purchasedCatalogueIds, $membersOnlyCatalogueIds));

            $latestCourse = \App\Models\Catalogue::whereIn('id', $accessibleCatalogueIds)
                ->where('service_type', 'Course')
                ->latest('id')
                ->first();

            // 3. Fetch latest 1 Accreditation Application (pure recency: latest id)
            $latestAccreditation = \App\Models\AccreditationApplication::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email_address', $user->email);
            })
            ->latest('id')
            ->first();

            // Existence Check across all datasets
            if (!$latestCertApp && !$latestCourse && !$latestAccreditation) {
                return $this->errorResponse([], 'No dashboard overview data found.', 404);
            }

            // Format single latest Certification
            $certificationData = null;
            if ($latestCertApp) {
                $statusLabel = strtoupper($latestCertApp->status);
                if (in_array(strtolower($latestCertApp->status), ['accepted', 'approved'])) {
                    $statusLabel = 'APPROVED';
                } elseif (strtolower($latestCertApp->status) === 'pending') {
                    $statusLabel = 'PENDING';
                }

                $certificationData = [
                    'id'               => $latestCertApp->id,
                    'reference_number' => $latestCertApp->reference_number,
                    'catalogue_id'     => $latestCertApp->catalogue_id,
                    'title'            => $latestCertApp->catalogue?->title ?? '',
                    'status'           => $statusLabel,
                    'applied_date'     => $latestCertApp->created_at ? $latestCertApp->created_at->format('d M Y') : '',
                ];
            }

            // Format single latest Course
            $courseData = null;
            if ($latestCourse) {
                $examResult = \App\Models\UserExamResult::where('user_id', $user->id)
                    ->whereHas('catalogueExam', function ($q) use ($latestCourse) {
                        $q->where('catalogue_id', $latestCourse->id);
                    })
                    ->latest('id')
                    ->first();

                $progressPercentage = 0;
                if ($examResult) {
                    if ($examResult->status === 'passed') {
                        $progressPercentage = 100;
                    } else {
                        $progressPercentage = (int) $examResult->percentage;
                    }
                }

                $courseData = [
                    'id'                  => $latestCourse->id,
                    'title'               => $latestCourse->title,
                    'service_type'        => $latestCourse->service_type,
                    'progress_percentage' => $progressPercentage,
                    'status_label'        => "Enrolled · {$progressPercentage}% complete",
                ];
            }

            // Format single latest Accreditation
            $accreditationData = null;
            if ($latestAccreditation) {
                $accreditationData = [
                    'id'               => $latestAccreditation->id,
                    'reference_number' => $latestAccreditation->reference_number,
                    'program_name'     => $latestAccreditation->program_name,
                    'status'           => $latestAccreditation->status,
                    'admin_notes'      => $latestAccreditation->admin_notes ?? '',
                    'submission_date'  => $latestAccreditation->created_at ? $latestAccreditation->created_at->format('F j, Y') : '',
                ];
            }

            // Response wrapper
            $response = [
                'certification' => $certificationData,
                'course'        => $courseData,
                'accreditation' => $accreditationData,
            ];

            return $this->successResponse($response, 'Dashboard overview fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch dashboard overview.', 500);
        }
    }
}

