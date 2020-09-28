<?php session_start();
//echo $_SESSION['ERROR'];
require_once "Functions.php";
//echo $_SESSION['ERROR'];
checkErrors();
session_destroy();
$_SESSION = array();
?>
<html>
<head>
    <title>Sign in</title>
    <style>
        .sign_in {
            width: 370px;
            margin: auto;
        }
        body{
            font-family: 'Arial' !important;
        }
    </style>
</head>

<body>
    <div class="sign_in" align="center">
        <fieldset>
            <legend>
                <h4>Sign in :</h4>
            </legend>
            <form method="POST" action="/UsersLogic.php" name="Sign_in">
                Username : <br><input type="text" name=username size=30><br>
                Password : <br><input type=password name=password size=30><br>
                <button type="submit" name="Sign_in" style="float: right">Sign in</button>
        </fieldset>
        <p style="text-align:left;">Not a member yet?
            <span style="float:right;">Don't want to join?</span>
        </p><button type="submit" name="Register" style="float: left" formaction="/signup.php">Register</button>
        <button type="submit" name="No_Registration" style="float: right">Continue</button>
        </form>
    </div>
</body>

</html>