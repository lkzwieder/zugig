ci.shell = (function() {
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
    var isok = true, map_previous = storeAnchorMap(), changes = $.uriParser.getUriMapChanges(map_previous);
    try {
      for(var change in changes) {
        ci[change].handler(changes[change]);
      }
    } catch(error) {
      $.uriParser.setUri(map_previous);
      isok = false;
    }
    stateMap.anchor_map = isok ? $.uriParser.getCurrentUriMap() : map_previous;
    return false;
  };

  // dom methods

  // public functions
  initModule = function($container) {
    stateMap.$container = $container;
    $container.html(configMap.main_html);
    setJqueryMap();

    // set anchor_schema | allow or disallow some URI's
    /*$.uriAnchor.configModule({
     schema_map: configMap.anchor_schema_map
     });*/

    // bind to events
    $(window).bind('hashchange', onHashChange)
  };
  return {initModule: initModule};
}());