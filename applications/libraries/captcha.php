<?php

/*
 * Core class of IkoBB
 */
if (!defined('BASEPATH')) {
    die();
}

class Iko_Captcha
{

    public $error;
    private $captcha_type = 1;
    private $key;

    public function __construct()
    {
        global $IKO;
        if ($IKO->data['captcha_type'] == "2") {
            $this->captcha_type = 2;
            $this->key = array(
                'public' => $IKO->data['recaptcha_public_key'],
                'private' => $IKO->data['recaptcha_private_key']
            );
        }
    }

    /*
     * @Default Captcha
     * - Returns the text input with the captcha's image tag.
     * @reCaptcha
     * - Returns reCaptcha API call.
     */
    public function display()
    {
        global $LANG;
        if ($this->captcha_type == "1") {
            return '<img src="' . SITE_URL . '/public/img/captcha.php" alt="IkoBB Captcha" /><br /><input type="text" id="tangobb_captcha" name="tangobb_captcha" />';
        } else {
            return '<div class="g-recaptcha" data-sitekey="' . $this->key['public'] . '"></div>';
        }
    }

    /*
     * Verify if the input is the same as the captcha.
     */
    public function verify()
    {
        global $LANG;
        if ($this->captcha_type == "1") {
            $input = md5($_POST['tangobb_captcha']);
            if ($input !== $_SESSION['IkoBB_Captcha']) {
                throw new Exception ($LANG['global_form_process']['captcha_incorrect']);
            } else {
                return true;
            }
        } else {
            $recaptcha = $_POST['g-recaptcha-response'];
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $this->key['private'] . "&response=" . $recaptcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
            if ($response . success == true) {
                return true;
            } else {
                throw new Exception ($LANG['global_form_process']['captcha_incorrect']);
            }
        }
    }

}

?>