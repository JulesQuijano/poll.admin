<?php

namespace App\Http\Controllers;

use App\Imports\VotersImport;
use App\Jobs\PurgeVotersList;
use App\Jobs\UpdateVoterPassword;
use App\Models\Voter;
use App\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;


class VoterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $count = Voter::all()->count();

        $voters = Voter::orderBy('id')->cursorPaginate(10)->through(fn($voter) => [
            'id' => $voter->id,
            'student_number' => $voter->student_number,
            'email' => $voter->email,
            'college' => $voter->college,
            'has_password_request' => $voter->has_password_request
        ]);

        return Inertia::render('Voters/Index', ['voters'=>$voters,'totalVoters'=>$count]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $id = $request->get('voter');

        $raw = '1234';
        $delay = now()->addSeconds(5);

        $voter = Voter::find($id);

        UpdateVoterPassword::dispatch($voter,$raw);

        $voter->notify((new ResetPassword($raw))->delay($delay));

        $request->session()->put([
            'status' => 'ok',
            'message'=> 'Password has been reset',
        ]);

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function importVotersList(Request $request)
    {
        $rules = [
          'csv' => 'required'
        ];

        $messages = [
            'required' => 'CSV file is required.',
        ];

        $validator = Validator::make($request->all(),$rules,$messages);

        $validated = $validator->validate();

        Excel::import(new VotersImport(),$validated['csv']);

        $message = [
            'Voters list has been imported',
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function purgeVotersList(Request $request)
    {

        Voter::truncate();

        $message = [
            'Voters list has been purged',
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        sleep(1);

        return redirect()->back();
    }
}
