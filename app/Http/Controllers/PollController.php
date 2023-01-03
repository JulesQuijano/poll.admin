<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $polls = Poll::all()->map(fn($poll) => [
            'id' => $poll->id,
            'title' => $poll->title,
            'description' => $poll->description,
            'start' => $poll->start->toDateString(),
            'duration' => $poll->duration,
            'status_id' => $poll->status,
            'status' => $poll->statusStr(),
            'category_id' => $poll->category,
            'category' => $poll->categoryStr(),
            'canEdit' => $poll->canEdit(),
            'canDelete' => $poll->canDelete()
        ]);

        $status = [
            [Poll::Pending, Poll::STATUS_PENDING],
            [Poll::Ongoing, Poll::STATUS_ONGOING],
            [Poll::Concluded, Poll::STATUS_CONCLUDED],
            [Poll::Maintenance, Poll::STATUS_MAINTENANCE],
        ];

        $categories = [
            [Poll::College, Poll::CATEGORY_COLLEGE],
            [Poll::University, Poll::CATEGORY_UNIVERSITY],
        ];

        return Inertia::render('Polls/Index',['polls'=>$polls, 'status'=>$status,'categories'=>$categories]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $this->getValidated($request);

        Poll::create($validated);

        $message = [
          'New Poll Event created',
          $validated['title'],
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

        /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {

        $id = $request->integer('id');

        $validated = $this->getValidated($request);

        $poll = Poll::find($id);

        $poll->update($validated);

        $message = [
            'Poll Event updated',
            $validated['title'],
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function getValidated(Request $request): array
    {
        $rules = [
            'title' => ['required'],
            'description' => ['required'],
            'start' => ['required'],
            'duration' => ['required', 'gt:0'],
            'status' => ['required'],
            'category' => ['required'],
        ];
        $msg = [
            'required' => 'The :attribute field is required.',
            'gt' => 'The :attribute must be at least a day.',
        ];

        return Validator::make($request->all(), $rules, $msg)->validated();
    }

    public function destroy(Request $request)
    {
        $id = $request->integer('id');

        $poll = Poll::find($id);

        $title = $poll->title;

        $poll->delete();

        $message = [
            $title,
            'Poll Event deleted',
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

}
