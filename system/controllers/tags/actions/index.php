<?php

class actionTagsIndex extends cmsAction {

    public function run($target = '', $tag_name = ''){

        $is_index = false;

        // ничего нет - показываем список тегов
        if(!$target && !$tag_name){
            return $this->displayTags();
        }

        // передан только $target, значит это тег
        if(!$tag_name){
            $tag_name = $target;
            $target   = '';
        }

        // субъект в формате controller_name-subject_name
        if($target && !preg_match('/^([a-z0-9\_]+\-{1}[a-z0-9\_]+)$/', $target)){
            cmsCore::error404();
        }

        $tag_name = urldecode($tag_name);

        // получаем тег
        $tag = $this->model->getTagByTag($tag_name);
        if(!$tag){
            cmsCore::error404();
        }

        // получаем все субъекты тега
        $targets = $this->model->getTagTargets($tag['id']);
        if(!$targets){
            cmsCore::error404();
        }

        // субъект по умолчанию - первый из списка
        if(!$target){

            $is_index = true;

            foreach ($targets as $controller_name => $subjects) {

                $target = $controller_name.'-'.reset($subjects);

                break;
            }

        }

        list($target_controller, $target_subject) = explode('-', $target);

        if(!cmsCore::isControllerExists($target_controller) || !cmsController::enabled($target_controller)){
            cmsCore::error404();
        }

        $page_url = $is_index ? href_to($this->name, urlencode($tag_name)) : href_to($this->name, $target, urlencode($tag_name));

        // результат поиска получаем только по переданному контроллеру
        $controller = cmsCore::getController($target_controller, $this->request);

        $list_html = $controller->runHook('tags_search', array($target_subject, $page_url));
        if (!$list_html) { cmsCore::error404(); }

        $menu_items = cmsEventsManager::hookAll('tags_search_subjects', array($tag, $targets, $target, $is_index));
        if (!$menu_items) { cmsCore::error404(); }

        $is_first_menu_item = true;

        foreach ($menu_items as $menu_item) {

            if($is_first_menu_item){

                $keys = array_keys($menu_item);
                $first_key = $keys[0];

                $menu_item[$first_key]['url'] = str_replace($first_key.'/', '', $menu_item[$first_key]['url']);

            }

            $this->cms_template->addMenuItems('results_tabs', $menu_item);

            $is_first_menu_item = false;

        }

        $seo_title = sprintf(LANG_TAGS_SEARCH_BY_TAG, $tag['tag']);
        $seo_keys  = $seo_title;
        $seo_desc  = $seo_title;
        $seo_h1    = $seo_title;

        $seo_data = array(
            'tag'         => $tag['tag'],
            'ctype_title' => $menu_items[$target_controller][$target]['title']
        );

        if($tag['tag_title']){
            $seo_title = string_replace_keys_values($tag['tag_title'], $seo_data);
        }

        if($tag['tag_desc']){
            $seo_desc = string_replace_keys_values($tag['tag_desc'], $seo_data);
        }

        if($tag['tag_h1']){
            $seo_h1 = string_replace_keys_values($tag['tag_h1'], $seo_data);
        }

        if ($this->cms_user->is_admin){
            $this->cms_template->addToolButton(array(
                'class' => 'page_gear',
                'title' => LANG_TAGS_SETTINGS,
                'href'  => href_to('admin', 'controllers', array('edit', 'tags'))
            ));
        }

        return $this->cms_template->render('search', array(
            'seo_title'  => $seo_title,
            'seo_keys'   => $seo_keys,
            'seo_desc'   => $seo_desc,
            'seo_h1'     => $seo_h1,
            'html'       => $list_html
        ));

    }

    private function displayTags() {

        return $this->cms_template->render('tags', $this->getTagsWidgetParams($this->options));

    }

}