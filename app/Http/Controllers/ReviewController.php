<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    /**
     * Create a new instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Send an invitation to a user.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'sender_id' => 'required',
            'recipient_id' => 'required',
            'score' => 'required|max:5',
            'review' => 'required|max:255',
            'type' => 'required|max:20'
        ]);

        $this->createReview($request);
    }

    /**
     * Send a brand account invite.
     *
     * @param  Request $request
     * @return Response
     */
    public function createReview(Request $request)
    {
        $review = Review::create([
            'sender_id' => $request->sender_id,
            'recipient_id' => $request->recipient_id,
            'score' => $request->score,
            'type' => $request->type,
            'review' => $request->review
        ]);

        return response()->json([
            'review' => $review
        ]);
    }
}
