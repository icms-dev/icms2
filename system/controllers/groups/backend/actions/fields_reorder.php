<?php

class actionGroupsFieldsReorder extends cmsAction {

    public function run() {

        $items = $this->request->get('items', []);
        if (!$items) {
            return cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $content_model->setTablePrefix('');

        $content_model->reorderContentFields('groups', $items);

        if ($this->request->isAjax()) {

            return $this->cms_template->renderJSON([
                'error'        => false,
                'success_text' => LANG_CP_ORDER_SUCCESS
            ]);
        }

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        return $this->redirectToAction('fields');
    }

}
