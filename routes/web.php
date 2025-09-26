<?php

use Livewire\Volt\Volt;
use App\Livewire\Templates;
use Laravel\Fortify\Features;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if(auth()->check()) {
        return redirect()->route('dashboard');
    }else{
        return redirect()->route('login');
    }
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
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

    Route::get('templates', Templates::class)->name('templates');
        
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
                ['role' => 'user', 'content' => 'Hello, can you generate a short greeting?'],
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
