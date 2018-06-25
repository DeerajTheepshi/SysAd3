<!-- Login page, also the index page of the server -->

<html>
<body>
  <?php

    //Check if there is a session existing, if so redirect to the welcome page
    session_start();
    if(isset($_SESSION['username'])){
      header("location: welcome.php");
      exit;
    }
   ?>

  <!-- User input form for logging in -->
  <form action="welcome.php" method="post">
    <center><b>User Login</b>
    <table>
      <tr>
    <td><p>User Name :
    <td><input type="text" name="username" size="30" value="" />
    </p></tr>

    <tr>
    <td><p>Password :
    <td><input type="password" name="password" size="30" value="" />
    </p></tr>
    </table>

    <p>
      <input type="checkbox" name="checkbox" value=""/> Remeber Me
    </p>


    <p>
      <input type="submit" name="login" value="Send"/>
    </p>

   <!-- REDIRECT TO REGISTRATION -->	
    <p>
      Create a new User from : <a href="http://localhost:9000"> Register Here ! </a>
    </p>


</center>
</form>
</body>
</html>
