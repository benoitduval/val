<?php

namespace App\Services;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

class Mail
{
    const TEMPLATE_RDV          = 1;
    const TEMPLATE_CONFIRMATION = 2;

    protected $_transport;
    protected $_mail;

    public function __construct($transport)
    {
        $this->_transport = $transport;
        $this->_mail = new Message();
    }

    public function send()
    {
        $this->_transport->send($this->_mail);
    }

    public function addBcc($recipients)
    {
        $this->_mail->addBcc($recipients);
    }

    public function addFrom($recipient)
    {
        $this->_mail->addFrom($recipient);
    }

    public function setSubject($subject)
    {
        $this->_mail->setSubject($subject);
    }

    public function getContentWhiteStyle()
    {
        return '
            color: rgb(85, 85, 85);
            display: block;
            font-family: Roboto, "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 16px;
            height: 66px;
            line-height: 22px;
            margin-bottom: 11px;
            text-align: left;
            width: 90%;
            padding-left:50px;
            padding-right:50px;
            margin:0 auto;
        ';
    }

    public function getTitleStyle()
    {
        return '
            display: block;
            font-size: 50px;
            font-weight: 100;
            padding: 10px 0;
            margin-bottom: 44px;
            text-align: center;
            border-bottom: solid 1px #ccc;
            border-top: solid 1px #ccc;
            text-transform: uppercase;
            line-height: 1.2;
            width:400px;
            margin-left:auto;
            margin-right:auto;
        ';
    }

    public function getButtonStyle()
    {
        return '
        border-bottom-color: rgb(79, 191, 168);
        border-bottom-style: solid;
        border-bottom-width: 1px;
        border-image-outset: 0px;
        border-image-width: 1;
        border-color: rgb(79, 191, 168);
        color: rgb(79, 191, 168);
        cursor: pointer;
        display: inline-block;
        font-family: Roboto, "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 20px;
        height: 48px;
        letter-spacing: normal;
        line-height: 26.6px;
        margin-top: 20px;
        padding-bottom: 10px;
        padding-left: 16px;
        padding-right: 16px;
        padding-top: 10px;
        text-align: center;
        width: 121.797px;
        ';
    }

    public function getBaseStyle()
    {
        return '
            background: #ffffff;
            font-family: HelveticaNeue-Thin;
            font-size: 18px;
            font-weight: 100;
            padding-bottom:50px;
        ';
    }

    public function getSignatureStyle()
    {
        return '
            font-family: HelveticaNeue-Thin;
            font-size: 14px;
        ';
    }


    public function setTemplate($template, $data)
    {
        $data;
        switch ($template) {
            case self::TEMPLATE_RDV:
                $content = '
<div style="' . $this->getBaseStyle() . '">
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h1 style="' . $this->getTitleStyle() . '">
            Demande de RDV
        </h1>
        <p>Prénom : {firstname}</p>
        <p>Nom : {lastname}</p>
        <p>Téléphone : {phone}</p>
        <p>Date : {date}</p>
        <p>commentaire : {comment}</p>
        <a href="{baseUrl}/admin/detail/{id}" style="' . $this->getButtonStyle() . '">Voir le rendez-vous</a>
    </div>
</div>
            ';
            break;

            case self::TEMPLATE_CONFIRMATION:
                $content = '
<div style="' . $this->getBaseStyle() . '">
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h1 style="' . $this->getTitleStyle() . '">
            Rendez-vous confirmé
        </h1>
        <p>Bonjour {firstname},</p>
        <p>Votre rendez-vous du <b>{date}</b> est maintenant confirmé,<br/>en cas d\'imprévu, merci me contacter au 06.74.72.55.00</p>
        <p>Je vous dis à bientôt et vous remerci de votre confiance,</p>
        <p style="' . $this->getSignatureStyle() . '">
            Valérie Defour<br/>
            osteo.defour@gmail.com<br/>
            http://osteo-defour.fr<br/>
            06.74.72.55.00
        </p>
    </div>
</div>
            ';
            break;
        }

        $keys = array_keys($data);
        foreach ($keys as $key) $result[] = '/\{' . $key . '\}/';
        $content = preg_replace($result, array_values($data), $content);

        $html = new MimePart($content);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);
        $this->_mail->setBody($body);
    }
}
