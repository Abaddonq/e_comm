<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800">500</h1>
            <p class="text-xl text-gray-600 mt-4">Server Error</p>
            <p class="text-gray-500 mt-2">Something went wrong on our end. Please try again later.</p>
            @if(app()->environment('local'))
                <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg text-left max-w-lg mx-auto">
                    <p class="font-bold">Error Details:</p>
                    <pre class="text-sm mt-2 whitespace-pre-wrap">{{ $exception->getMessage() ?? 'No error message' }}</pre>
                </div>
            @endif
            <a href="/" class="inline-block mt-6 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Go to Homepage
            </a>
        </div>
    </div>
</body>
</html>
