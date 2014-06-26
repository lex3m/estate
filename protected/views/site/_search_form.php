<?php
$compact = isset($compact) ? $compact : 0;
$isInner = isset($isInner) ? $isInner : 0;

if(isset($this->objType) && $this->objType){
    $searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => $this->objType), array('group' => 'field'));
    if(!$searchFields){
        $searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => SearchFormModel::OBJ_TYPE_ID_DEFAULT), array('group' => 'field'));
    }
} else {
    $searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => SearchFormModel::OBJ_TYPE_ID_DEFAULT), array('group' => 'field'));
}
$i = 1;
foreach($searchFields as $search){
    if($isInner){
        $divClass = 'small-header-form-line width450';
    }else{
        $divClass = 'header-form-line';
    }

    //if($search->status <= SearchFormModel::STATUS_NOT_REMOVE){
        $this->renderPartial('//site/_search_field_' . $search->field, array(
                'divClass' => $divClass,
                'textClass' => 'width140',
                'fieldClass' => 'width290 search-input-new',
                'minWidth' => '290',
                'isInner' => $isInner,
            ));
   /* } else {
        $this->renderPartial('//site/_search_new_field', array(
                'divClass' => $divClass,
                'textClass' => 'width135',
                'fieldClass' => 'width290 search-input-new',
                'minWidth' => '290',
                'search' => $search,
                'isInner' => $isInner,
            ));
    }*/

    if($isInner && $i == 1) {
        $displayLink = $compact ? '' : 'style="display: none;"';
        $displayForm = $compact ? 'style="display: none;"' : '';
      //  echo '<a href="javascript:;" id="more-options-link-inner" '.$displayLink.'>'.tc('More options').'</a>';
     //   echo '<div tt="1" '.$displayForm.' id="search-more-fields">';
    }
    $i++;

    SearchForm::increaseJsCounter();
}

if($isInner){
    echo '</div tt="1">';
}
?>
