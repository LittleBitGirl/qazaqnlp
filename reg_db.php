<?php
// die("<script type='text/javascript'>alert('Here');</script>");
require_once('connect.php');
$name = trim($_POST['name']);
$email = mysql_real_escape_string($_POST['email']);
$password = mysql_real_escape_string($_POST['password']);
$base_url = 'http://www.oqu.today/email_activation/';
if ((isset($name) && !empty($name)) && (isset($email) && !empty($email)) && (isset($password) && !empty($password))) {
    $regex = '/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/';
    if(preg_match($regex, $email)){ 
        $password=md5($password); // encrypted password
        $activation=md5($email.time());
        $query="INSERT INTO `users`(`name`, `email`, `password`,`activation`) VALUES ('$name','$email', '$password', '$activation')";
        if (mysqli_query($conn,$query)==TRUE){
            header("Location: login.php");
            $query->fetch_assoc();
            include 'smtp/send_mail.php';
            $to=$email;
            $subject="Подтверждение электронной почты";
            $body='Здравствуйте! <br/> <br/> Мы должны убедиться в том, что адрес электронной почты, который вы указали у нас на сайте действителен. Пожалуйста, подтвердите адрес вашей электронной почты, пройдя по этой ссылке: <br/> <br/> <a href="'.$base_url.'activation/'.$activation.'">'.$base_url.'activation/'.$activation.'</a>';
            Send_Mail($to,$subject,$body);
            $msg = "Регистрация выполнена успешно, пожалуйста, проверьте электронную почту."; 

        } else {  
              echo "<script>
                    alert('Не удалось связаться с сервером. Пожалуйста повторите позднее.');
                    window.location.href='signin.php';
                    </script>";
        }
    }
} else {
    echo "Адрес, введенный вами, неверен. Пожалуйста, попробуйте еще раз.";
}
?>