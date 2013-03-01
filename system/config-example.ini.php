;<?php exit(); ?>

[system]
  salt        = "your salt phrase, change this phrase only once before installing the system, and never touch it again!"
  pepper      = "your pepper phrase, change this phrase only once before installing the system, and never touch it again!"
  upload-path = "../uploads"
  time-zone   = "America/Sao_Paulo"

[smtp]
  host               = "smtp.gmail.com"
  username           = "example@gmail.com"
  password           = "example"
  port               = 465
  SMTPSecure         = "ssl"
  default-from-name  = "noreply"
  default-from-email = "noreply@noreply.com"

[mysql]
  host     = "127.0.0.1"
  port     = "3306"
  username = "root"
  password = "thepassword"
  database = "example"

[users]
  table-users  = "_m_user"
  table-groups = "_m_group"

[frontend]
  bridge-path = "../bridge/json"
  bridge-http = "http://127.0.0.1/totem/system/bridge"

[backend]
  template      = "templates/df1/"
  max-page-list = 10
  start-place   = "?action=view-dashboard&path=user/dashboards/dashboard"
  bootstrap     = '
    "userBootstrap" : "modules/user/extra/bootstrap.php"
  '

[console]
  template = "templates/default/"

[bridge]