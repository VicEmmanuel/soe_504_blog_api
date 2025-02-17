<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthenticationController extends Controller
{
    use HttpResponses;

    public function login(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        // Return the first validation error if validation fails
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        // Check if the user exists before attempting to authenticate
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error([], 'User does not exist', 404);
        }

        // Attempt to authenticate the user with JWT
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error([], 'Invalid login details', 401);
        }

        // Return the user data and the access token
        return $this->success([
            'user' => $user,
            'access_token' => $token
        ], 'Login Successful');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:12',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', // Ensure password validation
        ]);

        // Return the first validation error if validation fails
        if ($validator->fails()) {
            $error = $validator->errors()->first(); // Get the first error message
            return $this->error([], $error, 403);
        }

        // Generate a unique referral code

        // Create a new user
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);


        // Send OTP for email verification
        $this->sendOtp($user);

        // Generate a JWT token for the newly created user
        $token = JWTAuth::fromUser($user);

        return $this->success([
            'user' => $user,
            'access_token' => $token
        ], 'Account created successfully');
    }



    public function sendOtp(User $user)
    {
        try {
            // Generate OTP
            $otp = rand(100000, 999999); // 6-digit OTP

            // Set OTP and expiration time (10 minutes from now)
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            $data = [
                'title' => $otp,
                'body' => $otp,
            ];
            $subject = 'Urgent: Your OTP Code - Expires in 10 Minutes';

            Mail::to($user->email)->send(new OtpMail($data, $subject));




            return $this->success([], 'OTP sent successfully');

            // Check if the request was successful
//            if ($response->getStatusCode() == 201) {
//                return $this->success([], 'OTP sent successfully');
//            } else {
//                $responseBody = $response->getBody()->getContents();
//                return $this->error([], 'Failed to send OTP. Response: ' . $responseBody, 500);
//            }

        } catch (\Exception $e) {
            // Return the error message to help diagnose the issue
            return $this->error([], 'Error Sending OTP: ' . $e->getMessage(), 403);
        }
    }



    public function resendOtp(Request $request)
    {
        try {
            // Validate the request to ensure user exists
//            $request->validate([
//                'email' => 'required|email|exists:users,email',
//            ]);

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);
            // Return the first validation error if validation fails
            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 403);
            }
            // Find the user
            $user = User::where('email', $request->input('email'))->firstOrFail();

            // Generate OTP
            $otp = rand(100000, 999999); // 6-digit OTP

            // Set OTP and expiration time (10 minutes from now)
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            $data = [
                'title' => $otp,
                'body' => $otp,
            ];
            $subject = 'Urgent: Your OTP Code - Expires in 10 Minutes';

            Mail::to($user->email)->send(new OtpMail($data, $subject));




            return $this->success([], 'OTP sent successfully');

        } catch (\Exception $e) {
            // Log the error
            // Log::error('Error sending OTP: ' . $e->getMessage());
            return $this->error([], 'Error Sending OTP', 403);        }
    }


    public function verifyOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp || $user->otp_expires_at->isPast()) {
            return $this->error([], 'Invalid or expired OTP', 400);
        }

        // OTP is valid
        $user->otp = null; // Clear OTP after verification
        $user->otp_expires_at = null;
        $user->email_verified_at = now(); // Mark email as verified
        $user->is_email_verified = true; // Set is_email_verified to true
        $user->save();

        return $this->success([], 'Email verified successfully');
    }


    public function forgotPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',

        ]);

        // Return the first validation error if validation fails
        if ($validator->fails()) {
            $error = $validator->errors()->first(); // Get the first error message
            return $this->error([], $error, 403);
        }

//        $request->validate([
//            'email' => 'required|email|exists:users,email',
//        ]);
        // Find the user
        $user = User::where('email', $request->input('email'))->firstOrFail();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        return $this->sendOtp($user);
    }


    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp || $user->otp_expires_at->isPast()) {
            return $this->error([], 'Invalid or expired OTP', 400);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        $user->otp = null; // Clear OTP after reset
        $user->otp_expires_at = null;
        $user->save();

        return $this->success([], 'Password reset successfully');
    }



}
