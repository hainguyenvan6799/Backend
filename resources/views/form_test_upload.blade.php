<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="/post_form_test_upload" enctype="multipart/form-data">
        @csrf
        <input type="file" name="uploadfile" id="uploadfile"/>
        <input type="submit" name="Submit" value="Upload Files"/> 
    </form>
</body>
</html>