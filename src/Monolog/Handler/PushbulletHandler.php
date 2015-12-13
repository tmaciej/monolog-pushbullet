<?php namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use GuzzleHttp\Client as GuzzleClient;

/**
 *
 */
class PushbulletHandler extends AbstractProcessingHandler
{
    protected $headers;

    protected $guzzle;

    protected $apiUrl = 'https://api.pushbullet.com/v2/';
    
    protected $deviceIden;

    public function __construct($accessToken = null, $level = Logger::DEBUG, $bubble = true, $deviceIden = null)
    {
        if ($accessToken === null) {
            throw new Exception('You need to specify Pushbullet access token.');
        }
        if ($deviceIden !== null) {
            $this->deviceIden = $deviceIden;
        }

        $this->guzzle = new GuzzleClient(['base_uri' => $this->apiUrl]);
        $this->headers = array(
            'Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        );

        parent::__construct($level, $bubble);
    }

    public function write(array $record)
    {
        if ($this->deviceIden !== null) {
            $json = array('type' => 'note', 'title' => $_SERVER['HTTP_HOST'], 'body' => $record['message'], 'device_iden' => $this->deviceIden);
        } else {
            $json = array('type' => 'note', 'title' => $_SERVER['HTTP_HOST'], 'body' => $record['message']);
        }
        
        $this->guzzle->post('pushes', array(
            'headers' => $this->headers,
            'json' => $json
        ));
    }
}
