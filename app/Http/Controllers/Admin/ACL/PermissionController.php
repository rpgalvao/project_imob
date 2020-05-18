<?php

namespace LaraDev\Http\Controllers\Admin\ACL;

use Illuminate\Http\Request;
use LaraDev\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();

        return view('admin.permissions.index', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = Permission::where('name', $request->name)->get();
        if($permission->count() > 0){
            return redirect()->back()->withInput()->with(['color' => 'orange', 'message' => 'Ooops, essa permissão já está em uso :(']);
        }

        $permission = new Permission();
        $permission->name = $request->name;
        $permission->save();

        return redirect()->route('admin.permission.index')
            ->with(['color' => 'green', 'message' => 'Permissão cadastrada com sucesso!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::findById($id);

        return view('admin.permissions.edit', [
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::where('name', $request->name)->where('id', '!=', $id)->get();
        if($permission->count() > 0){
            return redirect()->back()->withInput()->with(['color' => 'orange', 'message' => 'Ooops, essa permissão já está em uso :(']);
        }

        $permission = Permission::findById($id);

        $permission->name = $request->name;
        $permission->save();

        return  redirect()->route('admin.permission.index')
            ->with(['color' => 'green', 'message' => 'Permissão alterada com sucesso!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findById($id);
        $permission->delete();

        return redirect()->route('admin.permission.index')
            ->with(['color' => 'orange', 'message' => 'Permissão excluída com sucesso!']);
    }
}
