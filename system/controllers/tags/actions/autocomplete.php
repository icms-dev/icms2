<?php
/**
 * @property \modelTags $model
 */
class actionTagsAutocomplete extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $result = [];

        $term = $this->request->get('term', '');
        if (!$term) {
            return $this->cms_template->renderJSON($result);
        }

        $tags = $this->model->filterLike('tag', "%{$term}%")->
                select("(LEFT(`tag`, " . mb_strlen($term) . ") = '{$term}')", 'tag_order')->
                orderByList([
                    ['by' => 'tag_order', 'to' => 'desc', 'strict' => true],
                    ['by' => 'tag', 'to' => 'asc']
                ])->
                getTags();

        if ($tags) {
            foreach ($tags as $tag) {
                $result[] = [
                    'id'    => $tag['id'],
                    'label' => $tag['tag'],
                    'value' => $tag['tag']
                ];
            }
        }

        return $this->cms_template->renderJSON($result);
    }

}
