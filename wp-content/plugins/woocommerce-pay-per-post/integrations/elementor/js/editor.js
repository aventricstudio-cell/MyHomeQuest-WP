class WCPayPerPostBehavior extends Marionette.Behavior {

  initialize() {
    super.initialize();

    // After the DOM has loaded.
    _.defer(() => {
      const settings = this.view.model.get('settings');

      // Set up the required listeners on layout and overlay
      this.listenTo(settings, 'change:wc_pay_per_post_enable', this.onPayPerPostControlChange);
      this.listenTo(settings, 'change:wc_pay_per_post_page_view_restriction_enable', this.onPayPerPostControlChange);
      this.listenTo(settings, 'change:wc_pay_per_post_delay_restriction_enable', this.onPayPerPostControlChange);
      this.listenTo(settings, 'change:wc_pay_per_post_expire_restriction_enable', this.onPayPerPostControlChange);

      this.checkProtectedStyle(this.view.model);

    });

  }

  /**
   * Executed when Page View Restriction toggle is changed
   * @param {Object} model The backbone view model of the active element
   */
  onPayPerPostControlChange(model) {

    const settings = this.view.model.get('settings'),
      enabledSetting = model.attributes.wc_pay_per_post_enable,
      pageViewSetting = model.attributes.wc_pay_per_post_page_view_restriction_enable,
      expiryViewSetting = model.attributes.wc_pay_per_post_expire_restriction_enable,
      delayViewSetting = model.attributes.wc_pay_per_post_delay_restriction_enable;

    if(enabledSetting){
      if ('yes' === model.changed.wc_pay_per_post_enable) {
        console.log('PPP Enabled');
      }
    }

    if (pageViewSetting) {
      if ('yes' === model.changed.wc_pay_per_post_page_view_restriction_enable) {
        this.disableSetting(settings, 'wc_pay_per_post_expire_restriction_enable');
        this.disableSetting(settings, 'wc_pay_per_post_delay_restriction_enable');
      }
    }
    if (expiryViewSetting) {
      if ('yes' === model.changed.wc_pay_per_post_expire_restriction_enable) {
        this.disableSetting(settings, 'wc_pay_per_post_page_view_restriction_enable');
        this.disableSetting(settings, 'wc_pay_per_post_delay_restriction_enable');
      }
    }
    if (delayViewSetting) {
      if ('yes' === model.changed.wc_pay_per_post_delay_restriction_enable) {
        this.disableSetting(settings, 'wc_pay_per_post_page_view_restriction_enable');
        this.disableSetting(settings, 'wc_pay_per_post_expire_restriction_enable');
      }
    }

    this.checkProtectedStyle(this.view.model);
  }

  /**
   * Disabled the required switcher control
   * @param {Object} settings The settings for the element model
   * @param {string} control The switcher control for which to disable the switch
   */
  disableSetting(settings, control) {
    $e.run('document/elements/settings', {
      container: this.view.getContainer(),
      settings: {
        [control]: '', // setting the switcher value to empty string, i.e. false
      },
      options: {
        external: true,
      }
    });
  }

  /**
   * Set the protected style to sections and columns.
   * The counterpart solution for widgets is more efficient, but it doesn't apply to Stack_Controls
   *
   * @param {Object} model The backbone view model of the active element
   */
  checkProtectedStyle(model) {
    const elType          = model.get('elType');
    const protectedStyle  = '1px dashed red';

    if ('section' == elType || 'column' == elType || 'widget' == elType) {
      const settings = model.get('settings');
      const id = model.id;
      const wc_pay_per_post_enable = settings.get('wc_pay_per_post_enable');

      var iframe = document.getElementById('elementor-preview-iframe');
      var innerDoc = iframe.contentDocument || iframe.contentWindow.document;

      const iFrameCollection = innerDoc.getElementsByClassName('elementor-element-' + id);
      if (iFrameCollection.length > 0) {
        iFrameCollection[0].style.border = 'yes' == wc_pay_per_post_enable ? protectedStyle : '';
      }

      const navigatorCollection = document.getElementById('elementor-navigator__elements').querySelector('[data-id="' + id + '"]');
      navigatorCollection.style.border = 'yes' == wc_pay_per_post_enable ? protectedStyle : '';
    }
  }
}

/**
 * Hooks the class into every Elementor element
 */
elementor.hooks.addFilter('elements/base/behaviors', function (behaviors, BaseElementView) {

  // const elType = BaseElementView.options.model.get('elType');

  behaviors.WCPayPerPostBehavior = {
    behaviorClass: WCPayPerPostBehavior,
  };

  return behaviors;

});
