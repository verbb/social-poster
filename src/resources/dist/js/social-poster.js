void 0===Craft.SocialPoster&&(Craft.SocialPoster={}),function($){$(document).on("click","[data-refresh-settings]",(function(e){e.preventDefault();const r=$(this),t=r.parent().parent(),a=t.find("select"),o=undefined,n=undefined,s={account:r.data("account"),setting:r.data("refresh-settings")},d=function(e){let r=t.find(".sp-error");e||r.remove(),r.length||(r=$('<div class="sp-error error"></div>').appendTo(t)),r.html(e)},i=function(e){let r=a.val(),t="";$.each(e,((e,r)=>{t+='<option value="'+r.value+'">'+r.label+"</option>"})),a.html(t),r&&a.val(r)};r.addClass("sp-loading sp-loading-sm"),d(null),Craft.sendActionRequest("POST","social-poster/accounts/refresh-settings",{data:s}).then((e=>{if(e.data.error){let r=Craft.t("social-poster","An error occurred.");return e.data.error&&(r+=`<br><code>${e.data.error}</code>`),void d(r)}i(e.data)})).catch((e=>{let r=e;e.response&&e.response.data&&e.response.data.error&&(r+=`<br><code>${e.response.data.error}</code>`),d(r)})).finally((()=>{r.removeClass("sp-loading sp-loading-sm")}))}))}(jQuery);
//# sourceMappingURL=social-poster.js.map