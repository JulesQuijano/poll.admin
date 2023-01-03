<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateAdminPassword;
use App\Models\User;
use App\Notifications\AdminResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{

    public function index()
    {
        $roles = [
            [User::ROLE_SUPER_ADMIN, User::SuperAdmin],
            [User::ROLE_ADMIN, User::Admin],
            [User::ROLE_EMAIL_NOTIFICATION, User::EmailNotification],
        ];

        $status = [
          [User::STATUS_INACTIVE, User::Inactive],
          [User::STATUS_ACTIVE, User::Active],
        ];

        $users = User::all()->map(fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'roleStr' => $user->roleStr(),
            'status' => $user->status,
            'statusStr' => $user->statusStr(),
            'canModify' => $user->canModify(),
            'isSuper' => $user->isSuper(),
        ]);

        return Inertia::render('Users/Index',[
            'users'=>$users,
            'roles'=>$roles,
            'status'=>$status
        ]);
    }

    private function getValidated(Request $request): array
    {
        $rules = [
            'name' => ['required'],
            'email' => ['required'],
            'role' => ['required'],
            'status' => ['required'],
        ];

        $msg = [
            'required' => 'The :attribute field is required.'
        ];

        return Validator::make($request->all(), $rules, $msg)->validated();
    }

    public function store(Request $request)
    {
        $validated = $this->getValidated($request);

        User::create($validated);

        $message = [
            'New User created',
            $validated['name'],
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    public function update(Request $request)
    {
        $id = $request->integer('id');

        $validated = $this->getValidated($request);

        $user = User::find($id);

        $user->update($validated);

        $message = [
            'User updated',
            $validated['name'],
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $id = $request->integer('id');

        $user = User::find($id);

        $name = $user->name;

        $user->delete();

        $message = [
            $name,
            'User deleted',
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }

    public function resetPassword(Request $request)
    {
        $id = $request->get('id');

        $user = User::find($id);

        $password = Str::random(8);
        $delay = now()->addSeconds(5);

        UpdateAdminPassword::dispatch($user,$password);

        $user->notify((new AdminResetPassword($password))->delay($delay));

        $message = [
            'Password has been reset',
            $user->name,
            $password,
        ];

        $request->session()->put('status','ok');
        $request->session()->put('message',$message);

        return redirect()->back();
    }
}
