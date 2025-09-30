<?php

use App\Models\User;
use App\Livewire\Inbox;
use Livewire\Volt\Volt;
use App\Livewire\Templates;
use App\Livewire\TestToast;
use App\Livewire\UserInbox;
use App\Livewire\ManageUsers;
use Laravel\Fortify\Features;
use App\Livewire\AdminAccounts;
use App\Models\ResearchProject;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\ManageProjects;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    

    if(auth()->check() && auth()->user()->status === 'verified') {

        if(auth()->user()->isUser()){
            return redirect()->route('templates');
        }else{
            return redirect()->route('dashboard');
        }

    }elseif(auth()->check() && auth()->user()->status === 'pending'){

            return redirect()->route('not-verified');

    }else{
        
        return redirect()->route('login');
    }
})->name('home');

Route::get('not-verified', function(){
    if(auth()->user()->status === 'pending'){
        return view('not-verified')
        ->layout('component.layouts.guest');
    }else{
        return redirect()->route('home');
    }
})->name('not-verified');

Route::get('dashboard', function () {
    $userCount = User::where('role', 'user')->count();
    $name = auth()->user()->name;
    $projectCount = ResearchProject::count();

    return view('dashboard', compact('userCount', 'projectCount' ,'name'));
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

Route::get('test-toast', TestToast::class)->name('test-toast');

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

Route::get('/test-pdf', function () {

    $acmData = [
        "title" => "LUBARRIOS: A Data Management System for Government Beneficiaries’ Programs in Lubao, Municipality",

        "abstract" => "
            This study introduces LUBARRIOS, a data management system designed to digitalize and manage resident profiles
            and beneficiaries of government programs in Lubao, Pampanga. The system aims to improve efficiency, accuracy,
            and security of records by replacing manual paper-based methods with digital solutions that enable quick data
            entry, retrieval, and management. It caters to municipal and barangay staff, streamlining processes involved
            in handling 4P’s and TUPAD beneficiaries, and promoting transparency and accountability in public service delivery.
        ",

        "introduction" => "
            In the Philippines, a barangay is the smallest local government unit. Many still rely on manual, paper-based record systems,
            which pose risks of data loss and inefficiency. Lists of 4P’s and TUPAD members are often submitted to the municipal hall,
            leading to inaccuracies and delays. LUBARRIOS is proposed as a digital system for managing resident and beneficiary data,
            enhancing accuracy, security, and workflow efficiency. Transitioning from manual to digital methods facilitates
            better data management and improved service delivery.
        ",

        "purposeOfDescription" => "
            This project aims to create a digital platform to efficiently and securely manage resident profiles and beneficiaries
            of government programs. By replacing manual record-keeping, the system allows faster, more accurate data handling,
            minimizes physical record loss, and reduces delays. The system is designed for municipal and barangay officials,
            as well as residents, to facilitate registration, updates, and document submissions, improving public service delivery.
            It also promotes transparency and accountability for programs like 4P’s and TUPAD.
        ",

        "methodology" => "
            The system was developed by digitalizing resident and beneficiary records, creating user interfaces for data entry
            and retrieval, and facilitating electronic document submission. Role-based access ensures security, while iterative
            development with user feedback improved usability, performance, and reliability. Testing verified compliance with quality standards.
        ",

        "methodologyDesign" => "
            LUBARRIOS features a user-friendly interface, role-based access controls, and a database connected to a secure web-based interface.
            Features include registration, profile management, document submission, and data viewing. The iterative development
            approach and ISO 25010 compliance ensure usability, reliability, security, and maintainability.
        ",

        "table" => [
            "title" => "ISO-25010 Evaluation Overall",
            "columns" => ['ISO 25010 Standard', 'Mean', 'Interpretation'],
            "rows" => [
                ['Functional Suitability', '3', 'Meets requirements'],
                ['Performance Efficiency', '3', 'Fast response times'],
                ['Compatibility', '3', 'Compatible with existing systems'],
                ['Usability', '3', 'User-friendly interface'],
                ['Reliability', '3', 'Reliable operation'],
                ['Security', '3', 'Secured data'],
                ['Portability', '3', 'Easily deployable'],
                ['Maintainability', '3', 'Easy to maintain'],
            ],
        ],

        "acknowledgement" => "
            The development of LUBARRIOS was supported by local government units, barangay officials, and the technical team,
            whose feedback was vital. Guidance from academic institutions and industry partners contributed to successful implementation,
            highlighting the importance of digital transformation in local governance.
        ",

        "references" => [
            'BALUMI. (2021). Data management in barangays.',
            'Don Honorio Ventura State University. (2024). LUBARRIOS: A Data Management System for Government Beneficiaries’ Programs.',
            'Philippine Statistics Authority. (2020). Barangay Profiles and Data.',
            'Department of the Interior and Local Government (DILG). (2022). Guidelines on Barangay Records Management.',
            'Department of Social Welfare and Development (DSWD). (2023). Implementation of Pantawid Pamilya Program.',
            'Department of Labor and Employment (DOLE). (2022). TUPAD Program Guidelines.',
            'Lubao Municipality Office Records. (2024). Beneficiary List Reports.',
            'ISO 25010 standard documentation',
        ],

        "keywords" => "data management, government beneficiaries, barangay records, digitalization, 4Ps, TUPAD, public service, Lubao, Pampanga",
    ];

    $authors = [
        [
            "name" => "Jane Doe",
            "program" => "Bachelor of Science in CS",
            "university" => "Pampanga State University",
            "address" => "123 Academic Lane, Lubao, Pampanga",
            "email" => "jane.doe@example.com",
            "contact" => "+63 912 345 6789",
        ],
        [
            "name" => "John Smith",
            "program" => "Master of Information Technology",
            "university" => "Pampanga State University",
            "address" => "456 Scholar Street, Lubao, Pampanga",
            "email" => "john.smith@example.com",
            "contact" => "+63 912 345 6789",
        ],
        [
            "name" => "Alice Reyes",
            "program" => "Bachelor of Engineering",
            "university" => "Pampanga State University",
            "address" => "789 Research Blvd, Lubao, Pampanga",
            "email" => "alice.reyes@example.com",
            "contact" => "+63 912 345 6789",
        ],
    ];


    // Load the Blade view
    // $pdf = Pdf::loadView('pdf.acm-template', compact('acmData', 'authors'));
    // $pdf = Pdf::loadView('pdf.acm', compact('acmData', 'authors'));

    // // Download PDF
    // // return $pdf->download('sample_acm.pdf');
    // return response()->streamDownload(
    //         fn() => print($pdf->output()),
    //         'two-column.pdf'
    //     );

        // Create new PDF

     $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $pdf->SetTitle($acmData['title'] ?? '');
            $pdf->SetMargins(15, 10, 15);
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            // --- TITLE & AUTHORS (centered, full width) ---
            $pdf->SetFont('times', 'B', 14);
            $pageWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];
            $pdf->MultiCell($pageWidth, 7, $acmData['title'] ?? '', 0, 'C', false, 1);

            $pdf->SetFont('times', '', 10);

            // Number of authors
            $numAuthors = !empty($authors) ? count($authors) : 0;
            if ($numAuthors > 0) {
                // Calculate width for each author block
                $authorWidth = $pageWidth / $numAuthors;

                // Save current Y position
                $yStart = $pdf->GetY();

                foreach ($authors as $index => $author) {
                    // Build author text safely
                    $authorText = ($author['name'] ?? '') . "\n";
                    $authorText .= ($author['program'] ?? '') . ', ' . ($author['university'] ?? '') . "\n";
                    // Example address & contact
                    $authorText .= ($author['address'] ?? "123 Academic Lane, Lubao,\nPampanga") . "\n";
                    $authorText .= ($author['phone'] ?? '+63 912 345 6789') . "\n";
                    $authorText .= ($author['email'] ?? '');

                    // X position: left margin + index * authorWidth
                    $xPos = $pdf->getMargins()['left'] + $index * $authorWidth;

                    $pdf->SetXY($xPos, $yStart);
                    $pdf->MultiCell($authorWidth, 5, $authorText, 0, 'C', false, 0); // 0 = continue on same line
                }
                $pdf->Ln(40);
            }

            // --- ENABLE 2 COLUMNS ---
            $gap = 5; // mm
            $columnWidth = ($pageWidth - $gap) / 2;
            $pdf->setEqualColumns(2, $columnWidth);
            $pdf->selectColumn(0);

            // --- ABSTRACT ---
            if (!empty($acmData['abstract'])) {
                $pdf->SetFont('times', 'B', 11);
                $pdf->Cell(0, 6, 'ABSTRACT', 0, 1);
                $pdf->SetFont('times', '', 10);
                $pdf->MultiCell(0, 5, $acmData['abstract']);
                $pdf->Ln(2);
            }

            // --- KEYWORDS ---
            if (!empty($acmData['keywords'])) {
                $pdf->SetFont('times', 'B', 11);
                $pdf->Cell(0, 6, 'KEYWORDS', 0, 1);
                $pdf->SetFont('times', '', 10);
                $pdf->MultiCell(0, 5, $acmData['keywords']);
                $pdf->Ln(5);
            }

            // --- SECTIONS ---
            $sections = [
                'INTRODUCTION' => $acmData['introduction'] ?? '',
                'PURPOSE OF DESCRIPTION' => $acmData['purposeOfDescription'] ?? '',
                'METHODOLOGY' => $acmData['methodology'] ?? '',
                'METHODOLOGY DESIGN' => $acmData['methodologyDesign'] ?? ''
            ];

            foreach ($sections as $title => $content) {
                if (trim($content) !== '') {
                    $pdf->SetFont('times', 'B', 11);
                    $pdf->Cell(0, 6, $title, 0, 1);
                    $pdf->SetFont('times', '', 10);
                    $pdf->MultiCell(0, 5, $content);
                    $pdf->Ln(2);
                }
            }

            // --- EVALUATION TABLE ---
           


            // --- ACKNOWLEDGEMENT ---
            if (!empty($acmData['acknowledgement'])) {
                $pdf->SetFont('times', 'B', 11);
                $pdf->Cell(0, 6, 'ACKNOWLEDGEMENT', 0, 1);
                $pdf->SetFont('times', '', 10);
                $pdf->MultiCell(0, 5, $acmData['acknowledgement']);
                $pdf->Ln(3);
            }

            // --- REFERENCES ---
            if (!empty($acmData['references'])) {

                // Step 1: Remove empty, URL, or duplicate entries
                $validReferences = array_filter($acmData['references'], function($ref) {
                    $ref = trim($ref);
                    return !empty($ref) && preg_match('/[A-Za-z0-9]/', $ref) && !preg_match('#^https?://#i', $ref);
                });
                $validReferences = array_unique($validReferences);

                // Step 2: Keep only entries that look like real references
                $validReferences = array_filter($validReferences, function($ref) {
                    // Keep if contains a 4-digit year (e.g., 2021) OR "Author, ..." pattern
                    return preg_match('/\d{4}/', $ref) || preg_match('/[A-Z][a-z]+,\s*[A-Z]?/', $ref);
                });

                // Step 3: Remove existing numbering (e.g., "1. ")
                $validReferences = array_map(function($ref) {
                    return preg_replace('/^\s*\d+\.\s*/', '', $ref);
                }, $validReferences);

                // Step 4: Re-number references consistently
                $numberedReferences = [];
                foreach ($validReferences as $i => $ref) {
                    $numberedReferences[] = ($i + 1) . ". " . $ref;
                }

                // Combine all references into one block
                $refsText = implode("\n", $numberedReferences);

                // PDF output
                $pdf->SetFont('times', 'B', 11);
                $pdf->Cell(0, 6, 'REFERENCES', 0, 1);
                $pdf->SetFont('times', '', 10);
                $pdf->MultiCell(0, 5, $refsText, 0, 'J');
            }


            $filename = 'ACM_' . Str::random(8) . '.pdf';
            $path = "public/{$filename}"; // will live in storage/app/public
            $fullPath = storage_path("app/{$path}");

            $pdf->Output($fullPath, 'F');
})->name('download-pdf');


require __DIR__.'/auth.php';
