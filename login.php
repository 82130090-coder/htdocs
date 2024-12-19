<?php
session_start();
include ("db.php");
if($_SERVER['REQUEST_METHOD']=="POST"){
   $user_email=$_POST['email'];
    $user_password=$_POST['password'];
    if (!empty($user_email) && !empty($user_password) && !is_numeric($user_email)){
      $query="SELECT * FROM signup WHERE email='$user_email' limit 1";
      $result=mysqli_query($con ,$query);
      if($result){
         if($result && mysqli_num_rows($result)>0){
            $user_data=mysqli_fetch_assoc($result);
            if($user_data['password']==$user_password){
               $_SESSION['user_id'] = $user_data['id'];
               if ($_SESSION['user_id'] == 4) {
                  header("location: ISDadmin/index.php");
              } else {
                  header("location: index.php");
              }
              die;
          }
         }
      }
      echo "<script type='text/javascript'> alert('wrong username or password'); </script>";
    }
    else{
      echo "<script type='text/javascript'> alert('wrong username or password'); </script>";
    }


}
?>

<!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
      <link rel="stylesheet" href="./styles/login.css">

      <title>Animated login form</title>
   </head>
   <body>
      <div class="login">
         <img src="images/GoldenPlates.jpg" alt="login image" class="login__img">

         <form method="POST" class="container">
            <h1 class="login__title">Login</h1>

            <div class="login__content">
               <div class="login__box">
                  <i class="ri-user-3-line login__icon"></i>

                  <div class="login__box-input">
                     <input type="email" required class="login__input" id="login-email" name="email" placeholder=" " title="enter email">
                     <label for="login-email" class="login__label">Email</label>
                  </div>
               </div>

               <div class="login__box">
                  <i class="ri-lock-2-line login__icon"></i>

                  <div class="login__box-input">
                     <input type="password" required class="login__input" id="login-pass" name="password" placeholder=" " title="enter password">
                     <label for="login-pass" class="login__label">Password</label>
                  </div>
               </div>
            </div>

            <button type="submit" class="login__button">Login</button>

            <p class="login__register">
               Don't have an account? <a href="signup.php">Sign Up</a>
            </p>
         </form>
      </div>
      
   </body>
</html>