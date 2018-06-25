<?php
  $checker = 0;
  
  //Connect to the database:	
  require_once('config.php');
  
  //Check the post request against the given conditions	
  if(isset($_POST['login'])){
    $query = "SELECT USERNAME, PASSWORD, COUNT FROM USERS WHERE USERNAME = ? ";
    $stmt = mysqli_prepare($dbc,$query);

    if(empty($_POST['username']) || strlen($_POST['username'])>10 || strlen($_POST['username'])<5){
      $checker = 1;
      echo "username cannot be empty, has to be between 5-10 characters<br>";
    }else {
      $USERNAME = $_POST['username'];
    }

    if(empty($_POST['password']) || !preg_match("/(?=[a-zA-Z]*)(?=[0-9]*)(?=[._^%$#!~@-]*)/",$_POST["password"])){
      $checker = 1;
      echo "password cannot be empty, must contain one alphabet, one numeric and one special character <br>";
    }else {
      $PASSWORD = sha1($_POST['password']);
    }

    //If all conditions are satisfied:	
    if($checker!=1){
      //Extract from the database	
      mysqli_stmt_bind_param($stmt,"s",$USERNAME);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $affectedRows = mysqli_stmt_num_rows($stmt);

      //Check if user exists	
      if($affectedRows < 1){
        echo "User doesnt exist click here to register: " .  "<a href='a.php'> Register Here ! </a>";
      }else{
        mysqli_stmt_bind_result($stmt,$URNAME,$PASSHASH,$count);
        mysqli_stmt_fetch($stmt);
        if($PASSHASH==$PASSWORD){
          update_count($USERNAME,$count,$dbc);
	  //Setup Session variables, if checkbox is selected
          if(isset($_POST['checkbox'])){
            session_start();
            $_SESSION['username'] = $USERNAME;
            $_SESSION['password'] = $PASSWORD;
            $_SESSION['count'] = $count;	
            header("location: welcome.php");
	  //If no session is found:
          } else {
            $count++;
            echo "Welcome , " . $USERNAME . " You have been succesfully logged in " . $count . " times!";
          }
	  //Incase the password match fails:
        } else {
          echo "Invalid Password";
        }
      }
    } else {
      mysqli_stmt_close($stmt);
      mysqli_close($dbc);
    }
    //Close the connection	
    mysqli_close($dbc);
  }

  //If session exists,load data from session variables	
  session_start();
  if(isset($_SESSION['username'])){
    update_count($_SESSION['username'],$_SESSION['count'],$dbc);
    $_SESSION['count']++;
    echo "Welcome , " . $_SESSION['username'] . " You have visited us " .  $_SESSION['count'] . " times!";
    echo "<br> Log out from the session by: <a href = 'logout.php'> Clicking Here </a>";
  }


  //Custom function to increment views by user	
  function update_count($u,$c,$db){
    $query1 = "UPDATE USERS SET COUNT = ? WHERE USERNAME = ?";
    $stmt1 = mysqli_prepare($db,$query1);
    $c++;
    mysqli_stmt_bind_param($stmt1,"is",$c,$u);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);
  }
?>
