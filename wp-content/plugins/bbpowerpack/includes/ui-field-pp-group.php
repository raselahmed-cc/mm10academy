<#

var field   = data.field;
var fields  = field.fields;

var fieldName = '';

if ( data.isMultiple ) {
	fieldName = data.name + '[' + data.index + ']';
} else {
	fieldName = data.name + '[]';
}
#>

<div class="pp-group-fields">
	<table class="fl-form-table">
	<#
	for ( var key in fields ) {
		var label         = fields[key]['label'],
			name          = fieldName + '[' + key + ']',
			field  	  = fields[key],
			defaultVal    = ( ( 'undefined' != typeof fields[key]['default'] ) ? fields[key]['default'] : '' ),
			connections   = ( ( 'undefined' != typeof fields[key]['connections'] ) ? true : false ),
			fieldTemplate = wp.template( 'fl-builder-field' )( {
				name: name,
				value: ( ( 'undefined' != typeof data.value[key] ) ? data.value[key] : defaultVal ),
				field: field,
				template: 'fl-builder-field-' + fields[key]['type'],
				dynamicOptions: {
					source: 'legacy',
					isDataSource: false
				}
			} );
		#>
		<tr id="fl-field-{{name}}" class="fl-field" data-key="{{key}}" data-type="{{field.type}}" data-is-style="{{field.is_style}}" data-preview='{{{data.preview}}}' data-connections="{{{connections}}}">
		{{{fieldTemplate}}}
		</tr>
		<#
	}
	#>
	</table>
</div>