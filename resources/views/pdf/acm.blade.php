<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $acmData['title'] }}</title>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
        line-height: 1.4;
        margin: 20px;
        text-align: justify;
    }

    /* Title & Authors (single column) */
    .title { text-align: center; font-weight: bold; font-size: 18pt; margin-bottom: 10px; }
    .authors { text-align: center; margin-bottom: 20px; }
    .author { display: inline-block; margin: 0 10px; text-align: center; }

    /* Headings */
    h2 { margin-top: 0; }
</style>
</head>
<body>

<h1 class="title">{{ $acmData['title'] }}</h1>

<div class="authors">
    @foreach($authors as $author)
        <div class="author">
            <p>{{ $author['name'] }}</p>
            <p>{{ $author['program'] }}</p>
            <p>{{ $author['university'] }}</p>
            <p>{{ $author['email'] }}</p>
        </div>
    @endforeach
</div>

<!-- Multi-column content -->
<div style="columns:2; column-gap:20px;">

<h2>ABSTRACT</h2>
<p>{{ $acmData['abstract'] }}</p>

<h2>KEYWORDS</h2>
<p>{{ $acmData['keywords'] }}</p>

<h2>1. INTRODUCTION</h2>
<p>{{ $acmData['introduction'] }}</p>

<h2>2. PURPOSE OF DESCRIPTION</h2>
<p>{{ $acmData['purposeOfDescription'] }}</p>

<h2>3. METHODOLOGY</h2>
<p>{{ $acmData['methodology'] }}</p>

<h3>3.1 Methodology Design</h3>
<p>{{ $acmData['methodologyDesign'] }}</p>

@if(isset($acmData['table']) && count($acmData['table']['rows']) > 0)
<h2>4. EVALUATION RESULTS</h2>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
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
@endif

<h2>ACKNOWLEDGEMENT</h2>
<p>{{ $acmData['acknowledgement'] }}</p>

<h2>REFERENCES</h2>
<ol>
@foreach($acmData['references'] as $ref)
<li>{{ $ref }}</li>
@endforeach
</ol>

</div>
</body>
</html>
