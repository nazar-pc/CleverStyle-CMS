// Generated by LiveScript 1.4.0
/**
 * @package   WebSockets
 * @category  modules
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015-2016, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
(function(){
  var socket, handlers, messages_pool, allow_reconnect, socket_active, w;
  socket = null;
  handlers = {};
  messages_pool = [];
  allow_reconnect = true;
  socket_active = function(){
    var ref$;
    return socket && ((ref$ = socket.readyState) !== WebSocket.CLOSING && ref$ !== WebSocket.CLOSED);
  };
  (function(){
    var delay, onopen, onmessage, connect, keep_connection;
    delay = 0;
    onopen = function(){
      delay = 1000;
      cs.WebSockets.send('Client/authentication');
      while (messages_pool.length) {
        cs.WebSockets.send(messages_pool.shift());
      }
    };
    onmessage = function(message){
      var ref$, action, details, type, allow_reconnect, action_handlers;
      ref$ = JSON.parse(message.data), action = ref$[0], details = ref$[1];
      ref$ = action.split(':'), action = ref$[0], type = ref$[1];
      switch (action) {
      case 'Server/close':
        allow_reconnect = false;
      }
      action_handlers = handlers[action];
      if (!action_handlers || !action_handlers.length) {
        return;
      }
      if ((ref$ = typeof details) === 'boolean' || ref$ === 'number' || ref$ === 'string') {
        details = [details];
      }
      action_handlers.forEach(function(h){
        if (type === 'error') {
          h[1] && h[1].apply(h[1], details);
        } else {
          h[0] && h[0].apply(h[0], details);
        }
      });
    };
    connect = function(){
      var socket;
      socket = new WebSocket((location.protocol === 'https:' ? 'wss' : 'ws') + ("://" + location.host + "/WebSockets"));
      socket.onopen = onopen;
      socket.onmessage = onmessage;
    };
    keep_connection = function(){
      setTimeout(function(){
        if (!allow_reconnect) {
          return;
        }
        if (!socket_active()) {
          delay = (delay || 1000) * 2;
          connect();
        }
        keep_connection();
      }, delay);
    };
    keep_connection();
  })();
  w = {
    'on': function(action, callback, error){
      if (!action || (!callback && !error)) {
        return w;
      }
      if (!handlers[action]) {
        handlers[action] = [];
      }
      handlers[action].push([callback, error]);
      return w;
    },
    'off': function(action, callback, error){
      if (!handlers[action]) {
        return w;
      }
      handlers[action] = handlers[action].filter(function(h){
        if (h[0] === callback) {
          delete h[0];
        }
        if (h[1] === error) {
          delete h[1];
        }
        return h[0] || h[1];
      });
      return w;
    },
    once: function(action, callback, error){
      var callback_, error_;
      if (!action || (!callback && !error)) {
        return w;
      }
      callback_ = function(){
        w.off(action, callback_, error_);
        return callback.apply(callback, arguments);
      };
      error_ = function(){
        w.off(action, callback_, error_);
        return error.apply(error, arguments);
      };
      return w.on(action, callback_, error_);
    },
    send: function(action, details){
      var message;
      if (!action) {
        return w;
      }
      message = JSON.stringify([action, details]);
      if (!socket_active()) {
        messages_pool.push(message);
      } else {
        socket.send(message);
      }
      return w;
    }
  };
  cs.WebSockets = w;
}).call(this);