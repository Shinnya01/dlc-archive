<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ManageUsers extends Component
{
    public $users;

    public $updateName;
    public $updateStudentNumber;
    public $updateEmail;

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

    public $selectedUser = null;
    public function editUser($id)
    {
        $this->selectedUser = User::find($id);
        $this->updateName = $this->selectedUser->name;
        $this->updateEmail = $this->selectedUser->email;
        $this->updateStudentNumber = $this->selectedUser->student_number;

    }

    public function updateUser($id)
    {
        $this->validate([
            'updateName' => 'required|string|unique:users,name,'.$id.'|max:255',
            'updateEmail' => 'required|string|email|max:255|unique:users,email,'.$id,
            'updateStudentNumber' => 'required|string|max:255|unique:users,student_number,'.$id.'|max:10',
        ]);


        $user = User::find($id);
        $user->name = $this->updateName;
        $user->email = $this->updateEmail;
        $user->student_number = $this->updateStudentNumber;
        $user->save();


        $this->reset(['updateName', 'updateEmail','updateStudentNumber', 'selectedUser']);

        $this->fetchUser();
        $this->modal('edit-user'.$id)->close();
        Toaster::success('User Updated Successfully!');
    }
    public function approveUser($id)
    {
        $user = User::find($id);
        $user->status = 'verified';

        $user->save();
        $this->fetchUser();
        Toaster::success('User Verified Successfully!');
    }
    
    public function render()
    {
        return view('livewire.manage-users');
    }
}
