<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Witals + Stempler</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #1a73e8; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Hello from Stempler!</h1>
        <p>User: {{ $name }}</p>
        
        @if(count($items) > 0)
            <ul>
                @foreach($items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</body>
</html>
