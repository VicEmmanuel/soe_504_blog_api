<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use HttpResponses;

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'body' => 'required',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust rules as needed
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Handle image upload
            $imageFile = $request->file('image');
            $newImageName = null;

            if ($imageFile) {
                $newImageName = uniqid() . '.' . $imageFile->extension();
                $imageFile->move(public_path('posts'), $newImageName);
            }

            $user = Auth::user();

            // Create the post
            $post = Post::create([
                'body' => $request->input('body'),
                'image' => $newImageName,
                'user_id' => $user->id,
            ]);

            // Add the full URL for the image path
            if ($newImageName) {
                $post->image = url('posts/' . $newImageName);
            }

            return $this->success([
                'post' => $post,
            ], 'Post Created Successfully');
        } catch (\Exception $e) {
            // Catch any exception and return a proper error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the post. Please try again.',
                'error' => $e->getMessage(), // Optional: Include the error message for debugging (remove in production)
            ], 500);
        }
    }
    public function fetchAllPosts(Request $request)
    {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);

        // Paginate the blog posts
        $post = Post::orderBy('created_at', 'DESC')
            ->paginate($pageSize, ['*'], 'page', $page);

        // No need to modify the image URL since it's stored as a Cloudinary URL
        $postItems = $post->map(function ($item) {
            return $item; // The image field already contains the Cloudinary URL
        });

        // Check if there are more records
        $hasNextRecord = $post->currentPage() < $post->lastPage();

        return $this->success([
            'hasNextRecord' => $hasNextRecord,
            'totalCount' => $post->total(),
            'post' => $postItems,
        ], 'Post retrieved successfully', 200);
    }

}
