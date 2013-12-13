sc.shell = (function() {
  'use strict';

  // declarations
  var configMap = {
      /*anchor_schema_map: {
       header: true,
       footer: {show: true, hide: true}
       },*/
      main_html: String()
        + '<div id="shell-header"></div>'
        + '<div id="shell-board"></div>'
        + '<div id="shell-footer"></div>'
    }, stateMap = {
      $container: undefined,
      anchor_map: {}
    }, jqueryMap = {},
    setJqueryMap, onHashChange, initModule, storeAnchorMap;

  // utils methods
  storeAnchorMap = function() {
    return $.extend(true, {}, stateMap.anchor_map);
  };

  setJqueryMap = function() {
    var $container = stateMap.$container;
    jqueryMap = {
      $container: $container,
      $header: $container.find('#shell-header'),
      $board: $container.find('#shell-board'),
      $footer: $container.find('#shell-footer')
    }
  };

  // handlers
  onHashChange = function() {
    var isok = true, map_previous = storeAnchorMap(), changes = $.uriHandler.getUriMapChanges(map_previous);
    try {
      for(var change in changes) {

        /*console.log(changes[change]);
        if(changes[change].toString().search('{') > -1) {
          console.log("entra");
          changes[change] = $.parseJSON("'{" +changes[change] +"}'");
        }*/
        sc[change].handler(changes[change]);
      }
    } catch(error) {
      console.log(error);
      $.uriHandler.setUri(map_previous);
      isok = false;
    }
    stateMap.anchor_map = isok ? $.uriHandler.getCurrentUriMap() : map_previous;
    return false;
  };

  // dom methods

  // public functions
  initModule = function($container) {
    stateMap.$container = $container;
    $container.html(configMap.main_html);
    setJqueryMap();

    // bind to events
    $(window).bind('hashchange', onHashChange).trigger('hashchange');
  };
  return {initModule: initModule};
}());