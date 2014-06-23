<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.jstree/apple/style.css" />
<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.jstree.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.dialog-plugin.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/blockUI.js', CClientScript::POS_HEAD);

$this->breadcrumbs=array(
	tt('Manage menu items'),
);

$this->menu = array(
	array(),
);

$this->adminTitle = tt('Manage menu items');
?>

<div class="flash-notice"><?php echo tt('help_menumanager_backend_main_admin'); ?></div>

<script type="text/javascript">

var dlgStatus=$().dialogPlugin();
var dlgConfirm=$().dialogPlugin();
var jstree;

function jstree_loaded(){
	$(this).find('li[rel=root]').find('.jstree-checkbox:first').hide();
}

// редактирование элемента
function editItem(obj){
	var pid=$(this._get_node(obj)).attr('pid');
	if($.isNumeric(pid)) {
		var url = '<?php echo $this->createUrl("update"); ?>';

		if(url.indexOf("?") != -1) {
			window.location=url +'&id='+pid;
		}
		else {
			window.location=url +'?id='+pid;
		}
	}
	else
		dlgStatus.notice(<?php echo CJavaScript::encode(tt("Editing of this section is not allowed")); ?>,<?php echo CJavaScript::encode(tc("Error")); ?>).open();
}

// добавление узла
function create_node(e, data){
	var title=data.rslt.name;
	if(typeof data.rslt.obj.parents("li:eq(0)").attr("pid")=='undefined'){
		dlgStatus.notice(<?php echo CJavaScript::encode(tt("The parent node wasn't created. Please try again later.")); ?>,<?php echo CJavaScript::encode(tc("Error")); ?>).open();
		$.jstree.rollback(data.rlbk);
		return;
	}
	if(title==<?php echo CJavaScript::encode(tt("Enter the name of the menu item ")); ?>){
		$.jstree.rollback(data.rlbk);
		return;
	}
	jstree.jstree('lock');
	$.post(
		'<?php echo $this->createUrl("create"); ?>',
		{
			"parentId" : data.rslt.obj.parents("li:eq(0)").attr("pid"),
			"title" : title,
			"number": data.rslt.position
		},
		function(respdata){
			jstree.jstree('unlock');
			$(data.rslt.obj).attr("pid", respdata);
			jstree.jstree("refresh");
		}
	)
		.error(function(jqXHR){
			jstree.jstree('unlock');
			$.jstree.rollback(data.rlbk);
			dlgStatus.notice(jqXHR.responseText,<?php echo CJavaScript::encode(tc("Error")); ?>).open();
			jstree.jstree("refresh");
		});
};

//переименовать узел
function rename_node(e,data){
	var oldName=data.rslt.old_name;
	var newName=data.rslt.new_name;

	if(oldName==newName) return;

	dlgConfirm.confirm("<?php echo CJavaScript::encode(tt("Dou you really want to rename menu item")); ?> "+oldName+" <?php echo CJavaScript::encode(tt("in")); ?> "+newName+" ",<?php echo CJavaScript::encode(tt("Rename menu item")); ?>,
		function(){
			$.post(
				'<?php echo $this->createUrl("rename"); ?>',
				{
					"pid" : data.rslt.obj.attr("pid"),
					"title" : newName
				},
				function (respdata) {
					jstree.jstree("refresh");
				}
			)
				.error(function(jqXHR){
					$.jstree.rollback(data.rlbk);
					dlgStatus.notice(jqXHR.responseText,<?php echo CJavaScript::encode(tc("Error")); ?>).open();
				});
		},true,
		function(){
			$.jstree.rollback(data.rlbk);
		}
	).open();
};

//переключение чекбокса
function change_state(e, data){
	var checked=data.inst.is_checked(data.rslt)?1:0;
	var pid=data.rslt.attr("pid");
	if(typeof pid=='undefined') return;
	$.post(
		'<?php echo $this->createUrl("setVisible"); ?>',
		{
			"pid" : pid,
			"visible" : checked
		}
	)
		.error(function(jqXHR){
			dlgStatus.notice(jqXHR.responseText,<?php echo CJavaScript::encode(tc("Error")); ?>).open();
		});
}

//удалить узел
function remove_node(e,data){
	dlgConfirm.confirm("<?php echo CJavaScript::encode(tt("Do you really want to remove the menu item")); ?> "+data.inst.get_text(data.rslt.obj)+" <?php echo CJavaScript::encode(tt("and all its descendants?")); ?>",<?php echo CJavaScript::encode(tt("Delete a menu item")); ?>,
		function(){
			$.post(
				'<?php echo $this->createUrl("deleteItem"); ?>',
				{
					"pid" : data.rslt.obj.attr("pid")
				},
				function (respdata) {
					jstree.jstree("refresh");
				}
			)
				.error(function(jqXHR){
					$.jstree.rollback(data.rlbk);
					dlgStatus.notice(jqXHR.responseText,<?php echo CJavaScript::encode(tc("Error")); ?>).open();
				});
		},true,
		function(){
			$.jstree.rollback(data.rlbk);
		}
	).open();
};

function move_node(e,data){
	var ref=data.rslt.r.attr("pid");
	var pos=data.rslt.p;
	if(ref==0 && (pos=='after' || pos=='before')){
		$.jstree.rollback(data.rlbk);
		return;
	}
	jstree.jstree('lock');


	$.post(
		'<?php echo $this->createUrl("move"); ?>',
		{
			"pid" : data.rslt.o.attr("pid"),
			"dst" : data.rslt.np.attr("pid"),
			"ref" : ref,
			"pos": pos
		},
		function(){
			jstree.jstree('unlock');
			jstree.jstree("refresh");
		}
	)
		.error(function(jqXHR){
			jstree.jstree('unlock');
			$.jstree.rollback(data.rlbk);
			jstree.jstree('refresh');
			dlgStatus.notice(jqXHR.responseText,<?php echo CJavaScript::encode(tc("Error")); ?>).open();
		});
};

function contextMenu(node){
	var items={
		create : {
			label	: <?php echo CJavaScript::encode(tt("Create inside")); ?>,
			action: function(){jstree.jstree('create')}
		},
		rename : {
			label	: <?php echo CJavaScript::encode(tc("Rename")); ?>,
			action: function(){jstree.jstree('rename')}
		},
		remove : {
			label	: <?php echo CJavaScript::encode(tt("Delete")); ?>,
			action: function(){jstree.jstree('remove')}
		},
		edit : {
			label: <?php echo CJavaScript::encode(tt("Edit")); ?>,
			separator_before: true,
			action: editItem
		},
		ccp: false
	}

	if ($(node).attr('rel') == 'root') {
		delete items.rename;
		delete items.remove;
		delete items.edit;
	}
	else {
		switch($(node).attr('rel')){
		}

		switch($(node).attr('special')){
			case '1':
				delete items.create;
				delete items.remove;
				break;
		}

		switch($(node).attr('level')){
			case '0':
			case '1':
			case '2':
				break;
			case '<?php echo Menu::MAX_LEVEL;?>':
				delete items.create;
				break;
		}
	}

	jstree.jstree("deselect_all");
	jstree.jstree("select_node", node);

	return items;
}

$(function(){
	var freeHeight=$(window).height()-110;
	$("#pageList").height(freeHeight);

	jstree=$("#pageList").jstree({
		core:{
			"strings":{
				new_node    : <?php echo CJavaScript::encode(tt("Enter the name of the menu item ")); ?>,
				loading     : <?php echo CJavaScript::encode(tc("Loading ...")); ?>
			}
		},
		json_data : {
			"ajax" : {
				"url" : "<?php echo $this->createUrl("getPageList"); ?>"
			}
		},
		themes:{
			"theme" : "apple",
			"url" : '<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.jstree/apple/style.css',
			"dots" : true,
			"icons" : true
		},
		checkbox:{
			two_state: true
		},
		contextmenu: {items: contextMenu},
		types:{
			"types" : {
				"root" : {
					"icon" : {
						'position': '-56px -37px'
					},
					"start_drag" : false,
					"move_node" : false,
					"delete_node" : false,
					"rename" : false,
					"remove" : false,
					"change_state" : false
				},
				"default" : {
					"icon" : {
						'position': '-75px -38px'
					}
				}
			}
		},
		plugins : ["themes", "json_data", "contextmenu", "crrm", "dnd", "checkbox", "ui", "types"]
	})
		.bind("loaded.jstree", jstree_loaded)
		.bind("rename.jstree", rename_node)
		.bind('change_state.jstree', change_state)
		.bind("remove.jstree", remove_node)
		.bind("create.jstree", create_node)
		.bind("move_node.jstree", move_node)
	;

});
</script>


<div id="pageList" style="height: 400px; overflow: auto"></div>