(function($, d) {
  // plugin
  $.uriHandler = (function() {
    'use strict';
    var setUri, makeUriString, getUri, getCurrentUriMap, getUriMapChanges;

    getUriMapChanges = function(lastUrimap) {
      var res = {}, currentUri = getCurrentUriMap();
      for(var urikey in currentUri) {
        if(!(urikey in lastUrimap && lastUrimap[urikey] === currentUri[urikey])) res[urikey] = currentUri[urikey];
      }
      for(var urikey in lastUrimap) {
        if(!(urikey in currentUri && currentUri[urikey] === lastUrimap[urikey])) res[urikey] = lastUrimap[urikey];
      }
      for(var key in res) {
        if(res[key] === "true") res[key] = true;
        if(res[key] === "false") res[key] = false;
      }
      return res;
    };

    getCurrentUriMap = function() {
      var uri = getUri().toString(), uri_pairs = uri.split('&'), uri_map = {};
      uri_pairs.forEach(function(v) {
        if(v) {
          if(v.search('=') === -1) {
            uri_map[v] = true;
          } else {
            var s = v.split('=');
            uri_map[s[0]] = s[1];
            if(uri_map[s[0]] === "false" || uri_map[s[0]] === "") delete uri_map[s[0]];
            if(uri_map[s[0]] === "true") uri_map[s[0]] = true;
          }
        }
      });
      return uri_map;
    };

    makeUriString = function(uri_map) {
      var hash = uri_map ? String() + '#!' : '';
      for(var prop in uri_map) {
        if(uri_map.hasOwnProperty(prop)) {
          if(uri_map[prop] === true || uri_map[prop] === false) { // faster than return typeof $var === 'boolean'
            if(uri_map[prop]) hash += prop + '&';
          } else {
            hash += prop + '=' + uri_map[prop] + '&';
          }
        }
      }
      return hash.slice(0, hash.length - 1);
    };

    setUri = function(uri_map) {
      d.location.href = makeUriString(uri_map);
    }

    getUri = function() {
      var res, hash = d.location.href.split('#!', 2)[1];
      res = hash === undefined ? "" : hash;
      return res;
    };

    return {
      getUriMapChanges: getUriMapChanges,
      getCurrentUriMap: getCurrentUriMap,
      setUri: setUri
    };
  })();
})(jQuery, document);

//TODO check if uri is divided by #! or by #
//TODO include HTML5 API for history and so on
//TODO validate URIs
