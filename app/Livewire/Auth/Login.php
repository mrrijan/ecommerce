<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Login")]
class Login extends Component
{

    public $email;
    public $password;

    public function save()
    {
        $this->validate([
            "email" => "required|email|max:255|exists:users,email",
            "password" => "required|min:6|max:255"
        ]);

        if(!auth()->attempt(["email" => $this->email, "password" => $this->password])){
           Session::flash("error", "Invalid Credentials");
            return;
        }

        return redirect()->intended();

    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
