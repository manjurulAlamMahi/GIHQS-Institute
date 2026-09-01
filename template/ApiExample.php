public function profileInfo()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $userinfo = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'username' => $user->username,
                'avatar' => $user->avatar ? asset($user->avatar) : asset('user.jpg'),
                'role'   => $user->role,
                'city'   => $user->city,
                'zip'   => $user->zip,
            ];

            $response = [
                'userinfo' => $userinfo
            ];

            return $this->successResponse($response, 'Profile fetched successfully.', 200);
        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch Profile.', 500);
        }
    }

    public function bannerSliders()
{
    try {
        $banners = HomeBanner::all();
        $sliders = HomeSlider::all();

        if ($banners->isEmpty() && $sliders->isEmpty()) {
            return $this->errorResponse([], 'No banners or sliders found.', 404);
        }

        $bannerData = $banners->map(function ($item) {
            return [
                'id'          => $item->id,
                'title'       => $item->title,
                'description' => $item->description,
                'image'       => asset($item->image),
                'date'        => $item->date,
            ];
        });

        $sliderData = $sliders->map(function ($item) {
            return [
                'id'          => $item->id,
                'title'       => $item->title,
                'description' => $item->description,
                'image'       => asset($item->image),
                'date'        => $item->date,
            ];
        });

        $response = [
            'banners' => $bannerData,
            'sliders' => $sliderData,
        ];

        return $this->successResponse($response, 'Home Banner & Slider List', 200);
    } catch (Throwable $th) {
        return $this->errorResponse([], 'Failed to fetch home banners and sliders.', 500);
    }
}
