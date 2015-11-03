Ext.define('System.Component', {
  url: 'components/permission',
  method: 'POST',
  permissions: [],
  access: {},
  constructor: function(config) {
    var me = this;

    if (me.permissions.length > 0) {
      Ext.Ajax.request({
        async: false,
        url: me.url,
        method: me.method,
        jsonData: {
          permissions: me.permissions
        },
        success: function(response) {
          var result = Ext.decode(response.responseText);

          for (var type in me.permissions) {
            if (typeof result.permissions[me.permissions[type]] != 'undefined') {
              me.access[me.permissions[type]] = result.permissions[me.permissions[type]];
            }
          }
        },
        failure: function() {
          me.destruct.apply(me, arguments);
        }
      });
    }
    me.construct.apply(me, arguments);
  },
  construct: function() {

  },
  destruct: function() {

  }
});
