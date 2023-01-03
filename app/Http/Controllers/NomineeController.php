<?php

namespace App\Http\Controllers;

use App\Models\Nominee;
use App\Models\Poll;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NomineeController extends Controller
{
    public function index(Poll $poll)
    {
        $count = $poll->nominees->count();
        $nominees = Nominee::query()
            ->where('poll_id', $poll->id)
            ->when(\Illuminate\Support\Facades\Request::input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->cursorPaginate(10)
            ->withQueryString()
            ->through(fn($nominee) => [
                'id' => $nominee->id,
                'name' => $nominee->name,
                'position_id' => $nominee->position_id,
                'position' => Position::find($nominee->position_id)->name,
                'affiliation' => $nominee->affiliation,
                'canModify' => $nominee->votes->count() === 0,
            ]);

        $positions = Position::all();

        return Inertia::render('Nominees/Index', [
            'poll' => $poll,
            'nominees' => $nominees,
            'positions' => $positions,
            'totalCount' => $count,
            'filters' => \Illuminate\Support\Facades\Request::only(['search']),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    private function getValidated(Request $request): array
    {
        $rules = [
            'position_id' => ['required'],
            'name' => ['required'],
            'affiliation' => [],
        ];
        $msg = [
            'required' => 'The :attribute field is required.'
        ];

        return Validator::make($request->all(), $rules, $msg)->validated();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $poll_id = $request->integer('poll_id');

        $validated = $this->getValidated($request);

        $validated['poll_id'] = $poll_id;
        $validated['slug'] = Str::of($validated['name'])->slug('_');

        Nominee::create($validated);

        $message = [
            'New Nominee created',
            $validated['name'],
            Position::find($validated['position_id'])->name
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
        $validated['slug'] = Str::of($validated['name'])->slug('_');

        $nominee = Nominee::find($id);

        $nominee->update($validated);

        $message = [
            'Nominee updated',
            $validated['name'],
            Position::find($validated['position_id'])->name
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $id = $request->integer('id');

        $nominee = Nominee::find($id);

        $name = $nominee->name;

        $nominee->delete();

        $message = [
            $name,
            'Nominee deleted',
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }
}
