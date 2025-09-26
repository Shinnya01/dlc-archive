<?php

use App\Models\User;
use App\Livewire\Inbox;
use Livewire\Volt\Volt;
use App\Livewire\Templates;
use App\Livewire\UserInbox;
use App\Livewire\ManageUsers;
use Laravel\Fortify\Features;
use App\Livewire\AdminAccounts;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\ManageProjects;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if(auth()->check()) {

        if(auth()->user()->isUser()){
            return redirect()->route('templates');
        }else{
            return redirect()->route('dashboard');
        }

    }else{
        return redirect()->route('login');
    }
})->name('home');

Route::get('dashboard', function () {
    $userCount = User::where('role', 'user')->count();
    $name = auth()->user()->name;
    // $projectCount = Project::count();

    return view('dashboard', compact('userCount', 'name'));
})
->middleware(['auth', 'verified', 'role:admin'])
->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::middleware(['auth','role:admin'])->group(function () {
        Route::get('manage-users', ManageUsers::class)->name('manage-users');
        Route::get('manage-projects', ManageProjects::class)->name('manage-projects');
        Route::get('admin-accounts', AdminAccounts::class)->name('admin-accounts');
        Route::get('inbox', Inbox::class)->name('inbox');
    });

    Route::middleware(['auth','role:user'])->group(function () {
        Route::get('templates', Templates::class)->name('templates');
        Route::get('user-inbox', UserInbox::class)->name('user-inbox');
    });
});


Route::get('/test-github-models', function () {
    $token = config('services.github_models.token');

    $response = Http::withToken($token)
        ->withHeaders([
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'Content-Type' => 'application/json',
        ])
        ->post('https://models.github.ai/inference/chat/completions', [
            'model' => 'openai/gpt-4.1-nano', 
            'messages' => [
                ['role' => 'user', 'content' => 'Hello, do you know jhonmar?'],
            ],
        ]);

    if ($response->successful()) {
        $content = $response->json()['choices'][0]['message']['content'] ?? 'No response';

        $pdf = Pdf::loadView('pdf.ai-response', ['content' => $content]);
        return $pdf->download('ai-response.pdf');
    } else {
        return response($response->body(), $response->status());
    }
});

require __DIR__.'/auth.php';
