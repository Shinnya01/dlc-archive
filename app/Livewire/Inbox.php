<?php

namespace App\Livewire;

use App\Models\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

#[Title('Inbox')]
class Inbox extends Component
{
    public $requests;
    private int $chunkSize = 8000;

    public function mount()
    {
        $this->fetchRequest();
    }



    public function approveRequest($id)
    {
        Log::info("Fetching request ID: $id");
        set_time_limit(3000);

        $request = Request::find($id);
        if (!$request) {
            Log::warning("Request ID $id not found");
            return response()->json(['error' => 'Request not found'], 404);
        }

        // ✅ Extract metadata
        $authors = json_decode($request->researchProject->author, true);

        // Download the PDF from Spaces to a temporary local file
        $remoteUrl = Storage::disk('spaces')->url($request->researchProject->file);
        $tempPath = tempnam(sys_get_temp_dir(), 'pdf_');
        file_put_contents($tempPath, file_get_contents($remoteUrl));

        // Parse the PDF
        $combinedContent = $this->parsePdf($tempPath);

        // Delete the temp file after parsing
        @unlink($tempPath);

        $chunks = $this->splitText($combinedContent);

        $client = $this->githubClient();
        $acmDataTemp = $this->processPlainChunks($client, $chunks);

        $acmDataTemp = $this->cleanText($acmDataTemp);
        $acmData = $this->processChunk($client, $acmDataTemp, $this->chunkSize);

        
        if ($acmData) {
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetTitle($acmData['title'] ?? '');
            $pdf->SetMargins(15, 10, 15);
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            // --- TITLE ---
            $pdf->SetFont('times', 'B', 14);
            $pageWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];
            $pdf->MultiCell($pageWidth, 7, $acmData['title'] ?? '', 0, 'C', false, 1);

            // --- AUTHORS ---
            $pdf->SetFont('times', '', 10);
            $numAuthors = count($authors ?? []);
            if ($numAuthors > 0) {
                $authorWidth = $pageWidth / $numAuthors;
                $yStart = $pdf->GetY();

                foreach ($authors as $i => $author) {
                    $authorText = ($author['name'] ?? '') . "\n";
                    $authorText .= ($author['program'] ?? '') . ', ' . ($author['university'] ?? '') . "\n";
                    $authorText .= ($author['address'] ?? "123 Academic Lane, Lubao,\nPampanga") . "\n";
                    $authorText .= ($author['phone'] ?? '+63 912 345 6789') . "\n";
                    $authorText .= ($author['email'] ?? '');
                    $pdf->SetXY($pdf->getMargins()['left'] + $i * $authorWidth, $yStart);
                    $pdf->MultiCell($authorWidth, 5, $authorText, 0, 'C', false, 0);
                }
                $pdf->Ln(40);
            }

            // --- 2-COLUMNS for main content ---
            $gap = 5;
            $columnWidth = ($pageWidth - $gap) / 2;
            $pdf->setEqualColumns(2, $columnWidth);
            $pdf->selectColumn(0);

            // --- ABSTRACT & KEYWORDS ---
            foreach (['abstract' => 'ABSTRACT', 'keywords' => 'KEYWORDS'] as $field => $label) {
                if (!empty($acmData[$field])) {
                    $pdf->SetFont('times', 'B', 11);
                    $pdf->Cell(0, 6, $label, 0, 1);
                    $pdf->SetFont('times', '', 10);
                    $pdf->MultiCell(0, 5, $acmData[$field]);
                    $pdf->Ln(2);
                }
            }

            // --- MAIN SECTIONS ---
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

            // --- EVALUATION RESULTS (ISO-25010) ---
            if (!empty($acmData['table']['columns']) && !empty($acmData['table']['rows'])) {
                $pdf->Ln(3);
                $pdf->SetFont('times', 'B', 11);
                $pdf->Cell(0, 6, 'EVALUATION RESULTS (ISO-25010)', 0, 1);
                $pdf->SetFont('times', '', 10);

                // Start HTML table
                $tbl = '<table border="1" cellpadding="3" cellspacing="0">';
                $tbl .= '<tr style="background-color:#eeeeee;">';
                foreach ($acmData['table']['columns'] as $col) {
                    $tbl .= '<th><b>' . htmlspecialchars($col) . '</b></th>';
                }
                $tbl .= '</tr>';

                // TCPDF can auto-split tables if you use writeHTML with true for $ln and $reseth
                foreach ($acmData['table']['rows'] as $row) {
                    $tbl .= '<tr>';
                    foreach ($acmData['table']['columns'] as $colKey) {
                        $tbl .= '<td>' . htmlspecialchars($row[$colKey] ?? '') . '</td>';
                    }
                    $tbl .= '</tr>';
                }
                $tbl .= '</table>';

                // Use MultiCell height to estimate if table will fit, add page if needed
                $estimatedHeight = count($acmData['table']['rows']) * 6 + 20; // 6mm per row approx
                if ($pdf->GetY() + $estimatedHeight > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
                    $pdf->AddPage();
                    $pdf->selectColumn(0); // reset to first column
                }

                // Write the table
                $pdf->writeHTML($tbl, true, false, false, false, '');
                $pdf->Ln(5);
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
           // --- REFERENCES in ACM style ---
            if (!empty($acmData['references'])) {
                // Keep only bibliographic entries, remove evaluation/data rows
                $refs = array_filter($acmData['references'], function($r) {
                    return !preg_match('/(Functionality|Usability|Reliability|Security|Portability|Maintainability|ISO-25010)/i', $r);
                });

                $refs = array_values($refs); // reindex
                $refsText = '';
                foreach ($refs as $i => $ref) {
                    $ref = trim($ref);
                    if ($ref !== '') {
                        // Add ACM style numbering [1], [2], ...
                        $refsText .= '[' . ($i + 1) . '] ' . $ref . "\n";
                    }
                }

                if ($refsText !== '') {
                    $pdf->SetFont('times', 'B', 11);
                    $pdf->Cell(0, 6, 'REFERENCES', 0, 1);
                    $pdf->SetFont('times', '', 10);
                    $pdf->MultiCell(0, 5, $refsText, 0, 'J');
                }
            }


            $filename = 'ACM_' . Str::random(8) . '.pdf';
            $path = "public/{$filename}";
            $fullPath = storage_path("app/{$path}");
            $pdf->Output($fullPath, 'F');

            $request->pdf_path = $path;
            $request->status = 'approved';
            $request->save();

            $this->fetchRequest();
            Toaster::success('Request approved and ACM PDF generated successfully!');
        }


        $request->pdf_path = $path;
        $request->status = 'approved';
        $request->save();

        $this->fetchRequest();
        Toaster::success('Request Approve and ACM Generated Successfully!');
    
        // Example of generating PDF:
        // $pdf = Pdf::loadView('pdf.acm-template', compact('acmData', 'authors'));
        // return $pdf->download('sample_acm.pdf');
    }
   /** ----------------- Helpers ----------------- */

    private function parsePdf(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);

        $headerFooterPattern = '/(List of Tables|List of Figures|DEDICATION)/i';
        $content = '';

        foreach ($pdf->getPages() as $page) {
            $pageText = $page->getText();
            $content .= preg_replace($headerFooterPattern, '', $pageText);
        }

        // Additional cleaning
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
            $content = preg_replace($pattern, '', $content);
        }

        return mb_convert_encoding(str_replace(["\r", "\n"], '', $content), 'UTF-8', 'UTF-8');
    }

    private function splitText(string $text): array
    {
        $chunks = [];
        $len = mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $len; $i += $this->chunkSize) {
            $chunks[] = mb_substr($text, $i, $this->chunkSize, 'UTF-8');
        }

        return $chunks;
    }

    private function githubClient()
    {
        $token = config('services.github_models.token');
        return Http::withToken($token)
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'Content-Type' => 'application/json',
            ]);
    }

    private function processPlainChunks($client, array $chunks): string
    {
        $acmDataTemp = '';

        foreach ($chunks as $index => $chunk) {
            Log::info("Sending chunk $index to API, chunk length: " . mb_strlen($chunk, 'UTF-8'));
            sleep(8);

            $prompt = $this->plainPrompt($chunk);

            $response = $this->sendRequest($client, $prompt, $index);
            if (!$response) {
                return ''; // early exit on failure
            }

            $content = $this->stripJsonFences($response->json()['choices'][0]['message']['content'] ?? '');
            $acmDataTemp .= $content;
            sleep(10);
        }

        return $acmDataTemp;
    }

    private function sendRequest($client, string $prompt, int $index)
    {
        $attempts = 0;
        $maxAttempts = 3;
        $wait = 10;

        while ($attempts < $maxAttempts) {
            $attempts++;
            try {
                $response = $client
                    ->timeout(300)
                    ->post('https://models.github.ai/inference/chat/completions', [
                        'model' => 'openai/gpt-4.1-nano',
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                        'temperature' => 1.0,
                        'top_p' => 1.0,
                    ]);

                if ($response->successful()) {
                    return $response;
                }

                $status = $response->status();
                $body = $response->body();

                if ($status === 429) {
                    // rate limit
                    $waitTime = 60;
                    if (preg_match('/wait (\d+) seconds/i', $body, $m)) {
                        $waitTime = (int)$m[1];
                    }
                    \Log::warning("Rate limit hit on chunk {$index}. Waiting {$waitTime}s before retry.");
                    sleep($waitTime);
                    continue;
                }

                \Log::error("API request failed for chunk {$index}: Status {$status}, Body: {$body}");
                return null;
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                \Log::error("API connection error for chunk {$index} attempt {$attempts}: " . $e->getMessage());
                sleep($wait);
                $wait *= 2; // exponential backoff
            }
        }

        Toaster::error("Failed to send chunk {$index} after {$maxAttempts} attempts.");
        return null;
    }


    private function stripJsonFences(string $content): string
    {
        return trim(preg_replace('/```json|```/i', '', $content));
    }

    private function cleanText(string $text): string
    {
        $text = str_replace(["\r", "\n", '/', '|', '------------------------'], '', $text);
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    private function plainPrompt(string $chunk): string
    {
        return <<<PROMPT
        Instructions (Shortened):
        Extract only readable text. Ignore formulas, equations, code, or non-text.
        Return fields exactly as listed. Missing = empty.
        Skip URLs and appendices.
        Return plain text only (no JSON).
        Keep concise, only best/relevant content.

        Fields:
        title
        abstract
        keywords (comma-separated)
        introduction (≤2 paragraphs)
        methodology (≤2 paragraphs)
        purposeOfDescription (≤3 paragraphs)
        methodologyDesign (≤3 paragraphs)
        references (≤20, preserve format/order)
        table (only "ISO-25010 Evaluation Overall" table, full rows/columns, keep original headers/values)

        $chunk
        PROMPT;
    }


    private function processChunk($client, $text,  $chunkSize)
    {
        $text = str_replace(['/', '|', "\r", "\n"], '', $text);

                $length = mb_strlen($text, 'UTF-8');
                $chunks = [];

                // Split into chunks
                for ($i = 0; $i < $length; $i += $chunkSize) {
                    $chunks[] = mb_substr($text, $i, $chunkSize, 'UTF-8');
                }

                $merged = [
                    'title' => '',
                    'abstract' => '',
                    'introduction' => '',
                    'purposeOfDescription' => '',
                    'methodology' => '',
                    'methodologyDesign' => '',
                    'acknowledgement' => '',
                    'references' => [],
                    'table' => [],
                    'year' => '',
                    'keywords' => ''
                ];
                
                sleep(25);
                foreach ($chunks as $index => $chunk) {

                    // dd($chunk);
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
                Include the authors' names (as listed at the start of the paper) when describing the work, e.g., "In this work, <Author Names> investigate..."
                - methodology: Up to two paragraphs describing the approach, rationale, and main steps.
                - purposeOfDescription: Up to three paragraphs summarizing goals, objectives, intended outcomes, and problems addressed.
                - methodologyDesign: Up to three paragraphs explaining the system development methodology, each phase clearly and concisely.
                - acknowledgement: Extract the acknowledgement section summarize in two paragraph.
                - references: Copy up to 20 cited references in the text, preserving format and order.
                - table: Extract only the "ISO-25010 Evaluation Overall" table. Include as JSON with "title", "columns", and "rows" (keys as column headers). Ensure all rows and columns are included.
                    note: do not include sub table, only the main table or the table that has overall in title
                Do not include any extra information, commentary, or explanation. Return strictly valid JSON in the following format:
                Do not include non-readable content like equations, source code, or any other non-text content.
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
                avoid this kind of error "Initial JSON parse failed: SyntaxError: Expected ',' or '}' after property value in JSON "

                **Here is the text to extract from:**  

                $chunk
                PROMPT;
                try {
                    $response = $client->timeout(300)
                        ->post('https://models.github.ai/inference/chat/completions', [
                        'model' => 'openai/gpt-4.1-nano', 
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 1.0,
                        'top_p' => 1.0,
                    ]);

                    if ($response->failed()) {
                        $status = $response->status();
                        $body = $response->body();

                        // Log the error
                        \Log::error("API request failed for chunk {$index}: Status {$status}, Body: {$body}");

                        // Detect rate limit specifically
                        if ($status == 429 && str_contains($body, 'RateLimitReached')) {
                            Toaster::error('Rate limit reached. You can try again tomorrow.');
                        } else {
                            Toaster::error('API request failed. Check logs for details.');
                        }

                        // Stop further execution
                        return;
                    }

                    } catch (\Illuminate\Http\Client\ConnectionException $e) {
                        \Log::error("API connection error for chunk {$index}: " . $e->getMessage());
                        Toaster::error('Connection timeout. Please try again later.');
                        return;
                    }

                    $content = $response->json()['choices'][0]['message']['content'] ?? '';
                    $content = preg_replace('/```json|```/i', '', $content);
                    $content = trim($content);
                    Log::info("Received API response for chunk $index, length: " . strlen($content));
                    try {
                        $Data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $e) {
                        error_log('JSON parse error: ' . $e->getMessage());
                        continue; // Skip invalid response
                    }

                    if (!$Data || !is_array($Data)) {
                    continue; // Skip invalid response
                    }

                    foreach ($Data as $key => $value) {
                        if ($key === 'references' && is_array($value)) {
                            $merged['references'] = array_merge($merged['references'], $value);
                        } else if ($key === 'table' && is_array($value)) {
                            if (empty($merged['table'])) {
                                $merged['table'] = $value;
                            }
                        } else {
                            if (empty($merged[$key]) && !empty($value)) {
                                $merged[$key] = trim($value);
                            }
                        }
                    }
                    if (!empty($merged['references'])) {
                        $merged['references'] = array_unique($merged['references']);
                    }
                }

            return $merged;

    }



    public function rejectRequest($id)
    {
        $request = Request::find($id);
        if ($request) {
            $request->status = 'deleted';
            $request->save();
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
