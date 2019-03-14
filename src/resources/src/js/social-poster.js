// ==========================================================================

// Social Poster Plugin for Craft CMS
// Author: Verbb - https://verbb.io/

// ==========================================================================

if (typeof Craft.SocialPoster === typeof undefined) {
    Craft.SocialPoster = {};
}

(function($) {

Craft.SocialPoster = Garnish.Base.extend({
    init: function() {
        this.initTabs();
    },
    initTabs: function() {
        this.$selectedTab = null;

        var $tabs = $('#sp-tabs').find('> ul > li');
        var tabs = [];
        var tabWidths = [];
        var totalWidth = 0;
        var i, a, href;

        for (i = 0; i < $tabs.length; i++) {
            tabs[i] = $($tabs[i]);
            tabWidths[i] = tabs[i].width();
            totalWidth += tabWidths[i];

            // Does it link to an anchor?
            a = tabs[i].children('a');
            href = a.attr('href');
            if (href && href.charAt(0) === '#') {
                this.addListener(a, 'click', function(ev) {
                    ev.preventDefault();
                    this.selectTab(ev.currentTarget);
                });

                if (encodeURIComponent(href.substr(1)) === document.location.hash.substr(1)) {
                    this.selectTab(a);
                }
            }

            if (!this.$selectedTab && a.hasClass('sel')) {
                this.$selectedTab = a;
            }
        }

        // Now set their max widths
        for (i = 0; i < $tabs.length; i++) {
            tabs[i].css('max-width', (100 * tabWidths[i] / totalWidth) + '%');
        }
    },

    selectTab: function(tab) {
        var $tab = $(tab);

        if (this.$selectedTab) {
            if (this.$selectedTab.get(0) === $tab.get(0)) {
                return;
            }
            this.deselectTab();
        }

        $tab.addClass('sel');
        var href = $tab.attr('href')
        $(href).removeClass('hidden');
        if (typeof history !== 'undefined') {
            history.replaceState(undefined, undefined, href);
        }
        Garnish.$win.trigger('resize');
        // Fixes Redactor fixed toolbars on previously hidden panes
        Garnish.$doc.trigger('scroll');
        this.$selectedTab = $tab;
    },

    deselectTab: function() {
        if (!this.$selectedTab) {
            return;
        }

        this.$selectedTab.removeClass('sel');
        if (this.$selectedTab.attr('href').charAt(0) === '#') {
            $(this.$selectedTab.attr('href')).addClass('hidden');
        }
        this.$selectedTab = null;
    },
});


})(jQuery);
