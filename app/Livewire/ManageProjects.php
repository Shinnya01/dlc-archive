<?php

namespace App\Livewire;

use Livewire\Component;
use Smalot\PdfParser\Parser;
use Masmerise\Toaster\Toaster;
use App\Models\ResearchProject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ManageProjects extends Component
{
    use WithFileUploads;


    public $title;
    public $year;

    public $projects;
    public $authorFile;     // PDF for authors
    public $projectFile; 

    public $extractedText;
    
    public $downloadLink;

    public function mount()
    {
       $this->fetchProject();
    }

    public function fetchProject()
    {
        $this->projects = ResearchProject::all();
    }

    public function createProject()
    {
        // 1. Validate inputs
        $this->validate([
            'title' => 'required|string|max:255',
            'year'  => 'required|integer|min:1900|max:'.date('Y'),
            'authorFile'  => 'required|mimes:pdf|max:2048',
            'projectFile' => 'required|mimes:pdf|max:10240',
        ]);

        // 2. Store PDF
        $authorPath  = $this->authorFile->store('projects/authorFile', 'public');
        $projectPath = $this->projectFile->store('projects', 'public');

        $authorFullPath  = storage_path('app/public/'.$authorPath);
        $projectFullPath = storage_path('app/public/'.$projectPath);

        // 3. Parse PDF text
        $parser = new Parser();
        $authorPdf = $parser->parseFile($authorFullPath);
        $authorText = $authorPdf->getText();
        

        // --- 4. Extract authors ---
        $authorsPrompt = <<<PROMPT
        Extract the authors and their details from the provided text.
        If the section is not found, return an empty string.
        Format the output as a valid JSON object:

        {
        "authors": [
            {
            "name": "",
            "program": "",
            "university": "",
            "address": "",
            "email": "",
            "contact": ""
            }
        ]
        }

        Text:
        {$authorText}
        PROMPT;

        $token = config('services.github_models.token');

        $authorsJson = json_encode([], JSON_UNESCAPED_UNICODE);

        $response = Http::withToken($token)
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'Content-Type' => 'application/json',
            ])
            ->post('https://models.github.ai/inference/chat/completions', [
                'model' => 'openai/gpt-4.1-nano', 
                'messages' => [
                    ['role' => 'user', 'content' => $authorsPrompt],
                ],
            ]);

        if ($response->successful()) {
            $content = $response->json()['choices'][0]['message']['content'] ?? '';
            $content = preg_replace('/^```json|```$/m', '', $content);
            $content = trim($content);

            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['authors'])) {
                $authorsJson = json_encode($decoded['authors'], JSON_UNESCAPED_UNICODE);
            }
        }

        // --- 5. Extract keywords ---
        $projectPdf = $parser->parseFile($projectFullPath);
        $pages = $projectPdf->getPages();
        $pdfNeed = '';

        for ($pageIndex = 9; $pageIndex < 11; $pageIndex++) {
            if (isset($pages[$pageIndex])) {
                $pdfNeed .= $pages[$pageIndex]->getText() . "\n";
            }
        }

        if (preg_match('/ABSTRACT(.*?)CHAPTER\s*I/is', $pdfNeed, $matches)) {
            $abstractText = trim($matches[1]);
        } else {
            $abstractText = $pdfNeed;
        }

        $abstractText = mb_substr($abstractText, 0, 4000, 'UTF-8');

        $keywordsPrompt = <<<PROMPT
        Extract keywords from the provided text.
        Keywords are unique words or phrases that represent the main topics of the paper.
        Format the output as a valid JSON object:

        {
        "keywords": []
        }

        Text:
        {$abstractText}
        PROMPT;

        $keywordsJson = json_encode([], JSON_UNESCAPED_UNICODE);

        $keywordsResponse = Http::withToken($token)
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'Content-Type' => 'application/json',
            ])
            ->post('https://models.github.ai/inference/chat/completions', [
                'model' => 'openai/gpt-4.1-nano', 
                'messages' => [
                    ['role' => 'user', 'content' => $keywordsPrompt],
                ],
            ]);

        if ($keywordsResponse->successful()) {
            $content = $keywordsResponse->json()['choices'][0]['message']['content'] ?? '';
            $content = preg_replace('/^```json|```$/m', '', $content);
            $content = trim($content);

            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['keywords'])) {
                $keywordsJson = json_encode($decoded['keywords'], JSON_UNESCAPED_UNICODE);
            }
        }

        // --- 6. Save to DB ---
        ResearchProject::create([
            'title'       => $this->title,
            'author'      => $authorsJson,   // AI output from author file
            'keywords'    => $keywordsJson,  // AI output from project file
            'year'        => $this->year,
            'author_file' => 'storage/'.$authorPath,
            'file'        => $projectPath,
        ]);


        // --- 7. Close modal and notify ---
        $this->modal('create-project')->close();
        $this->fetchProject();
        Toaster::success('Project Created!');
    }



    public function render()
    {
        return view('livewire.manage-projects');
    }
}
