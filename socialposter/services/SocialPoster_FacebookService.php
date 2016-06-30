<?php
namespace Craft;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

class SocialPoster_FacebookService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function getPayload($entry, $accessToken, $message, $picture)
    {
        try {
            $provider = craft()->oauth->getProvider('facebook');
            $token = craft()->socialPoster_accounts->getToken('facebook');

            $client = new Client('https://graph.facebook.com');
            
            $request = $client->post('me/feed', null, array(
                'access_token' => $accessToken,
                'message' => $message,
                'link' => $entry->url,
                'name' => $entry->title,
            ));

            $response = $request->send();
            $data = $response->json();

            SocialPosterPlugin::log('Facebook post: ' . print_r($data, true), LogLevel::Info);

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