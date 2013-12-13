var sc = (function() {
  'use strict';
  var initModule = function($container) {
    //sc.data.initModule();
    //sc.model.initModule();
    sc.shell.initModule($container);
  };
  return {initModule: initModule};
}());
