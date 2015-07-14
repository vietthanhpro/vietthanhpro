<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-baiviet" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-baiviet" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
            <li><a href="#tab-links" data-toggle="tab"><?php echo $tab_links; ?></a></li>
            <li><a href="#tab-image" data-toggle="tab"><?php echo $tab_image; ?></a></li>
            <li><a href="#tab-design" data-toggle="tab"><?php echo $tab_design; ?></a></li>
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
                      <input type="text" name="baiviet_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($baiviet_description[$language['language_id']]) ? $baiviet_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="baiviet_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>"><?php echo isset($baiviet_description[$language['language_id']]) ? $baiviet_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="baiviet_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($baiviet_description[$language['language_id']]) ? $baiviet_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="baiviet_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($baiviet_description[$language['language_id']]) ? $baiviet_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                    <div class="col-sm-10">
                      <textarea name="baiviet_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($baiviet_description[$language['language_id']]) ? $baiviet_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-tag"><span data-toggle="tooltip" title="<?php echo $help_tag; ?>"><?php echo $entry_tag; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="tag" value="" placeholder="<?php echo $entry_tag; ?>" id="input-tag" class="form-control" />
                  <div id="baiviet-tag" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($baiviet_tags as $baiviet_tag) { ?>
                    <div id="baiviet-tag<?php echo $baiviet_tag['tag_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $baiviet_tag['name']; ?>
                      <input type="hidden" name="baiviet_tag[]" value="<?php echo $baiviet_tag['tag_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-keyword"><span data-toggle="tooltip" title="<?php echo $help_keyword; ?>"><?php echo $entry_keyword; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" />
                  <?php if ($error_keyword) { ?>
                  <div class="text-danger"><?php echo $error_keyword; ?></div>
                  <?php } ?>               
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?></label>
                <div class="col-sm-10">
                  <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
              </div> 
              <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-accessby"><?php echo $entry_accessby; ?></label>
                    <div class="col-sm-10">
                      <select name="accessby_id" id="input-accessby" class="form-control">
                        <?php foreach ($accessbys as $accessby) { ?>
                        <?php if ($accessby['accessby_id'] == $accessby_id) { ?>
                        <option value="<?php echo $accessby['accessby_id']; ?>" selected="selected"><?php echo $accessby['accessby_description'][$config_language_id]['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $accessby['accessby_id']; ?>"><?php echo $accessby['accessby_description'][$config_language_id]['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-classname"><?php echo $entry_classname; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="classname" value="<?php echo $classname; ?>" placeholder="<?php echo $entry_classname; ?>" id="input-classname" class="form-control" />
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
            <div class="tab-pane" id="tab-links">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-danhmucbaiviet"><span data-toggle="tooltip" title="<?php echo $help_danhmucbaiviet; ?>"><?php echo $entry_danhmucbaiviet; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="danhmucbaiviet" value="" placeholder="<?php echo $entry_danhmucbaiviet; ?>" id="input-danhmucbaiviet" class="form-control" />
                  <div id="baiviet-danhmucbaiviet" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($baiviet_danhmucbaiviets as $baiviet_danhmucbaiviet) { ?>
                    <div id="baiviet-danhmucbaiviet<?php echo $baiviet_danhmucbaiviet['danhmucbaiviet_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $baiviet_danhmucbaiviet['name']; ?>
                      <input type="hidden" name="baiviet_danhmucbaiviet[]" value="<?php echo $baiviet_danhmucbaiviet['danhmucbaiviet_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <div class="checkbox">
                      <label>
                        <?php if (in_array(0, $baiviet_store)) { ?>
                        <input type="checkbox" name="baiviet_store[]" value="0" checked="checked" />
                        <?php echo $text_default; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="baiviet_store[]" value="0" />
                        <?php echo $text_default; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $baiviet_store)) { ?>
                        <input type="checkbox" name="baiviet_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="baiviet_store[]" value="<?php echo $store['store_id']; ?>" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-related"><span data-toggle="tooltip" title="<?php echo $help_related; ?>"><?php echo $entry_related; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="related" value="" placeholder="<?php echo $entry_related; ?>" id="input-related" class="form-control" />
                  <div id="baiviet-related" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($baiviet_relateds as $baiviet_related) { ?>
                    <div id="baiviet-related<?php echo $baiviet_related['baiviet_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $baiviet_related['name']; ?>
                      <input type="hidden" name="baiviet_related[]" value="<?php echo $baiviet_related['baiviet_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-image">
              <div class="table-responsive">
                <table id="images" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_image; ?></td>
                      <td class="text-right"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $image_row = 0; ?>
                    <?php foreach ($baiviet_images as $baiviet_image) { ?>
                    <tr id="image-row<?php echo $image_row; ?>">
                      <td class="text-left"><a href="" id="thumb-image<?php echo $image_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $baiviet_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="baiviet_image[<?php echo $image_row; ?>][image]" value="<?php echo $baiviet_image['image']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                      <td class="text-right"><input type="text" name="baiviet_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $baiviet_image['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $image_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"></td>
                      <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_image_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-design">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_store; ?></td>
                      <td class="text-left"><?php echo $entry_layout; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left"><?php echo $text_default; ?></td>
                      <td class="text-left"><select name="baiviet_layout[0]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($baiviet_layout[0]) && $baiviet_layout[0] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php foreach ($stores as $store) { ?>
                    <tr>
                      <td class="text-left"><?php echo $store['name']; ?></td>
                      <td class="text-left"><select name="baiviet_layout[<?php echo $store['store_id']; ?>]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($baiviet_layout[$store['store_id']]) && $baiviet_layout[$store['store_id']] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
$('#input-description<?php echo $language['language_id']; ?>').summernote({height: 300});
<?php } ?>
//--></script> 
  <script type="text/javascript"><!--
// Danhmucbaiviet
$('input[name=\'danhmucbaiviet\']').autocomplete({
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
		$('input[name=\'danhmucbaiviet\']').val('');
		
		$('#baiviet-danhmucbaiviet' + item['value']).remove();
		
		$('#baiviet-danhmucbaiviet').append('<div id="baiviet-danhmucbaiviet' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="baiviet_danhmucbaiviet[]" value="' + item['value'] + '" /></div>');	
	}
});

$('#baiviet-danhmucbaiviet').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Tag
$('input[name=\'tag\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/tag/autocomplete&token=<?php echo $token; ?>&tag_to_type=2&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['tag_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'tag\']').val('');
		
		$('#baiviet-tag' + item['value']).remove();
		
		$('#baiviet-tag').append('<div id="baiviet-tag' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="baiviet_tag[]" value="' + item['value'] + '" /></div>');	
	}
});

$('#baiviet-tag').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Related
$('input[name=\'related\']').autocomplete({
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
		$('input[name=\'related\']').val('');
		
		$('#baiviet-related' + item['value']).remove();
		
		$('#baiviet-related').append('<div id="baiviet-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="baiviet_related[]" value="' + item['value'] + '" /></div>');	
	}	
});

$('#baiviet-related').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
//--></script> 
  <script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage() {
	html  = '<tr id="image-row' + image_row + '">';
	html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="baiviet_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
	html += '  <td class="text-right"><input type="text" name="baiviet_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	
	$('#images tbody').append(html);
	
	image_row++;
}
//--></script> 
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
//--></script></div>
<?php echo $footer; ?> 