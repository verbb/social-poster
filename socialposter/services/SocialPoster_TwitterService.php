<?php
namespace Craft;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\RequestErrorResponseException;
use Guzzle\Http\Exception\CurlException;

class SocialPoster_TwitterService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function getPayload($entry, $accessToken, $message, $picture, &$chosenProvider)
    {
        try {
            $provider = craft()->oauth->getProvider('twitter');
            $token = craft()->socialPoster_accounts->getToken('twitter');

            $client = new Client('https://api.twitter.com/1.1');
            
            $subscriber = $provider->getSubscriber($token);
            $client->addSubscriber($subscriber);

            $request = $client->post('statuses/update.json', null, array(
                'status' => $message,
            ));

            $response = $request->send();
            $data = $response->json();

            SocialPosterPlugin::log('Twitter post: ' . print_r($data, true), LogLevel::Info);

            $responseReturn = $this->_returnResponse($response);

            if (isset($data['id'])) {
                return array('success' => true, 'response' => $responseReturn, 'data' => $data);
            } else {
                return array('success' => false, 'response' => $responseReturn, 'data' => $data);
            }
        } catch (ClientErrorResponseException $e) {
            return array('success' => false, 'response' => $this->_returnResponse($e->getResponse(), $e));
        } catch (ServerErrorResponseException $e) {
            return array('success' => false, 'response' => $this->_returnResponse($e->getResponse(), $e));
        } catch (RequestErrorResponseException $e) {
            return array('success' => false, 'response' => $this->_returnResponse($e->getResponse(), $e));
        } catch (CurlException $e) {
            return array('success' => false, 'response' => $this->_returnResponse($e->getMessage(), $e));
        }
    }



    // Private Methods
    // =========================================================================

    private function _returnResponse($response, $e = null)
    {
        if ($e) {
            SocialPosterPlugin::log('Twitter post error: ' . print_r($response, true), LogLevel::Error);
        }

        if ($e instanceof CurlException) {
            return array(
                'statusCode' => '[curl]',
                'reasonPhrase' => $response,
            );
        } else {
            return array(
                'statusCode' => $response->getStatusCode(),
                'reasonPhrase' => $response->getReasonPhrase(),
            );
        }
    }
}