<?php

namespace App\Services;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

class Mail
{
    const TEMPLATE_COMMENT      = 1;
    const TEMPLATE_GROUP        = 2;
    const TEMPLATE_EVENT        = 3;
    const TEMPLATE_REMINDER     = 4;
    const TEMPLATE_EVENT_UPDATE = 5;
    const TEMPLATE_PASSWORD     = 6;

    protected $_transport;
    protected $_mail;

    public function __construct($transport)
    {
        $this->_transport = $transport;
        $this->_mail = new Message();
    }

    public function send()
    {
        $this->_mail->addFrom('volley@rdv-volley.com');
        $this->_transport->send($this->_mail);
    }

    public function addBcc($recipients)
    {
        $this->_mail->addBcc($recipients);
    }

    public function setSubject($subject)
    {
        $this->_mail->setSubject($subject);
    }

    public function getContentWhiteStyle()
    {
        return '
            background-color: rgb(255, 255, 255);
            border-top-color: rgb(221, 221, 221);
            border-top-style: solid;
            border-top-width: 1px;
            box-sizing: border-box;
            color: rgb(51, 51, 51);
            display: block;
            float: left;
            font-family: \'Helvetica Neue\', Verdana, sans-serif;
            font-size: 18px;
            font-weight: 100;
            line-height: 26px;
            min-height: 1px;
            padding-bottom: 40px;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 40px;
            position: relative;
            text-align: center;
            width:95%;
        ';
    }

    public function getContentGreyStyle()
    {
        return '
            background-color: rgb(243, 243, 242);
            border-top-color: rgb(221, 221, 221);
            border-top-style: solid;
            border-top-width: 1px;
            box-sizing: border-box;
            color: rgb(51, 51, 51);
            display: block;
            float: left;
            font-family: \'Helvetica Neue\', Verdana, sans-serif;
            font-size: 18px;
            font-weight: 100;
            line-height: 26px;
            padding-bottom: 40px;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 40px;
            position: relative;
            width:95%;
        ';
    }

    public function getTitleStyle()
    {
        return '
            color: rgb(51, 51, 51);
            display: block;
            font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;
            font-size: 36px;
            font-weight: 500;
            line-height: 40px;
            margin-bottom: 10px;
            margin-top: 20px;
            text-align: center;
        ';
    }

    public function getButtonStyle()
    {
        return '
            display:block;
            line-height:2em;
            margin:20px auto 0;
            font-size: 20px;
            height: 44px;
            border-radius: 10px;
            background-image: linear-gradient(to bottom,#5bc0de 0,#2aabd2 100%);
            background-repeat: repeat-x;
            border-color: #28a4c9;
            width:400px;
            color:#fff;
            text-align:center;
            text-decoration:none;
        ';
    }

    public function getBaseStyle()
    {
        return '
            background: #ffffff;
            font-family: HelveticaNeue-Thin;
            font-size:
            18px; font-weight: 100;
            padding-bottom:50px;
        ';
    }

    public function setTemplate($template, $data)
    {
        $data;
        switch ($template) {
            case self::TEMPLATE_COMMENT:
                $content = '
<div style="' . $this->getBaseStyle() . '">
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h1 style="' . $this->getTitleStyle() . '">
            {eventName}
        </h1>
    </div>
    <div style="' . $this->getContentGreyStyle() . '">
        <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;text-decoration:underline;">
            {user}, {date}
        </h3>
        <div style="margin-left:50px;">
            <i>{comment}</i>
        </div>
    </div>
    <div style ="' . $this->getContentWhiteStyle() . '">
        <a href="{baseUrl}/event/detail/{eventId}#comment" style="' . $this->getButtonStyle() . '">Repondre</a>
    </div>
</div>
                ';
                break;

            case self::TEMPLATE_PASSWORD:
                $content = '
<div style="' . $this->getBaseStyle() . '">
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h1 style="' . $this->getTitleStyle() . '">
            Ton nouveau mot de passe
        </h1>
    </div>
    <div style="' . $this->getContentGreyStyle() . '">
        <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;text-decoration:underline;">
            Pour le compte {email}
        </h3>
        <div style="margin-left:50px;">
            Ce mot de passe à été généré aléatoirement : <i>{password}</i> <br/>
            Tu peux maintenant te connecter à ton compte avec ce nouveau mot de passe.
        </div>
        <div style ="' . $this->getContentWhiteStyle() . '">
            <a href="{baseUrl}/auth/login" style="' . $this->getButtonStyle() . '">Repondre</a>
        </div>
    </div>
</div>
                ';
                break;

            case self::TEMPLATE_EVENT:
                $content = '
<div style="' . $this->getBaseStyle() . '">
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h1 style="' . $this->getTitleStyle() . '">
            {eventName}
        </h1>
    </div>
    <div style="' . $this->getContentGreyStyle() . '">
        <table style="width:100%;">
            <tr>
                <td style="width:50%; padding-right:20px;">
                    <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
                        <img src="{baseUrl}/img/map-marker.png" /> Tu es attendu <br /><b>le {date}</b> <br/>à cette adresse
                    </h3>
                    <div style="margin-left:50px; margin-top:20px;">
                        {name}<br />
                        {address}<br />
                        {zip} {city}<br /><br />
                    </div>
                </td>
                <td style="width:50%; padding-right:20px; vertical-align:top;">
                    <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
                        <img src="{baseUrl}/img/comment.png" /> Commentaire
                    </h3>
                    <i style="margin-top:50px;">{comment}</i>
                </td>
            </tr>
        </table>
    </div>
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
            Ta disponibilité
        </h3>
        <div style="margin-left:50px; margin-top:20px;text-align:center;">
            <a href="{baseUrl}/guest/response/{eventId}/{ok}" style="margin-right:50px;"><img src="{baseUrl}/img/white-ok.png" alt="présent"/></a>
            <a href="{baseUrl}/guest/response/{eventId}/{no}"><img src="{baseUrl}/img/white-no.png" style="margin-right:50px;" alt="Absent"/></a>
            <a href="{baseUrl}/guest/response/{eventId}/{perhaps}"><img src="{baseUrl}/img/white-incertain.png" alt="Incertain"/></a>
        </div>
        <a href="{baseUrl}/event/detail/{eventId}" style="' . $this->getButtonStyle() . '">Voir les détails</a>
    </div>
</div>
                ';
                break;

                case self::TEMPLATE_EVENT_UPDATE:
                $content = '
<div style="' . $this->getBaseStyle() . '">
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h1 style="' . $this->getTitleStyle() . '">
            {eventName}
        </h1>
    </div>
    <div style="' . $this->getContentGreyStyle() . '">
        <table style="width:100%;">
            <tr>
                <td style="width:50%; padding-right:20px;">
                    <h2> ATTENTION, événement mis à jour !</h2>
                    <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
                        <img src="{baseUrl}/img/map-marker.png" /> Tu es attendu <br /><b>le {date}</b> <br/>à cette adresse
                    </h3>
                    <div style="margin-left:50px; margin-top:20px;">
                        {name}<br />
                        {address}<br />
                        {zip} {city}<br /><br />
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
            Ta disponibilité
        </h3>
        <div style="margin-left:50px; margin-top:20px;text-align:center;">
            <a href="{baseUrl}/guest/response/{eventId}/{ok}" style="margin-right:50px;"><img src="{baseUrl}/img/white-ok.png" alt="présent"/></a>
            <a href="{baseUrl}/guest/response/{eventId}/{no}"><img src="{baseUrl}/img/white-no.png" style="margin-right:50px;" alt="Absent"/></a>
            <a href="{baseUrl}/guest/response/{eventId}/{perhaps}"><img src="{baseUrl}/img/white-incertain.png" alt="Incertain"/></a>
        </div>
        <a href="{baseUrl}/event/detail/{eventId}" style="' . $this->getButtonStyle() . '">Voir les détails</a>
    </div>
</div>
                ';
                break;

            case self::TEMPLATE_REMINDER:
                $content = '
<div style="' . $this->getBaseStyle() . '">
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h1 style="' . $this->getTitleStyle() . '">
            {eventName}
        </h1>
    </div>
    <div style="' . $this->getContentGreyStyle() . '">
        <table style="width:100%;">
            <tr>
                <td style="width:50%; padding-right:20px;">
                    <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
                        <img src="{baseUrl}/img/map-marker.png" /> Tu es attendu <br /><b>le {date}</b> <br/>à cette adresse
                    </h3>
                    <div style="margin-left:50px; margin-top:20px;">
                        {name}<br />
                        {address}<br />
                        {zip} {city}<br /><br />
                    </div>
                </td>
                <td style="width:50%; padding-right:20px; vertical-align:top;">
                    <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
                        <img src="{baseUrl}/img/comment.png" /> Commentaire
                    </h3>
                    <i style="margin-top:50px;">{comment}</i>
                </td>
            </tr>
        </table>
    </div>
    <div style ="' . $this->getContentWhiteStyle() . '">
        <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">
            Ta disponibilité
        </h3>
        <div style="margin-left:50px; margin-top:20px;text-align:center;">
            <a href="{baseUrl}/guest/response/{eventId}/{ok}" style="margin-right:50px;"><img src="{baseUrl}/img/white-ok.png" alt="présent"/></a>
            <a href="{baseUrl}/guest/response/{eventId}/{no}"><img src="{baseUrl}/img/white-no.png" style="margin-right:50px;" alt="Absent"/></a>
            <a href="{baseUrl}/guest/response/{eventId}/{perhaps}"><img src="{baseUrl}/img/white-incertain.png" alt="Incertain"/></a>
        </div>
        <a href="{baseUrl}/event/detail/{eventId}" style="' . $this->getButtonStyle() . '">Voir les détails</a>
    </div>
</div>
                ';
                break;

            case self::TEMPLATE_GROUP:
                $content = '
<div style="background: #eeeeee; font-family: HelveticaNeue-Thin; font-size: 18px; font-weight: 100; padding-bottom:50px;">
    <h2 style="margin-top:50px; display:block; width:680px; font-weight: 500; font-family: HelveticaNeue-Thin; margin:auto; font-weight: 70; margin-top: 40px; margin-bottom: 40px; color: #428BCA;">Bonjour,</h2>
    <p style="display:block; width:680px; margin:auto; font-family: HelveticaNeue-Thin;">Tu as été invité à un nouvel évènement</p>
    <div style="background: #eee; width:680px; margin:50px auto; border: solid 1px #ddd; background-color: #fff; border-radius: 10px; padding-bottom: 50px; margin-bottom: 10px;">
        <h3 style="margin-left:50px;font-weight: 500; font-family: HelveticaNeue-Thin;">{owner},</h3>
        <div style="margin-left:50px; margin-top:20px;">
            <b>{user}</b> souhaite rejoindre le groupe <i>{group}</i>
        </div>
        <div style="margin-left:50px; margin-top:20px;">
            Accepter ?
            <a href="{baseUrl}/group/update/{groupId}/{userId}/{ok}"> OUI</a>
            <a href="{baseUrl}/group/update/{groupId}/{userId}/{no}"> NON</a>
        </div>
        <a href="{baseUrl}/group/detail/{groupId}" style="display:block; line-height:2em; margin:20px auto 0; font-size: 20px; height: 44px; border-radius: 10px; background-image: linear-gradient(to bottom,#5bc0de 0,#2aabd2 100%); background-repeat: repeat-x;
  border-color: #28a4c9;width:400px; color:#fff; text-align:center; text-decoration:none;">Voir les détails</a>
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
