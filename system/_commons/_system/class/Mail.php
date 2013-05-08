<?php
    namespace backend;

    use vendor\PHPMailer\PHPMailer;

    /**
     *
     *
     */
    class Mail{
        private $mail = "";

        /**
         *
         * @global type $_M_CONFIG
         */
        public function __construct(){
            global $_M_CONFIG;

            if( !extension_loaded('openssl') && $_M_CONFIG->smtp['SMTPSecure'] == "ssl" ){
                trigger_error("Openssl não esta instalado/ativado neste servidor.", E_USER_ERROR);
                exit;
            }

            $this->mail = new PHPMailer();
            $this->mail->IsSMTP();
            $this->mail->Host       =  $_M_CONFIG->smtp['host'];      // SMTP server
            $this->mail->SMTPDebug  = 1;                              // enables SMTP debug information (for testing)
            $this->mail->SMTPAuth   = true;                           // enable SMTP authentication
            $this->mail->Port       = $_M_CONFIG->smtp['port'];       // set the SMTP port for the GMAIL server
            $this->mail->Username   = $_M_CONFIG->smtp['username'];   // SMTP account username
            $this->mail->Password   = $_M_CONFIG->smtp['password'];   // SMTP account password
            $this->mail->SMTPSecure = $_M_CONFIG->smtp['SMTPSecure']; // SMTP secure type
            $this->mail->SetFrom($_M_CONFIG->smtp['default-from-email'], utf8_decode($_M_CONFIG->smtp['default-from-name']));
        }

        /**
         *
         * @param type $email
         * @param type $name
         *
         * @return \backend\Mail
         */
        public function setFrom($email, $name){
            $this->mail->SetFrom($email, utf8_decode($name));
            return $this;
        }

        /**
         *
         * @param type $email
         * @param type $name
         *
         * @return \backend\Mail
         */
        public function AddReplyTo($email, $name){
            $this->mail->AddReplyTo($email, utf8_decode($name));
            return $this;
        }

        /**
         *
         * @param type $email
         * @param type $name
         *
         * @return \backend\Mail
         */
        public function addMail($email, $name){
            $this->mail->AddAddress($email, utf8_decode($name));
            return $this;
        }

        /**
         *
         * @param type $email
         * @param type $name
         *
         * @return \backend\Mail
         */
        public function addCC($email, $name){
            $this->mail->AddBCC($email, utf8_decode($name));
            return $this;
        }

        /**
         *
         * @param type $title
         * @param type $mensage
         *
         * @return type
         */
        public function send($title, $mensage){
            $this->mail->Subject = utf8_decode($title);
            $this->mail->MsgHTML(utf8_decode($mensage));
            if(!$this->mail->Send()) {
                return Array(
                    "error"     => true,
                    "errorCode" => "mailError",
                    "message"   => $this->mail->ErrorInfo
                );
            } else {
                return Array(
                    "error"     => false,
                    "message"   => "Email enviado com sucesso."
                );
            }
        }
    }