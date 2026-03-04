if (typeof Craft.Elasticsearch === typeof undefined) {
  Craft.Elasticsearch = {};
}

(function ($) {
  Craft.Elasticsearch.Settings = Garnish.Base.extend({
    $authContainer: null,
    $authType: null,

    $entriesTable: null,

    init: function(prefix) {
      this.prefix = prefix;

      this.$authContainer = $(`#${this.prefix}-elasticsearch-auth-type`);
      this.$authType = $(`#${this.prefix}-auth`);

      this.addListener(this.$authType, 'change', this.onChange);

      this.$sitesTable = $(`#${this.prefix}-elasticsearch-indexable-sites`);
      this.$toggleAllSites = this.$sitesTable.find(`#${this.prefix}-toggle-all-sites`);
      this.$toggleSite = this.$sitesTable.find(`input[type="checkbox"]:not('#${this.prefix}-toggle-all-sites')`);

      this.addListener(this.$toggleAllSites, 'change', this.onToggleAllSites);
      this.addListener(this.$toggleSite, 'change', this.onToggleSite);

      this.$entriesTable = $(`#${this.prefix}-elasticsearch-indexable-entries`);
      this.$toggleAllEntries = this.$entriesTable.find(`#${this.prefix}-toggle-all-entries`);
      this.$toggleEntryType = this.$entriesTable.find(`input[type="checkbox"]:not('#${this.prefix}-toggle-all-entries')`);

      this.addListener(this.$toggleAllEntries, 'change', this.onToggleAllEntries);
      this.addListener(this.$toggleEntryType, 'change', this.onToggleEntryType);
    },

    onChange: function() {
      value = this.$authType.val();

      Craft.sendActionRequest('GET', Craft.getActionUrl('elasticsearch/settings/fields-template-for-auth-type', {
          type: value,
        }))
        .then((response) => {
          this.$authContainer.html(response.data.settingsHtml);

          Craft.appendHeadHtml(response.data.headHtml);
          Craft.appendBodyHtml(response.data.bodyHtml);
        })
        .catch((reponse) => {
          alert('could not replace content');
        });
    },

    onToggleAllSites: function($event) {
      if($event.target.checked) {
        this.$sitesTable.find(`input[type="checkbox"]:not('#${this.prefix}-toggle-all-sites')`).prop('checked', true);
      } else {
        this.$sitesTable.find(`input[type="checkbox"]:not('#${this.prefix}-toggle-all-sites')`).prop('checked', false);
      }
    },

    onToggleSite: function($event) {
      if(! $event.target.checked) {
        this.$toggleAllSites.prop('checked', false);
      }
    },

    onToggleAllEntries: function($event) {
      if($event.target.checked) {
        this.$entriesTable.find(`input[type="checkbox"]:not('#${this.prefix}-toggle-all-entries')`).prop('checked', true);
      } else {
        this.$entriesTable.find(`input[type="checkbox"]:not('#${this.prefix}-toggle-all-entries')`).prop('checked', false);
      }
    },

    onToggleEntryType: function($event) {
      if(! $event.target.checked) {
        this.$toggleAllEntries.prop('checked', false);
      }
    }
  });
})(jQuery);
