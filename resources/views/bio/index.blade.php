<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stored Fingerprints</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }
        .gallery-title {
            font-weight: bold;
            color: #333;
        }
        .card {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.03);
        }
        .card img {
            object-fit: cover;
            height: 250px;
        }
    </style>
</head>
<body>
    <button id="scanFingerprint">Scan Fingerprint</button>
<img id="fingerprintPreview" style="display:none;" />

<script>
document.getElementById('scanFingerprint').addEventListener('click', async () => {
    try {
        // Call C# API
        const res = await fetch('http://localhost:5000/capture');
        const data = await res.json();

        if (data.success) {
            const img = document.getElementById('fingerprintPreview');
            img.src = `data:image/bmp;base64,${data.image}`;
            img.style.display = 'block';

            // Optional: send to Laravel backend for storage
            await fetch('/api/fingerprint/store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image: data.image })
            });
        } else {
            alert('Capture failed: ' + data.message);
        }
    } catch (err) {
        console.error(err);
        alert('Error calling C# service');
    }
});
</script>



    <div class="container py-5">
        <h2 class="mb-4 text-center gallery-title">Stored Fingerprints</h2>

        @if(count($files) > 0)
            <div class="row g-4">
                @foreach($files as $file)
                    <div class="col-md-3 col-sm-6">
                        <div class="card shadow-sm h-100">
                            <img src="{{ $file }}" class="card-img-top" alt="Fingerprint">
                            <div class="card-body text-center">
                                <p class="small text-muted mb-0">{{ basename($file) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info text-center">
                No fingerprints uploaded yet.
            </div>
        @endif
    </div>

    <!-- Bootstrap JS -->
    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
</body>
</html>
