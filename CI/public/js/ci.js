var ci = (function() {
  'use strict';
  var initModule = function($container) {
    //ci.data.initModule();
    //ci.model.initModule();
    ci.shell.initModule($container);
  };
  return {initModule: initModule};
}());
