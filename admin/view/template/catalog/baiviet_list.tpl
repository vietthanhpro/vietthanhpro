<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="submit" form="form-baiviet" formaction="<?php echo $copy; ?>" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-default"><i class="fa fa-copy"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-baiviet').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-danhmucbaiviet"><?php echo $entry_danhmucbaiviet; ?></label>
                <input type="text" name="filter_danhmucbaiviet" value="<?php echo $filter_danhmucbaiviet; ?>" placeholder="<?php echo $entry_danhmucbaiviet; ?>" id="input-danhmucbaiviet" class="form-control" />
                <input type="hidden" name="filter_danhmucbaiviet_id" value="<?php echo $filter_danhmucbaiviet_id; ?>">
              </div>
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_status && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-createby"><?php echo $entry_createby; ?></label>
                <select name="filter_createby" id="input-createby" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($users as $createby) { ?>
                  <?php if ($createby['user_id'] == $filter_createby) { ?>
                  <option value="<?php echo $createby['user_id']; ?>" selected="selected"><?php echo $createby['username']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $createby['user_id']; ?>"><?php echo $createby['username']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-updateby"><?php echo $entry_updateby; ?></label>
                <select name="filter_updateby" id="input-updateby" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($users as $updateby) { ?>
                  <?php if ($updateby['user_id'] == $filter_updateby) { ?>
                  <option value="<?php echo $updateby['user_id']; ?>" selected="selected"><?php echo $updateby['username']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $updateby['user_id']; ?>"><?php echo $updateby['username']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-accessby_id"><?php echo $entry_accessby; ?></label>
                <select name="filter_accessby_id" id="input-accessby_id" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($accessbys as $accessby) { ?>
                  <?php if ($accessby['accessby_id'] == $filter_accessby_id) { ?>
                  <option value="<?php echo $accessby['accessby_id']; ?>" selected="selected"><?php echo $accessby['accessby_description'][$config_language_id]['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $accessby['accessby_id']; ?>"><?php echo $accessby['accessby_description'][$config_language_id]['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-modified"><?php echo $entry_date_modified; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-baiviet">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-center"><?php echo $column_image; ?></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'viewed') { ?>
                    <a href="<?php echo $sort_viewed; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_view; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_viewed; ?>"><?php echo $column_view; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'sort_order') { ?>
                    <a href="<?php echo $sort_sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_sort_order; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_sort_order; ?>"><?php echo $column_sort_order; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'accessby_id') { ?>
                    <a href="<?php echo $sort_accessby; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_accessby; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_accessby; ?>"><?php echo $column_accessby; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'date_modified') { ?>
                    <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($baiviets) { ?>
                <?php foreach ($baiviets as $baiviet) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($baiviet['baiviet_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $baiviet['baiviet_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $baiviet['baiviet_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-center"><?php if ($baiviet['image']) { ?>
                    <img src="<?php echo $baiviet['image']; ?>" alt="<?php echo $baiviet['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $baiviet['name']; ?></td>
                  <td class="text-right"><?php echo $baiviet['viewed']; ?></td>
                  <td class="text-right"><?php echo $baiviet['sort_order']; ?></td>
                  <td class="text-left"><?php echo $baiviet['status']; ?></td> 
                  <td class="text-left"><?php echo $baiviet['accessby']; ?></td> 
                  <td class="text-left"><span data-toggle="tooltip" title="<?php echo $baiviet['createby']; ?>"><?php echo $baiviet['date_added']; ?></span></td>
                  <td class="text-left"><span data-toggle="tooltip" title="<?php echo $baiviet['updateby']; ?>"><?php echo $baiviet['date_modified']; ?></span></td>
                  <td class="text-right"><a href="<?php echo $baiviet['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	var url = 'index.php?route=catalog/baiviet&token=<?php echo $token; ?>';

	var filter_danhmucbaiviet_id = $('input[name=\'filter_danhmucbaiviet_id\']').val();

	if (filter_danhmucbaiviet_id) {
		url += '&filter_danhmucbaiviet_id=' + encodeURIComponent(filter_danhmucbaiviet_id);
	}

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}
	
	var filter_createby = $('select[name=\'filter_createby\']').val();
	
	if (filter_createby != '*') {
		url += '&filter_createby=' + encodeURIComponent(filter_createby);
	}
	
	var filter_updateby = $('select[name=\'filter_updateby\']').val();
	
	if (filter_updateby != '*') {
		url += '&filter_updateby=' + encodeURIComponent(filter_updateby);
	}
	
	var filter_accessby_id = $('select[name=\'filter_accessby_id\']').val();
	
	if (filter_accessby_id != '*') {
		url += '&filter_accessby_id=' + encodeURIComponent(filter_accessby_id);
	}
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}

	location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_danhmucbaiviet\']').autocomplete({
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
		$('input[name=\'filter_danhmucbaiviet\']').val(item['label']);
		$('input[name=\'filter_danhmucbaiviet_id\']').val(item['value']);
	}
});
//--></script>
  <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
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
		$('input[name=\'filter_name\']').val(item['label']);
	}
});
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>