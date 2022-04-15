<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\AuthUserTrait;
use App\Models\ForumComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ForumCommentController extends Controller
{
    use AuthUserTrait;

    public function __construct()
    {
        return auth()->shouldUse('api');
    }

    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $forumId)
    {
        $this->validateRequest();
        $user = $this->getAuthUser();

        $user->forumComments()->create([
            'body' => request('body'),
            'forum_id' => $forumId
            // 'forum_id' => request('id')
            ]);

        return response()->json(['message' => 'Successfully comment posted']);
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(),  [
            'body' => 'required|min:10'
        ]);

        if ($validator->fails()) {
            // return response()->json($validator->errors(), 400);
            response()->json($validator->messages(), 422)->send();
            exit;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $forumId, $commentId)
    {

        $this->validateRequest();
        $forumComment = ForumComment::find($commentId);

        $this->checkOwnership($forumComment->user_id);

        $forumComment->update([
            'body' => request('body')
        ]);

        return response()->json(['message' => 'Successfully comment updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($forumId, $commentId)
    {
        $forumComment = ForumComment::find($commentId);

        $this->checkOwnership($forumComment->user_id);

        $forumComment->delete();

        return response()->json(['message' => 'Successfully comment deleted']);
    }
}
