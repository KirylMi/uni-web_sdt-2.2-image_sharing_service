<?php
function sendActivationEmail($address,$activationCode)
{
    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';
    require 'phpmailer/Exception.php';
    //dotenv
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPAuth   = true;

        // Настройки почты
        $mail->Host       = getenv("MAIL_SSL"); // SMTP сервера GMAIL
        //$mail->SMTPDebug  = 4;
        $mail->Username   = getenv("MAIL_USER"); // Логин на почте
        $mail->Password   = getenv("MAIL_PASS"); // Пароль на почте
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = getenv("MAIL_PORT");
        $mail->setFrom(getenv("MAIL_EMAIL"), getenv("MAIL_FROM")); // Адрес самой почты и имя отправителя

        // Получатель письма
        $mail->addAddress($address);
        //$mail->addAddress('rofloemail'); // Ещё один, если нужен

        // Прикрипление файлов к письму
        // if (!empty($_FILES['myfile']['name'][0])) {
        //     for ($ct = 0; $ct < count($_FILES['myfile']['tmp_name']); $ct++) {
        //         $uploadfile = tempnam(sys_get_temp_dir(), sha1($_FILES['myfile']['name'][$ct]));
        //         $filename = $_FILES['myfile']['name'][$ct];
        //         if (move_uploaded_file($_FILES['myfile']['tmp_name'][$ct], $uploadfile)) {
        //             $mail->addAttachment($uploadfile, $filename);
        //         } else {
        //             $msg .= 'Неудалось прикрепить файл ' . $uploadfile;
        //         }
        //     }   
        // }
        
        // Само письмо
        $mail->isHTML(true);
        $mail->Subject = 'Your activation mail has finally arrived! ^_^';
        $mail->Body    = '<h2>Hi there! Captain Speaking</h2>
        <font size="4">Not very long ago you have registered on our website (localhost:8001))))))<br>
        <a href="locatlhost:8001/profile/code/">clicking</a>If it wasn\'t you, then please, ignore this message,<br>
        But if it WAS you, then please, follow the link below:<br>
        <a href="http://localhost:8001/profile/' . $activationCode . '">Click</a></font>';  //Not tested after <br> and <a> PASS

        $mail->send();
        #PASS CHECK WITH THROW
        //if ($mail->send()) {
        //    echo "$msg";
        //} else {
        //    echo "Сообщение не было отправлено. Неверно указаны настройки вашей почты";
        //}
        #PASS SWAP CATCH TO THROW TO THE PARENT
    } catch (Exception $e) {
        echo "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
    }
}


function sendChangePrivilege($username){
    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';
    require 'phpmailer/Exception.php';
    //dotenv
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPAuth   = true;
        // Настройки почты
        $mail->Host       = getenv("MAIL_SSL"); // SMTP сервера GMAIL
        //$mail->SMTPDebug  = 4;
        $mail->Username   = getenv("MAIL_USER"); // Логин на почте
        $mail->Password   = getenv("MAIL_PASS"); // Пароль на почте
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = getenv("MAIL_PORT");
        $mail->setFrom(getenv("MAIL_EMAIL"), getenv("MAIL_FROM")); // Адрес самой почты и имя отправителя

        // Получатель письма
        $mail->addAddress(getenv("MAIL_EMAIL"));
        //$mail->addAddress('rofloemail'); // Ещё один, если нужен

        // Прикрипление файлов к письму
        // if (!empty($_FILES['myfile']['name'][0])) {
        //     for ($ct = 0; $ct < count($_FILES['myfile']['tmp_name']); $ct++) {
        //         $uploadfile = tempnam(sys_get_temp_dir(), sha1($_FILES['myfile']['name'][$ct]));
        //         $filename = $_FILES['myfile']['name'][$ct];
        //         if (move_uploaded_file($_FILES['myfile']['tmp_name'][$ct], $uploadfile)) {
        //             $mail->addAttachment($uploadfile, $filename);
        //         } else {
        //             $msg .= 'Неудалось прикрепить файл ' . $uploadfile;
        //         }
        //     }
        // }

        // Само письмо
        $mail->isHTML(true);
        $mail->Subject = "$username is asking for the admin rights";
        $mail->Body    = '<h2>Hi there! I\'m speaking to myself!</h2>
        <font size="4">Not very long ago someone decided that he can get admin rights....<br>
        If you know this '.$username.' guy, then please, follow the link below: (WORK IN PROGRESS.....)<br>
        <a href="http://localhost:8001/users/">Click</a></font>';  //Not tested after <br> and <a> PASS
        $mail->send();
        #PASS CHECK WITH THROW
        //if ($mail->send()) {
        //    echo "$msg";
        //} else {
        //    echo "Сообщение не было отправлено. Неверно указаны настройки вашей почты";
        //}
        #PASS SWAP CATCH TO THROW TO THE PARENT
    } catch (Exception $e) {
        echo "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
    }
}