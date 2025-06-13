<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'errors' => ['auth' => ['Please login to access this resource']]
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update user profile
     */


    public function update(Request $request)
{
    try {
        $user = Auth::user(); // You can use this safely if auth:sanctum middleware is applied

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'errors' => ['auth' => ['Please login to access this resource']]
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'username' => ['sometimes', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'surname' => ['sometimes', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'whatsapp_number' => ['sometimes', 'string', 'max:20', 'unique:users,whatsapp_number,' . $user->id],
            'address' => ['nullable', 'string', 'max:500'],
            'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'education' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:13', 'max:120'],
            'marital_status' => ['nullable', 'string', 'in:single,married,divorced,widowed'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
        ], [
            'whatsapp_number.unique' => 'This WhatsApp number is already registered',
            'age.min' => 'Age must be at least 13 years',
            'blood_group.in' => 'Please select a valid blood group',
            'profile_image.image' => 'The file must be an image',
            'profile_image.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $updateData = array_filter($validator->validated(), function ($value) {
            return $value !== null;
        });

            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    Storage::disk('public')->delete('profile_images/'.$user->profile_image);
                }

                // Store new image
                $imageName = time().'.'.$request->profile_image->extension();
                $request->profile_image->storeAs('profile_images', $imageName, 'public');
                $updateData['profile_image'] = $imageName;
            }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->makeHidden(['password', 'remember_token'])
        ]);

    } catch (\Exception $e) {
        \Log::error('Profile update failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Profile update failed',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}
}
