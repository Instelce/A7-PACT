<?php

namespace app\core;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        if ($_ENV['APP_ENVIRONMENT'] === 'dev') {
            $this->mailer->isSMTP();
            $this->mailer->Host = 'localhost';
            $this->mailer->Port = 2525;
            $this->mailer->SMTPAuth = false;
            $this->mailer->SMTPAutoTLS = false;
        } else {
            $this->mailer->isSMTP();
            $this->mailer->SMTPAuth = true;
            $this->mailer->Host = $_ENV['MAIL_HOST'];
            $this->mailer->Username = $_ENV['MAIL_USERNAME'];
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
            $this->mailer->Port = $_ENV['MAIL_PORT'];
        }
        $this->mailer->CharSet = 'UTF-8';
    }

    public function send($to, $subject, $view, $params = [])
    {
        // view c'est le nom du fichier php qui sera choisi (welcome par exemple)
        $this->mailer->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_NAME']);
        $this->mailer->addAddress($to);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = Application::$app->view->renderOnlyViewWithLayout("mail", "mail/" . $view, $params);
        $this->mailer->AltBody = strip_tags($this->mailer->Body);
        $this->mailer->send();
    }
}