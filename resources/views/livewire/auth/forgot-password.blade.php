<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $uid = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $showResetForm = false;

    public function verifyUser(): void
    {
        $this->validate([
            'uid'   => ['required', 'string', 'size:12'],
            'email' => ['required', 'email'],
        ]);

        $user = User::where('uid', $this->uid)
            ->where('email', $this->email)
            ->first();

        if (! $user) {
            session()->flash('status', 'No account found with this UID + Email.');
            return;
        }

        // Save user id in session
        session(['password_reset_user_id' => $user->id]);

        // Show password reset form instead of redirect
        $this->showResetForm = true;
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

        // Success
        session()->flash('status', 'Password reset successfully. You can log in now.');
        $this->redirectRoute('login', navigate: true);
    }
};


?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Forgot Password" description="Enter your UID & Email to reset your password" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    @if (! $showResetForm)
        {{-- Step 1: Verify UID + Email --}}
        <form wire:submit.prevent="verifyUser" class="flex flex-col gap-4">
            <flux:input type="text" wire:model="uid" placeholder="Enter UID (12 chars)" class="rounded"/>
            <flux:input type="email" wire:model="email" placeholder="Enter your email" class="rounded"/>

            <flux:button type="submit" variant="primary" class="w-full">{{ __('Continue') }}</flux:button>
        </form>
    @else
        {{-- Step 2: Reset Password --}}
        <form wire:submit.prevent="resetPassword" class="flex flex-col gap-4">
            <flux:input type="password" wire:model="password" placeholder="New Password" class="rounded"/>
            <flux:input type="password" wire:model="password_confirmation" placeholder="Confirm Password" class="rounded"/>

            <flux:button variant="primary" type="submit" class="w-full" >
                {{ __('Reset Password') }}
            </flux:button>
        </form>
    @endif

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        <span>{{ __('Or, return to') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('log in') }}</flux:link>
    </div>
</div>
