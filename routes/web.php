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
    "title" => "E - VAL: An Employee Performance Evaluation System for DHVSU Lubao Campus with Sentiment Analysis",
    
    "abstract" => "
        This paper introduces E-Val, a web-based platform that aims to improve the faculty performance evaluation process by incorporating student feedback and sentiment analysis. 
        It addresses limitations of traditional systems, such as lack of transparency and accessibility, by providing a centralized, user-friendly solution that analyzes feedback for insights into teaching quality and faculty development.
        E-Val provides transparency and supports data-driven decision-making for faculty development, enhancing the quality of education and promoting accountability in the evaluation process.
    ",
    
    "introduction" => "
        Instructors play a crucial role in shaping students' academic experiences and improving the standard of instruction. Assessing professor performance is essential for enhancing educational effectiveness and elevating academic standards. 
        Modern performance evaluation systems focus on accountability, transparency, and continuous improvements. In Philippine educational institutions, implementing effective evaluation systems is challenging due to limited access to detailed information and transparency issues. 
        E-Val aims to address these problems by providing an intuitive platform for faculty and student evaluations, utilizing sentiment analysis and quantitative measures to enhance accountability and transparency. 
        The system facilitates informed decision-making by students and fosters faculty development through actionable feedback. Overall, it seeks to improve teaching methods and student learning outcomes, contributing to higher educational standards.
    ",
    
    "purposeOfDescription" => "
        The purpose of this study is to assess the effectiveness of E-Val in enhancing faculty evaluation processes through sentiment analysis and data-driven feedback. 
        It aims to improve the transparency, accuracy, and usefulness of performance assessments, ultimately contributing to better teaching practices and organizational accountability. 
        The study also seeks to understand stakeholders’ perceptions, identify areas for system improvement, and promote continuous faculty development. 
        By addressing existing evaluation limitations, E-Val aspires to foster a culture of openness, accountability, and constant growth within higher education institutions.
    ",
    
    "methodology" => "
        The evaluation process of instructor performance at DHVSU Lubao Campus utilizes the web-based E-Val system, which enables a comprehensive ecosystem for educational feedback from students and faculty. 
        This digital platform collects, manages, and analyzes performance data to provide insights into teaching effectiveness. 
        The methodology involves implementing a full-stack application that supports feedback submission, sentiment analysis, and data visualization, aiming to streamline performance assessment and foster continuous improvement.
        The research incorporated user training, testing, and iterative evaluation to ensure proper adoption and reliable system operation.
    ",
    
    "methodologyDesign" => "
        The methodology employs a systematic evaluation framework with mixed methods, combining quantitative data analysis of feedback ratings and sentiment scores with qualitative insights from surveys and interviews. 
        Data collection involves stratified random sampling to ensure diverse departmental representation. 
        Sentiment analysis algorithms process student feedback to derive insights into faculty performance, while survey instruments gauge user satisfaction and system usability. 
        The analysis aims to measure the system's impact on teaching quality, fairness, and stakeholder perceptions, guiding iterative improvements.
        The system design prioritizes usability, scalability, and security, incorporating role-based access and modular architecture for future enhancements.
    ",
    
    "table" => [
        "title" => "ISO-25010 Evaluation Overall",
        "columns" => ['ISO 25010 Standard', 'Mean', 'Interpretation'],
        "rows" => [
            ['Functional Suitability', 'Excellent', 'Excellent'],
            ['Performance Efficiency', 'Good', 'Good'],
            ['Compatibility', 'Good', 'Good'],
            ['Usability', 'Excellent', 'Excellent'],
            ['Reliability', 'Good', 'Good'],
            ['Security', 'Good', 'Good'],
            ['Maintainability', 'Very Good', 'Very Good'],
            ['Portability', 'Good', 'Good'],
        ],
    ],
    
    "acknowledgement" => "
        The authors express gratitude to the faculty and students of DHVSU Lubao Campus for their participation and feedback, which were vital to the research. 
        Special thanks are extended to the institutional support that facilitated the development and deployment of the E-Val system, enabling this study to contribute to faculty evaluation practices.
    ",
    
    "references" => [
        'El-Ashkar AM, Miskeen EHI, Alghamdi M et al., 2021 IEEE Standard for Software and System Testability, IEEE 610.1-2020',
        'Nielsen, J. (2011). Reliability of Website Usability Evaluation.',
        'M. Biehl, "Sentiment analysis in educational feedback," Journal of Educational Data Mining, 2019.',
        'ISO-25010 Evaluation Model, International Organization for Standardization.',
        'Shneiderman, B. (2003). "Designing the User Interface: Strategies for Effective Human-Computer Interaction."',
        'W3C. (2018). Web Content Accessibility Guidelines (WCAG) 2.1.',
        'He, X., & Tong, Y. (2020). "Automated Feedback Analysis for Educational Performance."',
        'Creswell, J. W. (2014). Research Design: Qualitative, Quantitative, and Mixed Methods Approaches',
        'DIN EN ISO 9241-11:2018. Ergonomics of human-system interaction.',
        'Kumar, V., & Sharma, R. (2021). "Enhancing Education Quality Through Technology-Driven Evaluation Systems."',
    ],
    
    "year" => "",
    
    "keywords" => "DHVSU, web-based system, sentiment analysis, performance evaluation, teaching quality, educational standards, faculty evaluation",
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
        "contact" => "+63 998 765 4321",
    ],
    [
        "name" => "Alice Reyes",
        "program" => "Bachelor of Engineering",
        "university" => "Pampanga State University",
        "address" => "789 Research Blvd, Lubao, Pampanga",
        "email" => "alice.reyes@example.com",
        "contact" => "+63 917 555 1234",
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
        if (!empty($acmData['table']['columns']) && !empty($acmData['table']['rows'])) {
            $pdf->Ln(2);
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, 'EVALUATION RESULTS', 0, 1);
            $pdf->SetFont('times', '', 10);

            $tbl = '<table border="1" cellpadding="3" cellspacing="0">';
            $tbl .= '<tr style="background-color:#eeeeee;">';
            foreach ($acmData['table']['columns'] as $col) {
                $tbl .= '<th><b>'.htmlspecialchars($col).'</b></th>';
            }
            $tbl .= '</tr>';

            foreach ($acmData['table']['rows'] as $row) {
                $tbl .= '<tr>';
                foreach ($row as $cell) {
                    $tbl .= '<td>'.htmlspecialchars($cell).'</td>';
                }
                $tbl .= '</tr>';
            }
            $tbl .= '</table>';

            $pdf->writeHTML($tbl, true, false, false, false, '');
            $pdf->Ln(3);
        }

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
            // Filter out invalid references (empty, just URLs, or too short)
            $validReferences = array_filter($acmData['references'], function($ref) {
                $ref = trim($ref);
                // Keep if it has some letters/numbers and is not just a URL
                return !empty($ref) && preg_match('/[A-Za-z0-9]/', $ref) && !preg_match('#^https?://#i', $ref);
            });

            // Remove duplicates
            $validReferences = array_unique($validReferences);

            // Number the references
            $numberedReferences = [];
            foreach ($validReferences as $i => $ref) {
                $numberedReferences[] = ($i + 1) . ". " . $ref;
            }

            // Combine all references into one block, separated by newlines
            $refsText = implode("\n", $numberedReferences);

            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, 'REFERENCES', 0, 1);
            $pdf->SetFont('times', '', 10);
            $pdf->MultiCell(0, 5, $refsText, 0, 'J');
        }

    // --- OUTPUT PDF ---
    $pdf->Output('acm-paper-ready.pdf','D');
})->name('download-pdf');


require __DIR__.'/auth.php';
