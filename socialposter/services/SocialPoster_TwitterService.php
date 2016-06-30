<?php
namespace Craft;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

class SocialPoster_TwitterService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function getPayload($entry, $accessToken, $message, $picture)
    {
        try {
            $provider = craft()->oauth->getProvider('twitter');
            $token = craft()->socialPoster_accounts->getToken('twitter');

            $client = new Client('https://api.twitter.com/1.1');
            
            $subscriber = $provider->getSubscriber($token);
            $client->addSubscriber($subscriber);

            $request = $client->post('statuses/update.json', null, array(
                'status' => $message . rand(),
            ));

            $response = $request->send();
            $data = $response->json();

            SocialPosterPlugin::log('Twitter post: ' . print_r($data, true), LogLevel::Info);

            $responseReturn = $this->_returnResponse($response);

            if (isset($data['id'])) {
                return array('success' => true, 'response' => $responseReturn);
            } else {
                return array('success' => false, 'response' => $responseReturn);
            }
        } catch (ClientErrorResponseException $e) {
            SocialPosterPlugin::log('Twitter post error: ' . print_r($e->getResponse(), true), LogLevel::Info);

            return array('success' => false, 'response' => $this->_returnResponse($e->getResponse()));
        }
    }



    // Private Methods
    // =========================================================================

    private function _returnResponse($response)
    {
        return array(
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
        );
    }
}