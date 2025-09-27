<!DOCTYPE html>
<html>
<head>
    <title>ACM Metadata</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { margin-bottom: 0; }
        .section { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h1>{{ is_array($acmData['title']) ? implode(' ', $acmData['title']) : $acmData['title'] }}</h1>
<p><strong>Year:</strong> {{ is_array($acmData['year']) ? implode(' ', $acmData['year']) : $acmData['year'] }}</p>
<p><strong>Authors:</strong> {{ implode(', ', $authors) }}</p>
<div class="section">
    <h2>Abstract</h2>
    <p>{{ is_array($acmData['abstract']) ? implode(' ', $acmData['abstract']) : $acmData['abstract'] }}</p>
</div>


    <div class="section">
        <h2>Introduction</h2>
        <p>{{ $acmData['introduction'] }}</p>
    </div>

    <div class="section">
        <h2>Methodology</h2>
        <p>{{ $acmData['methodology'] }}</p>
    </div>

    <div class="section">
        <h2>Purpose of Description</h2>
        <p>{{ $acmData['purposeOfDescription'] }}</p>
    </div>

    <div class="section">
        <h2>Methodology Design</h2>
        <p>{{ $acmData['methodologyDesign'] }}</p>
    </div>

    <div class="section">
        <h2>Acknowledgement</h2>
        <p>{{ $acmData['acknowledgement'] }}</p>
    </div>

    <div class="section">
        <h2>Keywords</h2>
        <p>{{ $acmData['keywords'] }}</p>
    </div>

    <div class="section">
        <h2>References</h2>
        <ul>
            @foreach ($acmData['references'] as $ref)
                <li>{{ $ref }}</li>
            @endforeach
        </ul>
    </div>

    @if(!empty($acmData['table']['rows']))
    <div class="section">
        <h2>{{ $acmData['table']['title'] }}</h2>
        <table>
            <thead>
                <tr>
                    @foreach ($acmData['table']['columns'] as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($acmData['table']['rows'] as $row)
                    <tr>
                        @foreach ($acmData['table']['columns'] as $col)
                            <td>{{ $row[$col] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>
