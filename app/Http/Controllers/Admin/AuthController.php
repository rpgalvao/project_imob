<?php

namespace LaraDev\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaraDev\Contract;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Property;
use LaraDev\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() === true) {
            return redirect()->route('admin.home');
        }

        return view('admin.index');
    }

    public function home()
    {
        $lessors = User::lessors()->count();
        $lessees = User::lessees()->count();
        $team = User::where('admin', 1)->count();

        $propertiesAvailable = Property::available()->count();
        $propertiesUnavailable = Property::unavailable()->count();
        $propertiesTotal = Property::all()->count();

        $contractsPending = Contract::contractsPending()->count();
        $contractsActive = Contract::contractsActive()->count();
        $contractsCancelled = Contract::contractsCancelled()->count();
        $contractsFinished = Contract::contractsFinished()->count();
        $contractsTotal = Contract::all()->count();

        $contracts = Contract::orderBy('id', 'DESC')->get();

        $properties = Property::orderBy('id', 'DESC')->limit('3')->get();

        return view('admin.dashboard', [
            'lessors' => $lessors,
            'lessees' => $lessees,
            'team' => $team,
            'propertiesAvailable' => $propertiesAvailable,
            'propertiesUnavailable' => $propertiesUnavailable,
            'propertiesTotal' => $propertiesTotal,
            'contractsPending' => $contractsPending,
            'contractsActive' => $contractsActive,
            'contractsCancelled' => $contractsCancelled,
            'contractsFinished' => $contractsFinished,
            'contractsTotal' => $contractsTotal,
            'contracts' => $contracts,
            'properties' => $properties,
        ]);
    }

    public function login(Request $request)
    {
        if (in_array('', $request->only('email', 'password'))) {
            $json['message'] = $this->message->error('Oooops, informe todos os dados para efetuar o login')->render();
            return response()->json($json);
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $json['message'] = $this->message->error('Oooops, por favor informar um e-mail válido')->render();
            return response()->json($json);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            $json['message'] = $this->message->error('Oooops, o e-mail e/ou a senha são inválidas')->render();
            return response()->json($json);
        }

        if(!$this->isAdmin()){
            Auth::logout();
            $json['message'] = $this->message->error('Oooops, esse usuário não tem permissão para acessar o painel de administração!')->render();
            return response()->json($json);
        }

        $this->authenticated($request->getClientIp());
        $json['redirect'] = route('admin.home');
        return response()->json($json);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    private function isAdmin()
    {
        $user = User::where('id', Auth::user()->id)->first();

        if ($user->admin === 1) {
            return true;
        } else {
            return false;
        }
    }

    private function authenticated(string $ip)
    {
        $user = User::where('id', Auth::user()->id);
        $user->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
        ]);
    }
}
