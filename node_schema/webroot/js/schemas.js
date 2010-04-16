/**
 * Schemas
 *
 * for SchemasController
 */
var Schemas = {};

/**
 * methods to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Schemas.documentReady = function() {
    Schemas.addSchemaField();
    Schemas.removeSchemaField();
}

// SCHEMAS OBJECT CLASS METHODS //

/**
 * add field to the schema
 *
 * @return void
 */
Schemas.addSchemaField = function() {
    $('a.add-schema-field').click(function() {
        $.get(Croogo.basePath+'admin/node_schema/node_schemas/add_schema_field/', function(data) {
            $('#schema-field-fields div.clear').before(data);
            $('div.schema-field a.remove-schema-field').unbind();
            Admin.roundedCorners();
            Schemas.removeSchemaField();
        });
        return false;
    });
}

/**
 * remove field from the schema
 *
 * @return void
 */
Schemas.removeSchemaField = function() {
    $('div.schema-field a.remove-schema-field').click(function() {
        var aRemoveSchemaField = $(this);
        if (aRemoveSchemaField.attr('rel') != '') {
            $.getJSON(Croogo.basePath+'admin/node_schema/node_schemas/delete_field/'+$(this).attr('rel')+'.json', function(data) {
                if (data.success) {
                    aRemoveMeta.parents('.schema-field').remove();
                } else {
                    // error
                }
            });
        } else {
            aRemoveSchemaField.parents('.schema-field').remove();
        }
        return false;
    });
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
    if (Croogo.params.controller == 'node_schemas') {
        Schemas.documentReady();        
    }
});
