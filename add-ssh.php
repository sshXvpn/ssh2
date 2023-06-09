<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(0);

function sanitizeInput($input) {
    $sanitized = trim($input); 
    $sanitized = htmlspecialchars($sanitized);
    return $sanitized;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = sanitizeInput($_POST['username']);
    $pass = sanitizeInput($_POST['password']);
    $masaaktif = "5";

    $sshUser = 'root'; # vps users
    $sshPassword = 'your_password'; # vps password
    $host = 'sshxvpn.com'; # hostname or vps public ip

    $sshCommand = "{ echo $login; echo $pass; echo $masaaktif; } | /usr/local/sbin/add-ssh"; # shell file to add ssh user

    $response = [
        'status' => 'error',
        'message' => '',
        'account_info' => ''
    ];

    $connection = ssh2_connect($host, 22); # part port ini saya sangat2 rekomenkan untuk tukar ke port lain, selain 22
    if (!$connection) {
        $response['message'] = 'Failed to connect to the VPS host.';
    } elseif (!ssh2_auth_password($connection, $sshUser, $sshPassword)) {
        $response['message'] = 'Authentication failed. Please check your SSH credentials.';
    } else {
        $stream = ssh2_exec($connection, $sshCommand);
        stream_set_blocking($stream, true);
        $accountInfo = stream_get_contents($stream);
        fclose($stream);
        ssh2_disconnect($connection);

        if (strpos($accountInfo, 'already exists') !== false) {
            $response['message'] = "User '$login' already exists.";
        } else {
            list($var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9) = explode("\n", $accountInfo);
            $ipAddress = preg_replace('/[^0-9.]/', '', $var1);
            $ipAddress = str_replace('23', '', $ipAddress);
            $status = '
                <div class="alert alert-success alert-dismissible" role="alert">
                    Account Created<br><br>
                    <b>IP Address:</b> '.$ipAddress.'<br>
                    <b>Hostname:</b> '.$var2.'<br>
                    <b>Username:</b> '.$var3.'<br>
                    <b>Password:</b> '.$var4.'<br>
                    <b>OpenSSH:</b> '.$var5.'<br>
                    <b>OpenSSH SSL:</b> '.$var6.'<br>
                    <b>OpenSSH OHP:</b> '.$var7.'<br>
                    <b>SSH WS SSL:</b> '.$var8.'<br>
                </div>';

            $response['status'] = 'success';
            $response['message'] = 'SSH account created successfully on the VPS.';
            $response['account_info'] = $status;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create SSH Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Create SSH Account</h1>
        <div id="responseMessage" class="alert" role="alert"></div>
        <div id="accountInfo"></div>

        <form id="sshForm" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Create SSH Account">
        </form>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        var form = document.getElementById('sshForm');
        var responseMessage = document.getElementById('responseMessage');
        var accountInfo = document.getElementById('accountInfo');

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Create an XMLHttpRequest object
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Set the URL to the same page ('' means the current page)
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            // Handle the AJAX response
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    responseMessage.className = 'alert alert-' + response.status;
                    responseMessage.innerHTML = response.message;
                    accountInfo.innerHTML = response.account_info;
                }
            };

            // Prepare the form data to be sent
            var formData = new FormData(form);

            // Send the AJAX request
            xhr.send(formData);
        });
    </script>
</body>
</html>
