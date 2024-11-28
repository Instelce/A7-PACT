<?php

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <title>Test pdf</title>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Hello</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate deleniti error eum natus non quia rem soluta unde! Ab, doloremque, tempora. Dignissimos neque perspiciatis repellat! Deserunt eum excepturi exercitationem laboriosam!</p>

    <img src="http://localhost:8080/assets/images/avatar.png">
    
    <table>
        <thead>
            <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John</td>
                <td>Doe</td>
                <td>
                    <a href="mailto:toto@pact.com">toto@pact.com</a>
                </td>
            </tr>
            <tr>
                <td>Jane</td>
                <td>Doe</td>
                <td>
                    <a href="mailto:jj@pact.com">jj@pact.com</a>
                </td>
            </tr>
    </table>

    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate deleniti error eum natus non quia rem soluta unde! Ab, doloremque, tempora. Dignissimos neque perspiciatis repellat! Deserunt eum excepturi exercitationem laboriosam!</p>

    <ul>
        <li>Item 1</li>
        <li>Item 2</li>
        <li>Item 3</li>
        <li>Item 4</li>
    </ul>
</body>
</html>
