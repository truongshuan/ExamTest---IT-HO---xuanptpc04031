<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Testing - Pham Truong Xuan</title>
</head>

<body>
  <h1>Upload file data</h1>
  <br>
  <form action="{{ route('export') }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="file" name="file">
    <button type="submit">Export</button>
  </form>
</body>

</html>
