<?php
  // IMPORTA CLASSES DA API
  require_once 'src/Google_Client.php';
  require_once 'src/contrib/Google_AnalyticsService.php';
  require_once  'src/io/Google_HttpRequest.php';

  // INICIA SESSÃO
  session_start();

  // CRIA NOVA APLICAÇÃO
  $client = new Google_Client();
  $client->setApplicationName("Google Analytics PHP Starter Application");

  // CONFIGIRA NOVA APLICAÇÃO
  $client->setClientId('475064885133.apps.googleusercontent.com');
  $client->setClientSecret('T9779nlIqvZGsmaNsA5rC3pD');
  $client->setRedirectUri('http://social.crmall.com.br/php/index.php');
  $client->setDeveloperKey('475064885133@developer.gserviceaccount.com');

  // CRIA NOVO SERVIÇO DO ANALYTICS
  $service = new Google_AnalyticsService($client);

  // APAGA O TOKEN CASO RECEBA LOGOUT NA URL
  if (isset($_GET['logout'])) {
    unset($_SESSION['token']);
  }

  //
  if (isset($_GET['code'])) {
    $client->authenticate();
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
  }

  // SETA VALOR DO TOKEN PARA A APLICAÇÃO CASO RECEBA O VALOR NA URL
  if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
  }

  // CASO ESTEJA LOGADO EXECUTA PESQUISA
  if ($client->getAccessToken()) {

    // http://ga-dev-tools.appspot.com/explorer/

    $web_property_id = "34418456"; # https://www.google.com/analytics/web/?hl=pt-BR&pli=1#report/visitors-overview/a17314531w35086254p34418456/   --- é o valor entre o p e a ultima / nesse caso 34418456
    $yesterday       = strtotime('-1 day');
    $two_weeks_ago   = strtotime('-2 weeks', $yesterday);
    
    $search_config = array(
      'ids'        => 'ga:'.$web_property_id,
      'start-date' => date('Y-m-d', $two_weeks_ago),
      'end-date'   => date('Y-m-d', $yesterday),
      'dimensions' => 'ga:year,ga:month,ga:day',
      'metrics'    => 'ga:pageviews'
    );

    $url = 'https://www.googleapis.com/analytics/v3/data/ga?'.http_build_query($search_config);
    $request = $client->getIo()->authenticatedRequest(new Google_HttpRequest($url));

    echo "<pre>";
    print_r( json_decode($request->getResponseBody(), true) );
    echo "</pre>";

  } else {

    $authUrl = $client->createAuthUrl();
    print "<a class='login' href='$authUrl'>Connect Me!</a>";
    
  }