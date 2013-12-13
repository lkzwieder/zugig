sc.test = (function() {
  'use strict';

  // declarations
  var configMap = {
      main_html: String()
        + '<div id="test-test"></div>'
    }, stateMap = {
      $container: undefined
    }, jqueryMap = {},
    setJqueryMap, handler, initModule, test;

  // utils methods
  setJqueryMap = function() {
    var $container = stateMap.$container;
    jqueryMap = {
      $container: stateMap.$container,
      $test: $container.find('#test-test')
    }
  };

  // handlers
  handler = function(arg) {
    initModule($("#shell-header"));
    console.log(typeof arg);
    return false;
  };

  // dom methods

  // public functions
  initModule = function($container) {
    stateMap.$container = $container;
    $container.html(configMap.main_html);
    setJqueryMap();

    // bind to events
    //$(window).bind('hashchange', onHashChange).trigger('hashchange');
  };
  return {handler:handler}
}());
