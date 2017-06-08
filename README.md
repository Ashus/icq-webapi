Crude component, development has only just begun

    $icq = new Icq();
    $icq->login($uin, $password);
    $icq->send($uinTo, 'message');
   	$userInfo = $icq->getUserInfo($uin);
    $isUserOnline = $icq->isUserOnline($uin);