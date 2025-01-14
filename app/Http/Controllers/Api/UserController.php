<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    use HttpResponses;

    public function getUserProfile(Request $request)
    {
        try {
            // Attempt to get the authenticated user
            $user = JWTAuth::parseToken()->authenticate();

            // Generate a new token for the authenticated user
            $token = JWTAuth::fromUser($user);

            // Add the profile image URL to the user object if it exists
//            $user->profile_image = $user->profile_image ? url('profile_images/' . $user->profile_image) : null;

            // Return the user profile and token
            return $this->success([
                'user' => $user,
                'access_token' => $token,
            ], 'User profile retrieved successfully');
        } catch (TokenInvalidException $e) {
            // Handle token invalid exception
            return $this->error([], 'Invalid token. Please authenticate again.', 401);
        } catch (TokenExpiredException $e) {
            // Handle token expired exception
            return $this->error([], 'Token has expired. Please authenticate again.', 401);
        } catch (TokenBlacklistedException $e) {
            // Handle token blacklisted exception
            return $this->error([], 'Token is blacklisted. Please authenticate again.', 401);
        } catch (\Exception $e) {
            // Handle other exceptions
            return $this->error([], 'Unauthorized access. Please log in again.', 401);
        }
    }


    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 403);
        }

        // Get the authenticated user
        $user = JWTAuth::parseToken()->authenticate();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error([], 'Current password is incorrect', 403);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->success([], 'Password changed successfully');
    }

    public function logout(Request $request)
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->success([], 'Logged out successfully');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->error([], 'Invalid token. Logout failed.', 401);
        } catch (\Exception $e) {
            return $this->error([], 'Something went wrong. Please try again.', 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        // Get the authenticated user
        $user = JWTAuth::parseToken()->authenticate();

        // Delete the user account
        $user->delete();

        return $this->success([], 'Account deleted successfully');
    }

    public function updateUserAndProfile(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            // Validation for User fields
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
//            'phone' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 403);
        }

        // Get the authenticated user
        $user = JWTAuth::parseToken()->authenticate();

        // Update User fields
        $user->update($request->only(['firstname', 'lastname']));





        return $this->success($user, 'Profile updated successfully');
    }


}
