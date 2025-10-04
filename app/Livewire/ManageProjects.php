<?php

namespace App\Livewire;

use Livewire\Component;
use Smalot\PdfParser\Parser;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;
use App\Models\ResearchProject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Title('Manage Projects')]
class ManageProjects extends Component
{
    use WithFileUploads;

    public $search = '';

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

    public function updatedSearch()
    {
        // when search changes, refetch projects
        $this->fetchProject();
    }

    public function fetchProject()
    {
        $query = ResearchProject::query();

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereJsonContains('keywords', $this->search)
                  ->orWhereJsonContains('author', $this->search);
        }

        $this->projects = $query->get();
    }

    public function createProject()
    {
        // 1. Validate inputs
        $this->validate([
            'title'       => 'required|string|max:255',
            'year'        => 'required|integer|min:1900|max:'.date('Y'),
            'authorFile'  => 'required|mimes:pdf|max:30000',
            'projectFile' => 'required|mimes:pdf|max:10240',
        ]);

        // 2. Store PDFs
        // $authorPath  = $this->authorFile->store('projects/authorFile', 'public');
        // $projectPath = $this->projectFile->store('projects', 'public');

        $authorPath  = $this->authorFile->store('projects/authorFile', 'spaces');
        $projectPath = $this->projectFile->store('projects', 'spaces');


        // $authorFullPath  = storage_path('app/public/'.$authorPath);
        // $projectFullPath = storage_path('app/public/'.$projectPath);

        $authorUrl  = Storage::disk('spaces')->url($authorPath);
        $projectUrl = Storage::disk('spaces')->url($projectPath);


        // 3. Generate authors & keywords using helper methods
        $authorsJson  = $this->generateAuthor($authorUrl);
        $keywordsJson = $this->generateKeyword($projectUrl);

        if ($authorsJson === 'used_all_token' || $keywordsJson === 'used_all_token') {
            Toaster::error('AI token limit reached. Please try again later.');
            $this->modal('create-project')->close();
            $this->reset(['title','year','authorFile','projectFile']);
            return; 
        }
        if ($authorsJson === 'ai_request_failed' || $keywordsJson === 'ai_request_failed') {
            Toaster::error('AI request failed. Please try again later.');
            $this->modal('create-project')->close();
            $this->reset(['title','year','authorFile','projectFile']);
            return; 
        }
        

        // 4. Save to DB
        ResearchProject::create([
            'title'       => $this->title,
            'author'      => $authorsJson,
            'keywords'    => $keywordsJson,
            'year'        => $this->year,
            'author_file' => 'storage/'.$authorPath,
            'file'        => $projectPath,
        ]);

        // dd($keywordsJson);

        // 5. Close modal and notify
        $this->modal('create-project')->close();
        $this->fetchProject();
        Toaster::success('Project Created!');
    }

    public function generateAuthor($filePath)
    {
        try{
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $authorText = $pdf->getText();

            $prompt = <<<PROMPT
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
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);
            if ($response->failed()) {
                // Token or rate limit reached
                if ($response->status() == 429 || str_contains($response->body(), 'RateLimitReached')) {
                    // Toaster::error('AI token limit reached. Please try again later.');
                    return 'used_all_token';
                } else {
                    // Toaster::error('AI request failed. Check logs for details.');
                    return 'ai_request_failed';
                }
                return $authorsJson;
            }

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                $content = preg_replace('/^```json|```$/m', '', $content);
                $content = trim($content);

                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['authors'])) {
                    $authorsJson = json_encode($decoded['authors'], JSON_UNESCAPED_UNICODE);
                }
            }

            return $authorsJson;

        } catch (\Exception $e) {
            \Log::error('Author extraction failed: '.$e->getMessage());
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }
    }

    public function generateKeyword($filePath)
    {
        try{
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $pages = $pdf->getPages();
            $pdfText = '';

            // Extract pages 10–11
            for ($i = 9; $i < 11; $i++) {
                if (isset($pages[$i])) {
                    $pdfText .= $pages[$i]->getText() . "\n";
                }
            }

            // Extract only ABSTRACT → CHAPTER I
            if (preg_match('/ABSTRACT(.*?)CHAPTER\s*I/is', $pdfText, $matches)) {
                $abstractText = trim($matches[1]);
            } else {
                $abstractText = $pdfText;
            }

            $abstractText = mb_substr($abstractText, 0, 4000, 'UTF-8');

            $prompt = <<<PROMPT
            Extract keywords from the provided text.
            Keywords are unique words or phrases that represent the main topics of the paper.
            Format the output as a valid JSON object:

            {
            "keywords": []
            }

            Text:
            {$abstractText}
            PROMPT;

            $token = config('services.github_models.token');
            $keywordsJson = json_encode([], JSON_UNESCAPED_UNICODE);

            $response = Http::withToken($token)
                ->withHeaders([
                    'Accept' => 'application/vnd.github+json',
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Content-Type' => 'application/json',
                ])
                ->post('https://models.github.ai/inference/chat/completions', [
                    'model' => 'openai/gpt-4.1-nano', 
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

             if ($response->failed()) {
                // Token or rate limit reached
                if ($response->status() == 429 || str_contains($response->body(), 'RateLimitReached')) {
                    // Toaster::error('AI token limit reached. Please try again later.');
                    return 'used_all_token';
                } else {
                    // Toaster::error('AI request failed. Check logs for details.');
                    return 'ai_request_failed';
                }

                return $keywordsJson;
            }

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                $content = preg_replace('/^```json|```$/m', '', $content);
                $content = trim($content);

                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['keywords'])) {
                    $keywordsJson = json_encode($decoded['keywords'], JSON_UNESCAPED_UNICODE);
                }
            }
            
            return $keywordsJson;

        } catch (\Exception $e) {
            \Log::error('Author extraction failed: '.$e->getMessage());
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }
    }





    public function render()
    {
        return view('livewire.manage-projects');
    }
}
