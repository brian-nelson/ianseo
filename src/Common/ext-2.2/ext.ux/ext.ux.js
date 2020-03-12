Ext.ns('Ext.ux');

Ext.ux.IconCombo=function(config)
{
// call parent constructor
    Ext.ux.IconCombo.superclass.constructor.call(this, config);
    
    this.tpl 
    	= config.tpl || '<tpl for="."><div style="height:10px;" class="x-combo-list-item x-icon-combo-item {' 
    	+ this.iconClsField 
    	+ '}">{' 
    	+ this.displayField 
    	+ '}</div></tpl>';
        
    this.on({
        render:{scope:this, fn:function() {
            var wrap = this.el.up('div.x-form-field-wrap');
            this.wrap.applyStyles({position:'relative'});
            this.el.addClass('x-icon-combo-input');
            this.flag = Ext.DomHelper.append(wrap, {
                tag: 'div', style:'position:absolute'
            });
        }}
    });
}

// extend
Ext.extend(Ext.ux.IconCombo, Ext.form.ComboBox, {
 
    setIconCls: function() {
        var rec = this.store.query(this.valueField, this.getValue()).itemAt(0);
        if(rec) {
            this.flag.className = 'x-icon-combo-icon ' + rec.get(this.iconClsField);
        }
    },
 
    setValue: function(value) {
        Ext.ux.IconCombo.superclass.setValue.call(this, value);
        this.setIconCls();
    }
 
}); // end of extend