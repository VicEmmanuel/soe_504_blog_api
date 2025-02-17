<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    use HttpResponses;
    public function followUser($userId)
    {
        $authUser = Auth::id();

        // Check if user exists
        $userToFollow = User::find($userId);
        if (!$userToFollow) {
            return $this->error([], 'User not found', 404);
        }

        // Prevent self-following
        if ($authUser == $userId) {
            return $this->error([], 'You cannot follow yourself', 403);
        }

        // Check if already following
        $alreadyFollowing = Follow::where('follower_id', $authUser)->where('following_id', $userId)->exists();

        if ($alreadyFollowing) {
            return $this->error([], 'You are already following this user', 400);
        }

        // Create follow record
        Follow::create([
            'follower_id' => $authUser,
            'following_id' => $userId
        ]);

        return $this->success([], 'User followed successfully');
    }

    public function unfollowUser($userId)
    {
        $authUser = Auth::id();

        // Check if user exists
        $userToUnfollow = User::find($userId);
        if (!$userToUnfollow) {
            return $this->error([], 'User not found', 404);
        }

        // Delete follow record
        $deleted = Follow::where('follower_id', $authUser)->where('following_id', $userId)->delete();

        if ($deleted) {
            return $this->success([], 'User unfollowed successfully');
        } else {
            return $this->error([], 'You are not following this user', 400);
        }
    }

    public function getFollowers()
    {
        $user = Auth::user();
//        dd($user);
        $followers = Follow::where('following_id', $user->id)->with('follower')->get();

        return $this->success($followers, 'Followers retrieved successfully');
    }

    public function getFollowing()
    {
        $user = Auth::user();

        $following = Follow::where('follower_id', $user->id)->with('following')->get();

        return $this->success($following, 'Following retrieved successfully');
    }
}
