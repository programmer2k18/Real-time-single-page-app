<?php

namespace App\Http\Controllers;

use App\Http\Resources\Reply\ReplyResource;
use App\Models\Question;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of replies on a question.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Question $question)
    {
        $replies = $question->replies;
        return $replies?  response(ReplyResource::collection($replies),200):
            response(['msg'=>'No Available comments yet.'],204);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Question $question, Request $request)
    {
        $validator = $this->validateReplyInputs($request);
        if ($validator->fails())
            return response(['msg'=>'Not complete or invalid parameters',
                'errors'=>$validator->errors()],206);

        $reply = $question->replies()->create($validator->validated());
        return response( new ReplyResource($reply),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question , Reply $reply)
    {
        return response(new ReplyResource($reply),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Question $question, Reply $reply)
    {
        $validator = $this->validateReplyInputs($request);
        if ($validator->fails())
            return response(['msg'=>'Not complete or invalid parameters',
                'errors'=>$validator->errors()],206);
        $reply->update($validator->validated());
        return response( new ReplyResource($reply),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question,Reply $reply)
    {
        return $reply->delete()?
            response(['msg'=>'Comment deleted successfully'],200):
            response(['msg'=>'Something went wrong, Couldn\'t delete this Category'],404);
    }

    public function validateReplyInputs(Request $request){

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|max:255',
            'question_id'=>'sometimes|numeric',
            'user_id'=>'sometimes|numeric'
        ]);
        return $validator;
    }
}
