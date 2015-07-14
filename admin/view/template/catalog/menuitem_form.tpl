<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-menuitem" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-menuitem" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="menuitem_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($menuitem_description[$language['language_id']]) ? $menuitem_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="menuitem_description[<?php echo $language['language_id']; ?>][description]" value="<?php echo isset($menuitem_description[$language['language_id']]) ? $menuitem_description[$language['language_id']]['description'] : ''; ?>" placeholder="<?php echo $entry_description; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-html<?php echo $language['language_id']; ?>"><?php echo $entry_html; ?></label>
                    <div class="col-sm-10">
                      <textarea name="menuitem_description[<?php echo $language['language_id']; ?>][html]" placeholder="<?php echo $entry_html; ?>" id="input-html<?php echo $language['language_id']; ?>"><?php echo isset($menuitem_description[$language['language_id']]) ? $menuitem_description[$language['language_id']]['html'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-showtitle"><?php echo $entry_showtitle; ?></label>
                <div class="col-sm-10">
                  <select name="showtitle" id="input-showtitle" class="form-control">
                    <?php if ($showtitle) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-showdescription"><?php echo $entry_showdescription; ?></label>
                <div class="col-sm-10">
                  <select name="showdescription" id="input-showdescription" class="form-control">
                    <?php if ($showdescription) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-parent"><?php echo $entry_parent; ?></label>
                <div class="col-sm-10">
                  <select name="parent_id" id="input-parent" class="form-control">
                    <option value="*"></option>
                    <?php foreach ($menuparents as $menuitem_info) { ?>
                    <?php if ($menuitem_info['menuitem_id'] == $parent_id) { ?>
                    <option value="<?php echo $menuitem_info['menuitem_id']; ?>" selected="selected"><?php echo $menuitem_info['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $menuitem_info['menuitem_id']; ?>"><?php echo $menuitem_info['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <div class="checkbox">
                      <label>
                        <?php if (in_array(0, $menuitem_store)) { ?>
                        <input type="checkbox" name="menuitem_store[]" value="0" checked="checked" />
                        <?php echo $text_default; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="menuitem_store[]" value="0" />
                        <?php echo $text_default; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $menuitem_store)) { ?>
                        <input type="checkbox" name="menuitem_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="menuitem_store[]" value="<?php echo $store['store_id']; ?>" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-group"><?php echo $entry_group; ?></label>
                <div class="col-sm-10">
                  <select name="id_group" id="input-group" class="form-control">
                    <?php if ($id_group) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-columns"><span data-toggle="tooltip" title="<?php echo $help_column; ?>"><?php echo $entry_columns; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="columns" value="<?php echo $columns; ?>" placeholder="<?php echo $entry_column; ?>" id="input-columns" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-itemwidth"><?php echo $entry_itemwidth; ?></label>
                <div class="col-sm-10">
                  <select name="itemwidth" id="input-itemwidth" class="form-control">
                    <?php foreach ($itemwidths as $itemwidth_info) { ?>
                    <?php if ($itemwidth_info['itemwidth_id'] == $itemwidth) { ?>
                    <option value="<?php echo $itemwidth_info['itemwidth_id']; ?>" selected="selected"><?php echo $itemwidth_info['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $itemwidth_info['itemwidth_id']; ?>"><?php echo $itemwidth_info['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-type"><?php echo $entry_type; ?></label>
                <div class="col-sm-10">
                  <select name="type" id="input-type" class="form-control">
                    <?php foreach ($menutypes as $menutype_info) { ?>
                    <?php if ($menutype_info['type_id'] == $type) { ?>
                    <option value="<?php echo $menutype_info['typename']; ?>" selected="selected"><?php echo $menutype_info['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $menutype_info['typename']; ?>"><?php echo $menutype_info['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-manufacturer">
                <label class="col-sm-2 control-label" for="input-manufacturer"><?php echo $entry_manufacturer; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="megamenu-manufacturer" value="<?php echo $iteminfo_detail ?>" placeholder="<?php echo $entry_manufacturer; ?>" id="input-manufacturer" class="form-control" />
                  <input type="hidden" name="manufacturer_id" value="<?php echo $iteminfo; ?>" />
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-category">
                <label class="col-sm-2 control-label" for="input-category"><?php echo $entry_category; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="megamenu-path" value="<?php echo $iteminfo_detail; ?>" placeholder="<?php echo $entry_parent; ?>" id="input-parent" class="form-control" />
                  <input type="hidden" name="category_id" value="<?php echo $iteminfo; ?>" />
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-product">
                <label class="col-sm-2 control-label" for="input-product"><?php echo $entry_product; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="megamenu-product" value="<?php echo $iteminfo_detail; ?>" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" />
                  <input type="hidden" name="product_id" value="<?php echo $iteminfo; ?>" />
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-danhmucbaiviet">
                <label class="col-sm-2 control-label" for="input-danhmucbaiviet"><?php echo $entry_danhmucbaiviet; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="megamenu-danhmucbaiviet" value="<?php echo $iteminfo_detail; ?>" placeholder="<?php echo $entry_danhmucbaiviet; ?>" id="input-danhmucbaiviet" class="form-control" />
                  <input type="hidden" name="danhmucbaiviet_id" value="<?php echo $iteminfo; ?>" />
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-baiviet">
                <label class="col-sm-2 control-label" for="input-baiviet"><?php echo $entry_baiviet; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="megamenu-baiviet" value="<?php echo $iteminfo_detail; ?>" placeholder="<?php echo $entry_baiviet; ?>" id="input-baiviet" class="form-control" />
                  <input type="hidden" name="baiviet_id" value="<?php echo $iteminfo; ?>" />
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-information">
                <label class="col-sm-2 control-label" for="input-information"><?php echo $entry_information; ?></label>
                <div class="col-sm-10">
                  <select name="information_id" id="input-information" class="form-control">
                    <?php foreach ($informations as $information_info) { ?>
                    <?php if ($information_info['information_id'] == $iteminfo) { ?>
                    <option value="<?php echo $information_info['information_id']; ?>" selected="selected"><?php echo $information_info['information_description'][$config_language_id]['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $information_info['information_id']; ?>"><?php echo $information_info['information_description'][$config_language_id]['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-urlcommon">
                <label class="col-sm-2 control-label" for="input-urlcommon"><?php echo $entry_urlcommon; ?></label>
                <div class="col-sm-10">
                  <select name="urlcommon" id="input-urlcommon" class="form-control">
                    <?php foreach ($lienketlinks as $lienketlink_info) { ?>
                    <?php if ($lienketlink_info['link'] == $iteminfo) { ?>
                    <option value="<?php echo $lienketlink_info['link']; ?>" selected="selected"><?php echo $lienketlink_info['lienketlink_class_description'][$config_language_id]['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $lienketlink_info['link']; ?>"><?php echo $lienketlink_info['lienketlink_class_description'][$config_language_id]['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group megamenutype" id="megamenutype-url">
                <label class="col-sm-2 control-label" for="input-url"><?php echo $entry_url; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="urllink" value="<?php echo $iteminfo; ?>" placeholder="<?php echo $entry_url; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="status" id="input-status" class="form-control">
                    <?php if ($status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>" />
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
$('#input-html<?php echo $language['language_id']; ?>').summernote({
	height: 300
});
<?php } ?>
//--></script> 
  <script type="text/javascript"><!--
  $(".megamenutype").hide();
$("#megamenutype-"+ $("#input-type").val()).show();
$("#input-type").change( function(){
	$(".megamenutype").hide();
	$("#megamenutype-"+$(this).val()).show();
} );

$('input[name=\'megamenu-manufacturer\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['manufacturer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'megamenu-manufacturer\']').val(item['label']);
		$('input[name=\'manufacturer_id\']').val(item['value']);
	}
});

$('input[name=\'megamenu-product\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'megamenu-product\']').val(item['label']);
		$('input[name=\'product_id\']').val(item['value']);
	}
});

$('input[name=\'megamenu-path\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'megamenu-path\']').val(item['label']);
		$('input[name=\'category_id\']').val(item['value']);
	}
});

$('input[name=\'megamenu-danhmucbaiviet\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/danhmucbaiviet/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['danhmucbaiviet_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'megamenu-danhmucbaiviet\']').val(item['label']);
		$('input[name=\'danhmucbaiviet_id\']').val(item['value']);
	}
});

$('input[name=\'megamenu-baiviet\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/baiviet/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['baiviet_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'megamenu-baiviet\']').val(item['label']);
		$('input[name=\'baiviet_id\']').val(item['value']);
	}
});
//--></script>
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
//--></script></div>
<?php echo $footer; ?>