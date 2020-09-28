<?php session_start();
require_once("Functions.php");
checkErrors();
session_destroy();
$_SESSION = array();
?>
<html>

<head>
  <title>Sign up</title>
  <style>
    .sign_up {
      width: 370px;
      margin: auto;
    }
    body{
            font-family: 'Arial' !important;
        }
  </style>
</head>

<body>
  <div class="sign_up" align="center">
    <fieldset>
      <legend align="left">
        <h4>Sign up :</h4>
      </legend>
      <form method="POST" action="/UsersLogic.php" name="Sign_up">
        E-mail : <br><input type=email name=email size=30><br>
        Username : <br><input type=text name=username size=30><br>
        Password : <br><input type=password name=password size=30><br>
        Name : <br><input type=text name=name size=30><br>
        <button type="submit" name="Sign_up" style="float: right">Sign up</button>
    </fieldset>
    <p align=left>Already have an account?
      <button type="submit" name="Sign_in" style="float: right" formaction="/signin.php">Sign in</button> </p>
    </form>
  </div>
</body>

</html>