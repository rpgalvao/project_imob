<?php

namespace LaraDev\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Http\Requests\Admin\User as UserRequest;
use LaraDev\Support\Cropper;
use LaraDev\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Display a listing of the team.
     *
     * @return \Illuminate\Http\Response
     */
    public function team()
    {
        $users = User::where('admin', 1)->get();
        return view('admin.users.team', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->can == false;
        }

        return view('admin.users.create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $userCreate = User::create($request->all());

        if (!empty($request->file('cover'))) {
            $userCreate->cover = $request->file('cover')->store('user');
            $userCreate->save();
        }

        $rolesRequest = $request->all();
        $roles = null;
        foreach ($rolesRequest as $key => $value) {
            if (Str::is('acl_*', $key) == true){
                $roles[] = Role::findById(ltrim($key, 'acl_'));
            }
        }

        if(!empty($roles)){
            $userCreate->syncRoles($roles);
        }else{
            $userCreate->syncRoles(null);
        }

        return redirect()->route('admin.users.edit', $userCreate->id)
            ->with(['color' => 'green', 'message' => 'Cliente cadastrado com sucesso!']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::where('id', $id)->first();
        $roles = Role::all();
        foreach ($roles as $role) {
            if ($user->hasRole($role->name)) {
                $role->can = true;
            } else {
                $role->can = false;
            }
        }

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::where('id', $id)->first();

        $user->setLessorAttribute($request->lessor);
        $user->setLesseeAttribute($request->lessee);
        $user->setAdminAttribute($request->admin);
        $user->setClientAttribute($request->client);

        if (!empty($request->file('cover'))) {
            Storage::delete($user->cover);
            Cropper::flush($user->cover);
            $user->cover = '';
        }

        $user->fill($request->all());

        if (!empty($request->file('cover'))) {
            $user->cover = $request->file('cover')->store('user');
        }

        if (!$user->save()) {
            return redirect()->back()->withInput()->withErrors();
        }

        $rolesRequest = $request->all();
        $roles = null;
        foreach ($rolesRequest as $key => $value) {
            if (Str::is('acl_*', $key) == true){
                $roles[] = Role::findById(ltrim($key, 'acl_'));
            }
        }

        if(!empty($roles)){
            $user->syncRoles($roles);
        }else{
            $user->syncRoles(null);
        }

        return redirect()->route('admin.users.edit', $user->id)
            ->with(['color' => 'green', 'message' => 'Cliente atualizado com sucesso!']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
