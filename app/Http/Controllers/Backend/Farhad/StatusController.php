<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatusController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:category,product,user,banner,slider,pathway-question,pathway-result,catalogue,catalogue-is-feature,catalogue-is-trending,catalogue-is-popular,membership-package',
            'id'   => 'required|integer',
            'status' => 'required|in:0,1',
        ]);

        $model = null;

        switch ($request->type) {
            case 'category':
                $model = Category::findOrFail($request->id);
                break;
            case 'product':
                $model = Product::findOrFail($request->id);
                break;
            case 'user':
                $model = User::findOrFail($request->id);
                break;
            case 'banner':
                $model = \App\Models\Banner::findOrFail($request->id);
                break;
            case 'slider':
                $model = \App\Models\Slider::findOrFail($request->id);
                break;
            case 'pathway-question':
                $model = \App\Models\PathwayQuestion::findOrFail($request->id);
                break;
            case 'pathway-result':
                $model = \App\Models\PathwayResult::findOrFail($request->id);
                break;
            case 'catalogue':
                $model = \App\Models\Catalogue::findOrFail($request->id);
                break;
            case 'membership-package':
                $model = \App\Models\MembershipPackage::findOrFail($request->id);
                break;
            case 'catalogue-is-feature':
                $model = \App\Models\Catalogue::findOrFail($request->id);
                $model->is_feature = $request->status;
                $model->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Catalogue featured flag updated successfully!'
                ]);
            case 'catalogue-is-trending':
                $model = \App\Models\Catalogue::findOrFail($request->id);
                $model->is_trending = $request->status;
                $model->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Catalogue trending flag updated successfully!'
                ]);
            case 'catalogue-is-popular':
                $model = \App\Models\Catalogue::findOrFail($request->id);
                $model->is_popular = $request->status;
                $model->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Catalogue popular flag updated successfully!'
                ]);
        }

        $model->status = $request->status;
        $model->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('-', ' ', $request->type)) . ' status updated successfully!'
        ]);
    }
}
