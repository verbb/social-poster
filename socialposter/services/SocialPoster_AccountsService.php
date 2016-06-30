<?php
namespace Craft;

class SocialPoster_AccountsService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function getAll($indexBy = null)
    {
        $records = SocialPoster_AccountRecord::model()->findAll();
        return SocialPoster_AccountModel::populateModels($records, $indexBy);
    }

    public function getById($id)
    {
        $record = SocialPoster_AccountRecord::model()->findById($id);

        if ($record) {
            return SocialPoster_AccountModel::populateModel($record);
        }
    }

    public function getByHandle($handle)
    {
        $record = SocialPoster_AccountRecord::model()->findByAttributes(array('handle' => $handle));

        if ($record) {
            return SocialPoster_AccountModel::populateModel($record);
        }
    }

    public function getToken($provider)
    {
        $tokenModel = $this->getByHandle($provider);

        if ($tokenModel) {
            return craft()->oauth->getTokenById($tokenModel->tokenId);
        }
    }

    public function save(SocialPoster_AccountModel $model)
    {
        if ($model->id) {
            $record = SocialPoster_AccountRecord::model()->findById($model->id);
        } else {
            $record = new SocialPoster_AccountRecord();
        }

        $record->setAttributes($model->getAttributes(), false);

        $record->validate();
        $model->addErrors($record->getErrors());

        if ($model->hasErrors()) {
            return false;
        }

        $record->save(false);

        if (!$model->id) {
            $model->id = $record->id;
        }

        return true;
    }

    public function saveToken($providerHandle, $token)
    {
        $account = $this->getByHandle($providerHandle);

        if (!$account) {
            $account = new SocialPoster_AccountModel;
        }

        $tokenExists = craft()->oauth->getTokenById($account->tokenId);

        if ($tokenExists) {
            $token->id = $account->tokenId;
        }

        $token->providerHandle = $providerHandle;
        $token->pluginHandle = 'socialPoster';

        craft()->oauth->saveToken($token);

        $account->handle = $providerHandle;
        $account->tokenId = $token->id;

        return $this->save($account);
    }

    public function deleteById($id)
    {
        $account = $this->getById($id);

        if ($account->tokenId) {
            $token = craft()->oauth->getTokenById($account->tokenId);

            if ($token) {
                craft()->oauth->deleteToken($token);
            }
        }

        return SocialPoster_AccountRecord::model()->deleteByPk($id);
    }


    // Providers
    // =========================================================================

    public function getAccountProvider($handle)
    {
        $providers = $this->getAccountProviders();

        if (isset($providers[$handle])) {
            return $providers[$handle];
        }
    }

    public function getAccountProviders($enabledOnly = true)
    {
        $providers = array();

        $oauthProviders = craft()->oauth->getProviders($enabledOnly);

        foreach ($oauthProviders as $oauthProvider) {
            $provider = new SocialPoster_AccountProviderModel;
            $provider->oauthProviderHandle = $oauthProvider->getHandle();

            $providers[$provider->oauthProviderHandle] = $provider;
        }

        return $providers;
    }

}
