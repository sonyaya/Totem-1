<?php
    namespace backend;

    # USED NATIVE CLASSES
    use backend\Frontend;
    use backend\MySQL;
    use backend\Mail;

    # USED VENDORS CLASSES
    use vendor\Symfony\Component\Yaml\Yaml;

    class User{
        /**
         *
         */
        public static function check($context="backend", $identifier="", $returnType="html"){
            global $_M_CONF;
            global $_M_THIS_CONFIG;

            if( !empty($_SESSION['user']) ){
                if( isset($_SESSION['user']['permissions'][$context]) ){
                    $array_permission = $_SESSION['user']['permissions'][$context];
                    if( isset($array_permission[$identifier]) && $array_permission[$identifier]===true ){
                        return true;
                    }else{
                        $message = "Você não possui permissão para executar '$identifier' no contexto '$context'.";

                        $return = Array(
                            "error"     => true,
                            "errorCode" => "check-2",
                            "message"   => $message
                        );

                        switch ($returnType) {
                            case "print_r":
                                print_r($return);
                                break;

                            case "json":
                                echo json_encode($return);
                                break;

                            case 'html':
                            default:
                                echo new Frontend(
                                    $_M_THIS_CONFIG['template']."login-blocked.html",
                                    array_merge(
                                        $_M_THIS_CONFIG,
                                        Array( "error" => $message )
                                    )
                                );
                                break;
                        }
                        exit;
                            
                    }
                }else{
                    $message = "Você não possui permissão definidas para o contexto $context, verifique ou contate se administrador.";

                    $return = Array(
                        "error"     => true,
                        "errorCode" => "check-1",
                        "message"   => $message
                    );

                    switch ($returnType) {
                        case "print_r":
                            print_r($return);
                            break;
                            
                        case "json":
                            echo json_encode($return);
                            break;

                        case 'html':
                        default:
                            echo new Frontend(
                                $_M_THIS_CONFIG['template']."login-blocked.html",
                                array_merge(
                                    $_M_THIS_CONFIG,
                                    Array( "error" => $message )
                                )
                            );
                            break;
                    }
                    exit;
                }
            }else{
                echo new Frontend(
                    $_M_THIS_CONFIG['template']."login.html",
                    $_M_THIS_CONFIG
                );
                exit;
            }
        }

        /**
         *
         */
        public static function login($login, $password){
            global $_M_CONFIG;

            //
            $password = User::generatePasswordHash($password);

            //
            $db = new MySQL();
            $select = 
                $db
                    ->setTable($_M_CONFIG->users['table-users'])
                    ->setRowsPerPage(1)
                    ->select(
                        Array(
                            "id",
                            "first_name",
                            "middle_name",
                            "last_name",
                            "login",
                            "permissions",
                            "group_id",
                            "group" => Array(
                                "group_id",
                                "{$_M_CONFIG->users['table-groups']}.id",
                                Array(
                                    "id",
                                    "name",
                                    "permissions"
                                )
                            )
                        ),
                        "
                            login = '$login' 
                            AND password = '$password'
                        ",
                        true, // paginator
                        true  // listar sem contador se retornar somente uma linha
                    )
                ;

            //
            if(empty($select)){
                $_SESSION['user'] = null;
                return Array(
                    "error"     => true,
                    "errorCode" => "login",
                    "message"   => "Usuário e/ou senha não encontrados."
                );
            }else{    
                $gPermissions = Yaml::parse( $select['permissions'] );
                $uPermissions = Yaml::parse( $select['group']['permissions'] );
                $select['permissions'] = array_replace_recursive($uPermissions, (array)$gPermissions);
                unset($select['group']['permissions']);

                $_SESSION['user'] = $select;
                 return Array(
                    "error"     => false,
                    "message"   => "Usuário logado com sucesso."
                );
            }
        }

        /**
         *
         */
        public static function recoverPassword($login){
            global $_M_CONFIG;
            global $_M_THIS_CONFIG;

            //
            $db = new MySQL();
            $db->setTable($_M_CONFIG->users['table-users']);
            $select = 
                $db
                    ->setRowsPerPage(1)
                    ->select(
                        Array( "id", "email", "first_name", "middle_name", "last_name", "login" ),
                        "login = '$login'",
                        true, // paginator
                        true  // listar sem contador se retornar somente uma linha
                    )
                ;

            //
            if(!empty($select)){
                $token = 
                    time() . 
                    $select['id'] . 
                    sha1(
                        $_M_CONFIG->system['salt'] . 
                        uniqid(rand(), true) . 
                        $_M_CONFIG->system['pepper']
                    )
                ;

                $db->save(
                    Array( "recovery_hash" => $token ),
                    " id={$select['id']} "
                );

                $mail = new Mail();
                $mail->addMail($select['email'], $select['first_name']);

                $emailHtml = new Frontend(
                    $_M_THIS_CONFIG['template']."recovery-password-email.html",
                    array_merge(
                        $_M_THIS_CONFIG,
                        Array( 
                            "user" => $select,
                            "recovery_hash" => $token,
                        )
                    )
                );

                return $mail->send("Recuperação de senha", $emailHtml);
            }else{
                return Array(
                    "error"     => true,
                    "errorCode" => "mailError-select",
                    "message"   => "O login digitado não foi encontrado em nossa base de dados."
                );
            }

        }

        /**
         *
         */
        public static function recoverPasswordChangePassword($recovery_hash, $password, $repeatPassword){
            if(strlen($password) >= 6){
                if($password == $repeatPassword){
                    global $_M_CONFIG;

                    //
                    $password = User::generatePasswordHash($password);

                    //
                    $db = new MySQL();
                    $db->setTable($_M_CONFIG->users['table-users']);
                    $db->save(
                            Array( 
                                "password" => $password,
                                "recovery_hash" => ''
                            ),
                            "recovery_hash = '$recovery_hash'"
                        )
                    ;

                    if( $db->affectedRows() >= 1){
                        return Array(
                            "error"     => false,
                            "message"   => "Senha alterada com sucesso."
                        );
                    }else{
                        return Array(
                            "error"     => true,
                            "errorCode" => "changePassord-3",
                            "message"   => "A sua senha já foi alterada ou o link para alteração que você recebeu por email já expirou."
                        );
                    }
                }else{
                    return Array(
                        "error"     => true,
                        "errorCode" => "changePassord-2",
                        "message"   => "As senhas informadas são diferentes."
                    );
                }
            }else{
                return Array(
                    "error"     => true,
                    "errorCode" => "changePassord-1",
                    "message"   => "A senha precisa ter no mínimo 6 caracteres."
                );
            }
        }

        /**
         *
         */
        public static function generatePasswordHash($password){
            global $_M_CONFIG;
            $password = $_M_CONFIG->system['salt'] . $password . $_M_CONFIG->system['pepper'];
            $password = md5 ( $password );
            $password = sha1( $password );
            return $password;
        }

        /**
         *
         */
        public static function logout(){
            $_SESSION['user'] = null;
        }
    }