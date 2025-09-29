<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ManageUsers extends Component
{
    public $users;

    public function mount()
    {
       $this->fetchUser();
    }

     public function fetchUser()
    {
        $this->users = User::where('role', 'user')
                            ->get();
    }

    public function removeUser($id)
    {
        $removeUser = User::find($id); 

        $removeUser->delete();
        $this->fetchUser();
        $this->modal('delete-user'.$id)->close();
        Toaster::success('User Removed Successfully!');
    }



    public function approveUser($id)
    {
        $user = User::find($id);
        $user->status = 'verified';

        $user->save();
        $this->fetchUsers();
        Toaster::success('User Verified Successfully!');
    }
    
    public function render()
    {
        return view('livewire.manage-users');
    }
}
