# Accounts
To get started, you'll need to create an Account, which allows you to connect to a supported provider through their API, and allow you to configure default settings on your posts to be sent.

Each provider will have different settings, but almost all require an OAuth client. You'll need to follow the instructions to enter the Client ID and Client Secret for the provider you wish to use. Once these have been provided, you can connect to the provider to generate an access token. This token is then stored for later use when you want to actually post content.

:::tip
You can also set OAuth values via your [Configuration](docs:get-started/configuration).
:::

Next, you can configure defaults about the post that will be sent out. These are the default message, an Asset field to fetch an image for use in the post, the title and more. Each provider will support different fields.

You can also override any of these in the [posting widget](docs:feature-tour/entry-widget) by enabling the **Show in Widget** option for any of these fields.