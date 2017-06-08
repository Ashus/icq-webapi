<?php


namespace carono\components;

class Icq extends IcqCore
{
    const URL_CONNECT = 'https://icq.com/siteim/icqbar/php/proxy_jsonp_connect.php';
    const URL_SEND = 'https://api.icq.net/im/sendIM';
    const URL_MEMBERDIR_GET = 'https://api.icq.net/memberDir/get';

    public function login($login, $password)
    {
        $post = [
            'username' => $login,
            'password' => $password,
            'language' => 'ru',
            'time'     => time(),
            'remember' => 1,
        ];
        $res = $this->request('POST', self::URL_CONNECT, $post);
        $answer = json_decode($res->getBody(), true);
        $this->sessionKey = $answer['sessionKey'];
        $this->k = $answer['k'];
        $this->a = $answer['a'];
        $this->startSession();
    }

    public function send($uin, $message)
    {
        $data = [
            "aimsid"  => $this->aimsid,
            "c"       => $this->c,
            "f"       => $this->f,
            "message" => $message,
            "t"       => $uin
        ];
        $this->request('GET', self::URL_SEND, $data);
    }

	/**
	 * @param string $uin
	 * @return object
	 */
	public function getUserInfo($uin)
    {
        $data = [
            "aimsid"  => $this->aimsid,
            "r"       => '',
            "f"       => $this->f,
            "locale" => 'en-us',
            "infoLevel" => 'full',
            "t"       => $uin
        ];
        $response = $this->request('GET', self::URL_MEMBERDIR_GET, $data);
        $result = $response->getBody()->getContents();
        $result = @json_decode($result);
        return $result;
    }

	/**
	 * @param string $uin
	 * @return bool|null
	 */
	public function isUserOnline($uin)
	{
		$info = $this->getUserInfo($uin);
		if (!isset($info->response->data->infoArray[0]->profile))
			return null;

		$profile = $info->response->data->infoArray[0]->profile;
		$online = isset($profile->online) ? ($profile->online != 'false') : true;
		return $online;
	}
}