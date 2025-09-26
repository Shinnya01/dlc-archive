<!-- filepath: resources/views/pdf/ai-response.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>AI Response</title>
</head>
<body>
    <h1>AI Generated Response</h1>
    <p>{{ $content }}</p>

    @if(!empty($downloadLink))
    <a href="{{ $downloadLink }}" target="_blank" class="btn">Download AI PDF</a>
    @endif
</body>
</html>