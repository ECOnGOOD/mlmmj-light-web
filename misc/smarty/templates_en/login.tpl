<html>
    <head>
        <title>Login | {$headline}</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script>
            function validate_form()
            {
                var username = document.getElementById('username_input').value;
                var password = document.getElementById('password_input').value;

                if (username == "")
                {
                    alert("Please enter your username.");
                    return false;
                }

                if (password == "")
                {
                    alert("Please enter your password.");
                    return false;
                }
                if (username.match(/[^A-Za-z\-\.]/))
                {
                    alert("The username may only contain english letters, dots and hyphens.");
                    return false;
                }
            }
        </script>
    </head>
    <body>
        <div id="header">
            <div id="header_left">
                <a href="{$web_url}">{$headline}</a>
            </div>
        </div>
        <div id="login">
            <div id="login_form">
                <p>Please enter the credentials of your account<br />(<strong>without</strong> <i>@example.com</I>)</p>
				<br />
                <form method="post" action="login.php" onsubmit="return validate_form()">
                    <div id="username">
                        <div id="username_left">
                            Username:
                        </div>
                        <div id="username_right">
                            <input type="text" name="login_username" id="username_input" autofocus>
                        </div>
                    </div>
                    <div id="password">
                        <div id="password_left">
                            Password:
                        </div>
                        <div id="password_right">
                            <input type="password" name="login_pass" id="password_input">
                        </div>
                    </div>
                    <div id="enter">
                        <input type="submit" name="submit" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
