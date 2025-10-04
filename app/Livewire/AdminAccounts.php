<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Hash;

#[Title('Admin Accounts')]
class AdminAccounts extends Component
{
    public $admins;

    public $name;
    public $email;
    public $password;

    public $updateName;
    public $updateEmail;


    public function mount()
    {
        $this->fetchAdmins();
    }

    public function createAdmin()
    {
        $this->validate([
            'name' => 'required|string|unique:users|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

    
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'admin',
        ]);

        $this->fetchAdmins();
        $this->reset(['name','email','password']);
        $this->modal('add-admin')->close();
        Toaster::success('Admin Created Successfully!');
        
    }

    public function removeAdmin($id)
    {
        $removeAdmin = User::find($id); 

        $removeAdmin->delete();
        $this->fetchAdmins();
        $this->modal('delete-admin'.$id)->close();
        Toaster::success('Admin Removed Successfully!');
    }

    public $selectedAdmin = null;
    public function editAdmin($id)
    {
        $this->selectedAdmin = User::find($id);
        $this->updateName = $this->selectedAdmin->name;
        $this->updateEmail = $this->selectedAdmin->email;

    }

    public function updateAdmin($id)
    {
        $this->validate([
            'updateName' => 'required|string|unique:users,name,'.$id.'|max:255',
            'updateEmail' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);


        $admin = User::find($id);
        $admin->name = $this->updateName;
        $admin->email = $this->updateEmail;
        $admin->save();


        $this->reset(['updateName', 'updateEmail', 'selectedAdmin']);

        $this->fetchAdmins();
        $this->modal('update-admin'.$id)->close();
        Toaster::success('Admin Updated Successfully!');
    }

    public function fetchAdmins()
    {
        $this->admins = User::where('role', 'admin')
                        ->where('id', '!=', auth()->id())
                        ->get();
    }

    public function render()
    {
        return view('livewire.admin-accounts');
    }
}
