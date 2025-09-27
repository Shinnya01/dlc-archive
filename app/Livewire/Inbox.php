<?php

namespace App\Livewire;

use App\Models\Request;
use Livewire\Component;
use Smalot\PdfParser\Parser;
use Masmerise\Toaster\Toaster;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Inbox extends Component
{
    public $requests;

    public function mount()
    {
        $this->fetchRequest();
    }



public function approveRequest($id)
{
    set_time_limit(300);
    $request = Request::find($id);

    if (!$request) {
        return response()->json(['error' => 'Request not found'], 404);
    }

    // 1️⃣ Approve the request
    $request->status = 'approved';
    $request->save();

    // 2️⃣ Parse authors
    $authors = json_decode($request->researchProject->author, true);
    $filePath = $request->researchProject->file;

    // 3️⃣ Parse the PDF
    $pdfFilePath = storage_path('app/public/' . $filePath);

    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFilePath);
    $pages = $pdf->getPages();

    $headerFooterPattern = '/(List of Tables|List of Figures|DEDICATION)/i';
    $combined_content = '';

    foreach ($pages as $page) {
        $pageText = $page->getText();
        $cleanPageText = preg_replace($headerFooterPattern, '', $pageText);
        $combined_content .= $cleanPageText;
    }

    // 4️⃣ Additional cleaning patterns
    $patterns = [
        '/\bAppendix\s:?.*?(?=ABSTRACT\s:?)/is',
        '/\bTable of Contents\s:?.*?(?=ABSTRACT\s:?)/is',
        '/\bTABLE OF CONTENT\s:?.*?(?=ABSTRACT\s:?)/is',
        '/\bABSTRACT\s:?.*?(?=ABSTRACT\s:?)/is',
        '/\bSlovin’s Formula;\s:?.*?(?=Chapter\sIV:?)/is',
        '/\bChapter\s:?IV.*?(?=System Evaluation Results Based on ISO-25010\s:?)/is',
        '/\bChapter II\s:?.*?(?=Chapter\sIII:?)/is',
        '/\bChapter\sV:?.*?(?=REFERENCES\s:?)/is',
        '/\bScope of Objectives\sV:?.*?(?=Chapter\sIII:?)/is',
        '/\bAPPENDIX A\s:?.*?(?=APPENDIX P\s:?)/is',
    ];

    foreach ($patterns as $pattern) {
        $combined_content = preg_replace($pattern, '', $combined_content);
    }

    $combined_content = str_replace(["\r", "\n"], '', $combined_content);

    $combined_content = mb_convert_encoding($combined_content, 'UTF-8', 'UTF-8');
    

    // 🔹 Chunk the content
    $chunkSize = 8000;
    $chunks = [];
    $length = mb_strlen($combined_content, 'UTF-8');

    for ($i = 0; $i < $length; $i += $chunkSize) {
        $chunks[] = mb_substr($combined_content, $i, $chunkSize, 'UTF-8');
    }

    // 🔹 Prepare merged structure
    $merged = [
        'title' => '',
        'year' => '',
        'abstract' => '',
        'keywords' => '',
        'introduction' => '',
        'methodology' => '',
        'purposeOfDescription' => '',
        'methodologyDesign' => '',
        'acknowledgement' => '',
        'references' => [],
        'table' => [
            'title' => 'ISO-25010 Evaluation Overall',
            'columns' => [],
            'rows' => [],
        ],
    ];

    $token = config('services.github_models.token');

    foreach ($chunks as $index => $chunk) {
        try {
            sleep(2); // optional, to avoid rate limits

$prompt = <<<PROMPT
You are an ACM-format metadata extraction assistant. Extract metadata from the given PDF text and return strictly valid JSON.

Instructions:
1. Do not read or include formulas, equations, source code, or any other non-text content.
2. Authors must only be the authors of the paper (listed at the start, under the title). Do not include any authors from the References section.
3. Extract the following fields exactly as specified. Missing fields must be empty strings or empty arrays.
   - title: The title of the paper.
   - year: The year of publication.
   - abstract: summarize the abstract.
   - keywords: Extract exactly as listed under "Keywords: …", return as a comma-separated list.
   - introduction: Up to two paragraphs summarizing background, context, objectives, importance, scope, and contribution.
   - methodology: Up to two paragraphs describing the approach, rationale, and main steps.
   - purposeOfDescription: Up to three paragraphs summarizing goals, objectives, intended outcomes, and problems addressed.
   - methodologyDesign: Up to three paragraphs explaining the system development methodology, each phase clearly and concisely.
   - acknowledgement: Extract the acknowledgement section summarize in two paragraph.
   - references: Copy up to 20 cited references in the text, preserving format and order.
   - table: Extract only the "ISO-25010 Evaluation Overall" table. Include as JSON with "title", "columns", and "rows" (keys as column headers). Ensure all rows and columns are included.
     note: do not include sub table, only the main table or the table that has overall in title
Do not include any extra information, commentary, or explanation. Return strictly valid JSON in the following format:
{
  "title": "",
  "year": "",
  "abstract": "",
  "keywords": "",
  "introduction": "",
  "methodology": "",
  "purposeOfDescription": "",
  "methodologyDesign": "",
  "acknowledgement": "",
  "references": [],
  "table": {
    "title": "ISO-25010 Evaluation Overall",
    "columns": [],
    "rows": []
  }
}

Always return "table" in this JSON structure:

{
  "title": "ISO-25010 Evaluation Overall",
  "columns": ["ISO 25010 Standard", "Mean", "Interpretation"],
  "rows": [
    { "ISO 25010 Standard": "<criterion>", "Mean": "<numeric score>", "Interpretation": "<text>" }
  ]
}

The response must be only the JSON object, with no introductory text or explanations.
Provide the output as a valid JSON object.
avoid this kind of error "Initial JSON parse failed: SyntaxError: Expected ',' or '}' after property value in JSON"

**Here is the text to extract from:**

$chunk
PROMPT;


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

            if ($response->successful()) {
                $data = $response->json();

                // Assuming the AI response is in 'choices[0].message.content'
                if (isset($data['choices'][0]['message']['content'])) {
                    $aiContent = $data['choices'][0]['message']['content'];

                    // Merge into your structure (same logic as before)
                    // For example, append everything into 'abstract' or a temp variable
                    $merged['abstract'] = trim($merged['abstract'] . ' ' . $aiContent);
                }
            }
        } catch (\Exception $e) {
            \Log::error('AI Chunk processing error: ' . $e->getMessage());
        }
    }

        dd([
        'authors' => $authors,
        'merged' => $merged,
    ]);

}


    public function rejectRequest($id)
    {
        $request = Request::find($id);
        if ($request) {
            $request->delete();
            $this->fetchRequest();
            Toaster::success('Request Rejected');
        }
    }

    public function fetchRequest()
    {
        $this->requests = Request::all();
    }

    public function render()
    {
        return view('livewire.inbox');
    }
}
