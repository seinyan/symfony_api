<?php
namespace App\Utils;

use App\Entity\UserAgentData;
use donatj\UserAgent\UserAgentParser;
use Jenssegers\Agent\Agent;

/**
 * Class UserAgentManger
 */
class UserAgentManger
{
    public function getUserAgentData():UserAgentData
    {
        $data = new UserAgentData();
        $data->setCreatedAt(new \DateTime());
        $data->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        $ua = (new UserAgentParser())->parse();
        $agent = new Agent();

//        dump('isDesktop: '.$agent->isDesktop());
//        dump('isMobile: '.$agent->isMobile());
//        dump('isTablet: '.$agent->isTablet());
//        dump('isPhone: '.$agent->isPhone());


        $data->setPlatform($ua->platform());
        $data->setBrowser($ua->browser());
        $data->setDevice($agent->device());
        $data->setIp($this->getIp());


        return $data;
    }


    public function getIp()
    {
        $ip = null;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

}