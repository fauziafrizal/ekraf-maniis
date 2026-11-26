<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $document->title }}</title>
    <style>
        body {
            font: 16px/1.6 Georgia, serif;
            max-width: 750px;
            margin: 3rem auto;
            padding: 1rem
        }

        header {
            text-align: center;
            border-bottom: 3px double #173f35
        }

        article {
            white-space: pre-wrap
        }

        small {
            color: #555
        }

        @media print {
            body {
                margin: 0
            }
        }
    </style>
</head>

<body>
    <header>
        <h2>{{ config('ekraf.name') }}</h2>
        <p>{{ config('ekraf.location') }}</p>
    </header>
    <h1>{{ $document->title }}</h1>
    <p>Nomor: {{ $document->number }} · {{ $document->document_date }}</p>
    <article>{{ $document->body }}</article><small>Dokumen internal; periksa dan sahkan sebelum digunakan. Gunakan menu
        cetak browser untuk PDF.</small>
</body>

</html>
