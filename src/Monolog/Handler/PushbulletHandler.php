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

    protected $devices = null;

    protected $guzzle;

    protected $apiUrl = 'https://api.pushbullet.com/v2/';

    public function __construct($accessToken = null, $level = Logger::DEBUG, $bubble = true)
    {
        if ($accessToken === null) {
            throw new Exception('You need to specify Pushbullet access token.');
        }

        $this->guzzle = new GuzzleClient(['base_uri' => $this->apiUrl]);
        $this->headers = array(
            'Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        );

        parent::__construct($level, $buble);
    }

    public function write(array $record)
    {
        $this->guzzle->post('pushes', array(
            'headers' => $this->headers,
            'json' => array('type' => 'note', 'title' => $_SERVER['HTTP_HOST'], 'body' => $record['message'])
        ));
    }
}
