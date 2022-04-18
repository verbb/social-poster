# Front-end submission

As you can save or update existing entries from the front-end of your site, so can you post to social media. By default, Social Poster will use whatever defaults you have set up in your accounts. This means, if by default you have auto-posting set to `off`, posts won't be posted to social media.

However, you can override each field per-entry, just as you can through the control panel. You're just required to add this information into your form.

Take the below example (cut down) code for updating an existing entry.

```twig
<form method="post" accept-charset="UTF-8">
    <input type="hidden" name="action" value="entries/save-entry">
    <input type="hidden" name="entryId" value="{{ entry.id }}">
    {{ csrfInput() }}

    <label for="autoPost">Post to Facebook?</label>
    <input id="autoPost" type="checkbox" name="socialPoster[facebook][autoPost]" value="1" checked>
    
    <textarea name="socialPoster[facebook][message]">Check out this amazing new post!</textarea>

    <input type="submit" value="Publish">
</form>
```

Here, we've used a checkbox to allow the user to enable posting to social media. All other fields can be used, as you can see we've done with the message textarea.

Options vary from provider-to-provider, but are as below. Please note to update the first parameter to the handle used for your account. For example, where we have `socialPoster[facebook][autoPost]`, ensure that the handle for your Facebook account is indeed `facebook`, otherwise, change this to match the account handle.

## Facebook

```twig
<input name="socialPoster[facebook][autoPost]">
<input name="socialPoster[facebook][endpoint]">
<input name="socialPoster[facebook][title]">
<input name="socialPoster[facebook][url]">
<input name="socialPoster[facebook][message]">
<input name="socialPoster[facebook][imageField]">
```

## Twitter

```twig
<input name="socialPoster[twitter][autoPost]">
<input name="socialPoster[twitter][message]">
```

## Linked.in

```twig
<input name="socialPoster[linkedin][autoPost]">
<input name="socialPoster[linkedin][visibility]">
<input name="socialPoster[linkedin][title]">
<input name="socialPoster[linkedin][url]">
<input name="socialPoster[linkedin][message]">
<input name="socialPoster[linkedin][imageField]">
```