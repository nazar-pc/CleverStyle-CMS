// Generated by LiveScript 1.4.0
/**
 * @package   CleverStyle Framework
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2016, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
(function(){
  define('jquery', function(){
    return jQuery;
  });
  define('htm5sortable', function(){
    return sortable;
  });
  define('sprintf-js', function(){
    return {
      sprintf: sprintf,
      vsprintf: vsprintf
    };
  });
  requirejs.config({
    baseUrl: '/',
    paths: {
      jssha: 'includes/js/modules/jsSHA-2.1.0',
      autosize: 'includes/js/modules/autosize.min'
    }
  });
}).call(this);
