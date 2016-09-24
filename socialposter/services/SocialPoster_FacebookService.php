<?php
namespace Craft;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\RequestErrorResponseException;
use Guzzle\Http\Exception\CurlException;

class SocialPoster_FacebookService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function getPayload($entry, $accessToken, $message, $picture, &$chosenProvider)
    {
        // Get the URL to be used for the post - may be overridden per-entry
        $url = (isset($chosenProvider['url']) && $chosenProvider['url']) ? $chosenProvider['url'] : '{url}';
        $url = craft()->templates->renderObjectTemplate($url, $entry);
        $url = craft()->config->parseEnvironmentString($url);
        $chosenProvider['url'] = $url; // update 'model'

        // Get the Title to be used for the post - may be overridden per-entry
        $title = (isset($chosenProvider['title']) && $chosenProvider['title']) ? $chosenProvider['title'] : '{title}';
        $title = craft()->templates->renderObjectTemplate($title, $entry);
        $title = craft()->config->parseEnvironmentString($title);
        $chosenProvider['title'] = $title; // update 'model'

        try {
            $provider = craft()->oauth->getProvider('facebook');
            $token = craft()->socialPoster_accounts->getToken('facebook');

            $client = new Client('https://graph.facebook.com');

            $endpoint = $this->_getEndpoint($chosenProvider);

            // We must set an endpoint to post to
            if (!$endpoint) {
                return array('success' => false, 'response' => array('endpoint' => 'Please select a Post Type.'));
            }

            // Depending on if we're posting to a Page or Group/Timeline, we check what sort of access token we need. User vs Page.
            // If we use a User Access Token and try to post to a page, it'll post as the user authorized for the app.
            // So ideally we want to make this post as the actual page admin. So we make a call to `me/accounts` to fetch a Page
            // Access Token, specifically for this page. If we can't get one - then fall back to using the User Access Token. Phew!
            if ($chosenProvider['endpoint'] == 'page') {
                $pageAccessToken = $this->_getPageAccessToken($client, $accessToken, $chosenProvider);

                // Override our User Access Token (default) with our Page Access Token
                if ($pageAccessToken) {
                    $accessToken = $pageAccessToken;
                }
            }
            
            $request = $client->post($endpoint, null, array(
                'access_token' => $accessToken,
                'message' => $message,
                'link' => $url,
                'name' => $title,
            ));

            $response = $request->send();
            $data = $response->json();

            SocialPosterPlugin::log('Facebook post: ' . print_r($data, true), LogLevel::Info);

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

    private function _getEndpoint($provider)
    {
        $endpoint = (isset($provider['endpoint']) && $provider['endpoint']) ? $provider['endpoint'] : null;
        $provider['endpoint'] = $endpoint; // update 'model'

        // Post to Facebook Group
        if ($endpoint == 'group' && isset($provider['groupId']) && $provider['groupId']) {
            return $provider['groupId'] . '/feed';
        }

        // Post to Facebook Page
        if ($endpoint == 'page' && isset($provider['pageId']) && $provider['pageId']) {
            return $provider['pageId'] . '/feed';
        }

        // Post to Facebook Profile
        if ($endpoint == 'me') {
            return $endpoint . '/feed';
        }
    }

    private function _getPageAccessToken($client, $accessToken, $provider)
    {
        $request = $client->get('me/accounts', null, array(
            'query' => array('access_token' => $accessToken),
        ));

        $response = $request->send();
        $data = $response->json();

        $pageAccessToken = '';

        if (isset($data['data'])) {
            foreach ($data['data'] as $value) {
                if ($value['id'] == $provider['pageId']) {
                    $pageAccessToken = $value['access_token'];
                }
            }
        }

        return $pageAccessToken;
    }

    private function _returnResponse($response, $e = null)
    {
        if ($e) {
            SocialPosterPlugin::log('Facebook post error: ' . print_r($response, true), LogLevel::Error);
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