<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    use HttpResponses;

    public function store(Request $request)

    {

        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }
        $comment = $request->input('comment');
        $user  = Auth::user();

        $comments =   Comment::create([
            'comment' => $comment,
            'user_id' => $user->id,
            'post_id' => $request->input('post_id'),
        ]);


        return $this->success([
            'comments' => $comments,
        ], 'Comment Posted Successfully');

    }

    public function fetchAllCommentsInPost(Request $request, $postId)
    {

        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);


        // Paginate the transactions

        $comment =CommentResource::collection( Comment::where('post_id', $postId)->orderBy('created_at', 'DESC')
            ->paginate($pageSize, ['*'], 'page', $page));


        // Transform the history items to include the full image URL
        $commentItems = $comment->map(function ($item)  {
            return $item;
        });

        // Check if there are more records
        $hasNextRecord = $comment->currentPage() < $comment->lastPage();

        return $this->success([
            'hasNextRecord' => $hasNextRecord,
            'totalCount' => $comment->total(),
            'comment' => $commentItems,
        ], 'Comment retrieved successfully', 200);
    }
}
