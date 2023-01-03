<?php

namespace App\Http\Controllers;

use App\Models\Nominee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $positions = Position::all()
            ->map(fn($position) => [
                'id' => $position->id,
                'name' => $position->name,
                'canModify' => !Nominee::hasPosition($position->id)
            ]);
        return Inertia::render('Positions/Index',['positions'=>$positions]);
    }

    private function getValidated(Request $request): array
    {
        $rules = [
            'name' => ['required'],
        ];
        $msg = [
            'required' => 'The :attribute field is required.'
        ];

        return Validator::make($request->all(), $rules, $msg)->validated();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $this->getValidated($request);

        Position::create($validated);

        $message = [
            'New Position created',
            $validated['name'],
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $id = $request->integer('id');

        $validated = $this->getValidated($request);

        $position =  Position::find($id);

        $position->update($validated);

        $message = [
            'Position updated',
            $validated['name'],
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->integer('id');

        $position = Position::find($id);

        $name = $position->name;

        $position->delete();

        $message = [
            $name,
            'Position deleted',
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }
}
