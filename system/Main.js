Ext.define('System.Main', {
  extend: 'Ext.Viewport',
  requires: [
    'System.Auth'
  ],
  constructor: function (config) {
    this.callParent();
    new System.Auth();
  },
  layout: 'border'
});
