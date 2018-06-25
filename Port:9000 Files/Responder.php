<!-- User Registration page -->

<html>
<head><title>User Registration</title></head>
<body>
  <?php
  $checker = 0;

  //check for post request	
  if(isset($_POST['submit'])){
    //setup database connection
    require_once('config.php');
    $query = "INSERT INTO USERS(USERNAME, PASSWORD, NAME, EMAIL,COUNT) VALUES (?,?,?,?,?);";
  $stmt = mysqli_prepare($dbc,$query);

  //Compare user inputs against the setup constraints
  if(empty($_POST['username']) || strlen($_POST['username'])>10 || strlen($_POST['username'])<5){
    $checker = 1;
    echo "username cannot be empty, has to be between 5-10 characters<br>";
  }else {
    $USERNAME = $_POST['username'];
  }

  if(empty($_POST['password']) || !(preg_match("/[a-zA-Z]+/",$_POST["password"])&&preg_match("/[0-9]+/",$_POST["password"])&&preg_match("/[._^%$#!~@-]/",$_POST["password"]))){
    $checker = 1;
    echo "password cannot be empty, must contain one alphabet, one numeric and one special character <br>";
  }else {
    $PASSWORD = sha1($_POST['password']);
  }

  if(empty($_POST['email']) || !preg_match("/[a-zA-Z0-9]*[-_.]*[a-zA-Z0-9]*@[a-zA-Z]*[.]+[a-zA-Z.]{2,6}/",$_POST['email'])){
    $checker = 1;
    echo "email cannot be empty or check your proper format <br>";
  }else {
    $EMAIL = $_POST['email'];
  }

  if(empty($_POST['name'])){
    $checker = 1;
    echo "name cannot be empty<br>";
  }else {
    $NAME = $_POST['name'];
  }

  //If all check passes:----

  if($checker!=1){
    $initCount = 0;
    mysqli_stmt_bind_param($stmt,"ssssi",$USERNAME,$PASSWORD,$NAME,$EMAIL,$initCount);
    mysqli_stmt_execute($stmt);
    $affectedRows = mysqli_stmt_affected_rows($stmt);

    //If there is some error in adding:
    if($affectedRows < 1){
      echo "SOME ERROR ";
      echo $affectedRows . "were affected";
    }else{
      echo "Registration succesfull";
      mysqli_stmt_close($stmt);
      mysqli_close($dbc);
    }
  } else {
    //Close the connection
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
  }
}
  ?>

  <!-- HTML form for user input on registration -->

  <form action="Responder.php" method="post">
    <center><b>User Registration</b>
    <table>
      <tr>
    <td><p>User Name :
    <td><input type="text" name="username" size="30" value="" />
    </p></tr>

    <tr>
    <td><p>Password :
    <td><input type="password" name="password" size="30" value="" />
    </p></tr>

    <tr>
    <td><p>Full Name :
    <td><input type="text" name="name" size="30" value="" />
    </p></tr>

    <tr>
    <td><p>Email :
    <td><input type="text" name="email" size="30" value="" />
    </p></tr>
    </table>

    <p>
      <input type="submit" name="submit" value="Send"/>
    </p>

  <p>
    Already have an account? Login from  : <a href="login.php"> Login Here ! </a>
  </p>
  </form>
  </body>
  </html>
