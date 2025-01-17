<h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1>

<?php

    $this->setPageTitle(LANG_CP_CTYPE_RELATIONS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_RELATIONS);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_RELATION_ADD,
        'href'  => $this->href_to('ctypes', array('relations_add', $ctype['id']))
    ));
    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE_ORDER,
        'href'  => null,
        'onclick' => "icms.datagrid.submit('{$this->href_to('ctypes', array('relations_reorder', $ctype['id']))}')"
    ));
    $this->addToolButton(array(
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_RELATIONS
	));

?>

<div class="pills-menu">
    <?php $this->menu('admin_toolbar'); ?>
</div>

<?php $this->renderGrid($this->href_to('ctypes', array('relations', $ctype['id'])), $grid);
