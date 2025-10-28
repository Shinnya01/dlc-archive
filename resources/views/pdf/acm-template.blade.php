<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $acmData['title'] }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
        line-height: 1.5;
        margin: 0;
        padding: 0;
        text-align: justify;
    }

    /* Title & Authors */
    .title {
        font-size: 18pt;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    .authors-block {
        text-align: center;
        margin-bottom: 30px;
    }

    .author-group {
        display: inline-block;
        margin: 0 15px;
        vertical-align: top;
        text-align: center;
    }

    .author-group p {
        margin: 2px 0;
    }

    .author-name {
        font-weight: bold;
        font-size: 11pt;
        margin-top: 10px !important;
    }

    /* Abstract & Keywords */
    .abstract-container,
    .keywords-container {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 15px;
    }

    .abstract-title,
    .keywords-title {
        font-style: italic;
        font-weight: bold;
    }

    /* Two-column main content */
    .two-column {
        column-count: 2;
        column-gap: 0.3in;
        margin-bottom: 15px;
    }

    h2.section-header,
    h3 {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    /* Tables */
    .table-container {
        width: 100%;
        margin: 15px 0;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt;
    }

    .data-table th,
    .data-table td {
        border: 1px solid #000;
        padding: 5px;
    }

    .data-table th {
        background-color: #f2f2f2;
        text-align: center;
        font-weight: bold;
    }

    .data-table td:nth-child(2),
    .data-table td:nth-child(3) {
        text-align: center;
    }

    /* References */
    .references {
        column-count: 1;
        /* Single column */
        padding-left: 20px;
        margin-top: 5px;
    }

    .references li {
        margin-bottom: 5px;
    }
    </style>
</head>

<body>

    <h1 class="title">{{ $acmData['title'] }}</h1>

    <div class="authors-block">
        @foreach($authors as $author)
        <div class="author-group">
            <p class="author-name">{{ $author['name'] }}</p>
            <p>{{ $author['program'] }}</p>
            <p>{{ $author['university'] }}</p>
            <p><a href="mailto:{{ $author['email'] }}">{{ $author['email'] }}</a></p>
        </div>
        @endforeach
    </div>

    <div class="abstract-container">
        <p><span class="abstract-title">ABSTRACT: </span>{{ $acmData['abstract'] }}</p>
    </div>

    <div class="keywords-container">
        <p><span class="keywords-title">Keywords: </span>{{ $acmData['keywords'] }}</p>
    </div>

    <!-- Main two-column content -->
    <div class="two-column">
        <h2 class="section-header">1. INTRODUCTION</h2>
        <p>{{ $acmData['introduction'] }}</p>

        <h2 class="section-header">2. PURPOSE OF DESCRIPTION</h2>
        <p>{{ $acmData['purposeOfDescription'] }}</p>

        <h2 class="section-header">3. METHODOLOGY</h2>
        <p>{{ $acmData['methodology'] }}</p>

        <h3>3.1 Methodology Design</h3>
        <p>{{ $acmData['methodologyDesign'] }}</p>
    </div>

    @if(isset($acmData['table']) && count($acmData['table']['rows']) > 0)
    <h2 class="section-header">4. EVALUATION RESULTS</h2>
    <div class="table-container">
        <p style="text-align: center; font-style: italic; margin-bottom: 5px;">Table 1: Evaluation Summary Based on ISO
            25010 Standards</p>
        <table class="data-table">
            <thead>
                <tr>
                    @foreach($acmData['table']['columns'] as $column)
                    <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($acmData['table']['rows'] as $row)
                <tr>
                    @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <h2 class="section-header">ACKNOWLEDGEMENT</h2>
    <p>{{ $acmData['acknowledgement'] }}</p>

    <h2 class="section-header">REFERENCES</h2>
    <div class="references">
        <ol>
            @foreach($acmData['references'] as $reference)
            <li>{{ $reference }}</li>
            @endforeach
        </ol>
    </div>

</body>

</html>