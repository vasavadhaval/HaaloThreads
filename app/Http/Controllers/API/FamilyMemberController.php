<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Import the DB facade

class FamilyMemberController extends Controller
{

    /**
     * Get all family members for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'errors' => ['auth' => ['Please log in to access this resource']],
                ], 401);
            }

            // Get all family members for the user, and load the user relationship
            $familyMembers = $user->familyMembers()->with('user')->get();

            // Transform the data to include the profile image URL.
             $transformedFamilyMembers = $familyMembers->map(function ($member) {
                $memberArray = $member->toArray(); //convert to array
                $memberArray['profile_image_url'] = $member->profile_image_url; // Accessor
                return $memberArray;
            });


            return response()->json([
                'success' => true,
                'message' => 'Family members retrieved successfully',
                'data' => $transformedFamilyMembers,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve family members: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve family members',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
    /**
     * Store multiple family members.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMultiple(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'errors' => ['auth' => ['Please log in to access this resource']],
                ], 401);
            }

            // Define the validation rules for a single family member.  Crucially, we do NOT apply the 'required' rule at the top level of the array.
            $memberRules = [
                'name' => ['required', 'string', 'max:255'],
                'father_name' => ['nullable', 'string', 'max:255'],
                'surname' => ['required', 'string', 'max:255'],
                'age' => ['required', 'integer', 'min:1', 'max:120'],
                'education' => ['nullable', 'string', 'max:255'],
                'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                'occupation' => ['nullable', 'string', 'max:255'],
                'member_type' => ['required', 'string'],
                'marital_status' => ['nullable', 'string', 'in:single,married,divorced,widowed'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ];

            $messages = [
                'blood_group.in' => 'Please select a valid blood group',
                'member_type.in' => 'Invalid family member type',
                'marital_status.in' => 'Invalid marital status',
                'profile_image.image' => 'The file must be an image',
                'profile_image.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed',
                'profile_image.max' => 'Image size must be less than 2MB',
            ];

            // Validate the entire request.  We expect an array of family members.
            $validator = Validator::make($request->all(), [
                'family_members' => ['required', 'array', 'min:1'], // Ensure 'family_members' is an array with at least one element
                'family_members.*' => ['array'], // Each element in the 'family_members' array should also be an array.
                'family_members.*.name' => $memberRules['name'],
                'family_members.*.father_name' => $memberRules['father_name'],
                'family_members.*.surname' => $memberRules['surname'],
                'family_members.*.age' => $memberRules['age'],
                'family_members.*.education' => $memberRules['education'],
                'family_members.*.blood_group' => $memberRules['blood_group'],
                'family_members.*.occupation' => $memberRules['occupation'],
                'family_members.*.member_type' => $memberRules['member_type'],
                'family_members.*.marital_status' => $memberRules['marital_status'],
                'family_members.*.profile_image' => $memberRules['profile_image'], //apply image rule for each member
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $familyMembersData = $validator->validated()['family_members']; // Get the array of family members data

            // Use a database transaction for atomicity.  If any part of the process fails, the entire operation is rolled back.
            DB::beginTransaction();
            try {
                $createdFamilyMembers = [];

                foreach ($familyMembersData as $memberData) {
                    // Handle profile image upload for each member
                    if (isset($memberData['profile_image']) && $memberData['profile_image'] instanceof \Illuminate\Http\UploadedFile) {
                        // Get the existing image filename, if any
                        $existingImage = $user->familyMembers()->where('id', $memberData['id'] ?? null)->value('profile_image');

                        // Delete old image if exists
                        if ($existingImage) {
                            Storage::disk('public')->delete('family_member_images/' . $existingImage);
                        }

                        $imageName = time() . '_' . uniqid() . '.' . $memberData['profile_image']->extension(); // Ensure unique names
                        $memberData['profile_image']->storeAs('family_member_images', $imageName, 'public');
                        $memberData['profile_image'] = $imageName; // Store the filename in the database
                    }
                    $familyMember = $user->familyMembers()->create($memberData);
                    $createdFamilyMembers[] = $familyMember->toArray(); // Collect created members for the response
                }


                DB::commit(); // Commit the transaction

                return response()->json([
                    'success' => true,
                    'message' => 'Family members added successfully',
                    'data' => $createdFamilyMembers,
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack(); // Roll back the transaction on error
                Log::error('Failed to add family members: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add family members.  Error: ' . $e->getMessage(),
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in storeMultiple: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' =>  config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'errors' => ['auth' => ['Please login to access this resource']],
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'father_name' => ['nullable', 'string', 'max:255'],
                'surname' => ['required', 'string', 'max:255'],
                'age' => ['required', 'integer', 'min:1', 'max:120'],
                'education' => ['nullable', 'string', 'max:255'],
                'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                'occupation' => ['nullable', 'string', 'max:255'],
                'member_type' => ['required', 'string'],
                'marital_status' => ['nullable', 'string', 'in:single,married,divorced,widowed'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ], [
                'blood_group.in' => 'Please select a valid blood group',
                'member_type.in' => 'Invalid family member type',
                'marital_status.in' => 'Invalid marital status',
                'profile_image.image' => 'The file must be an image',
                'profile_image.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed',
                'profile_image.max' => 'Image size must be less than 2MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $validatedData = $validator->validated();

            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    Storage::disk('public')->delete('family_member_images/' . $user->profile_image); // Corrected path
                }

                // Store new image
                $imageName = time() . '.' . $request->profile_image->extension();
                $request->profile_image->storeAs('family_member_images', $imageName, 'public');
                $validatedData['profile_image'] = $imageName;
            }


            $familyMember = $user->familyMembers()->create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Family member added successfully',
                'data' => $familyMember,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Family member creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add family member',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
