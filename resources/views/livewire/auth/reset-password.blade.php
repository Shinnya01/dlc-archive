<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        if (! session()->has('password_reset_user_id')) {
            $this->redirectRoute('password.request', navigate: true);
        }
    }

    public function resetPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        $userId = session('password_reset_user_id');
        $user   = User::find($userId);

        if (! $user) {
            session()->flash('status', 'User not found.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // Clear session
        session()->forget('password_reset_user_id');

        session()->flash('status', 'Password reset successfully. You can log in now.');
        $this->redirectRoute('login', navigate: true);
    }
};

?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Reset Password" description="Enter your new password" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="resetPassword" class="flex flex-col gap-4">
        <flux:input viewable type="password" wire:model="password" placeholder="New Password" class="border p-2 rounded">
        <flux:input viewable type="password" wire:model="password_confirmation" placeholder="Confirm Password" class="border p-2 rounded">

        <flux:button type="submit" class=" text-white p-2 rounded">Reset Password</flux:button>
    </form>

</div>
