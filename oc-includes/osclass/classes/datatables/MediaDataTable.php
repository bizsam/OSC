<?php
if(!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 * Copyright 2014 Osclass
 * Copyright 2023 Osclass by OsclassPoint.com
 *
 * Osclass maintained & developed by OsclassPoint.com
 * You may not use this file except in compliance with the License.
 * You may download copy of Osclass at
 *
 *     https://osclass-classifieds.com/download
 *
 * Do not edit or add to this file if you wish to upgrade Osclass to newer
 * versions in the future. Software is distributed on an "AS IS" basis, without
 * warranties or conditions of any kind, either express or implied. Do not remove
 * this NOTICE section as it contains license information and copyrights.
 */


/**
 * MediaDataTable class
 * 
 * @since 3.1
 * @package Osclass
 * @subpackage classes
 * @author Osclass
 */
class MediaDataTable extends DataTable
{
  private $order_by;
  private $resourceID;

  /**
   * @param $params
   *
   * @return array
   */
  public function table( $params )
  {
    
    $this->addTableHeader();
    $this->getDBParams($params);

    $media = ItemResource::newInstance()->getResources($this->resourceID, $this->start, $this->limit, $this->order_by['column_name'], $this->order_by['type']);
    $this->processData($media);
    
    $this->total = ItemResource::newInstance()->countResources();
    if( $this->resourceID == null ) {
      $this->total_filtered = $this->total;
    } else {
      $this->total_filtered = ItemResource::newInstance()->countResources( $this->resourceID );
    }
          
    return $this->getData();
  }

  private function addTableHeader()
  {

    $arg_date = '&sort=date';
    if( Params::getParam('sort') === 'date') {
      if( Params::getParam('direction') === 'desc') {
        $arg_date .= '&direction=asc';
      }
    }
    $arg_item = '&sort=attached_to';
    if( Params::getParam('sort') === 'attached_to') {
      if( Params::getParam('direction') === 'desc') {
        $arg_item .= '&direction=asc';
      }
    }

    Rewrite::newInstance()->init();
    $page  = (int)Params::getParam('iPage');
    if($page==0) { $page = 1; }
    Params::setParam('iPage', $page);
    $url_base = preg_replace('|&direction=([^&]*)|', '', preg_replace('|&sort=([^&]*)|', '', osc_base_url().Rewrite::newInstance()->get_raw_request_uri()));

    $this->addColumn('bulkactions', '<input id="check_all" type="checkbox" />');
    $this->addColumn('file', __('File'));
    $this->addColumn('type', __('Type'));
    $this->addColumn('action', __('Action'));
    $this->addColumn('attached_to', '<a href="'.osc_esc_html($url_base.$arg_item).'">'.__('Attached to').'</a>');
    $this->addColumn('date', '<a href="'.osc_esc_html($url_base.$arg_date).'">'.__('Date').'</a>');

    $dummy = &$this;
    osc_run_hook( 'admin_media_table' , $dummy);
  }

  /**
   * @param $media
   */
  private function processData( $media )
  {
    if(!empty($media)) {
    
      foreach($media as $aRow) {
        $row = array();

        $row['bulkactions'] = '<input type="checkbox" name="id[]" value="' . $aRow['pk_i_id'] . '" />';
        $row['file'] = '<div class="img-wrap"><img src="' . osc_apply_filter('resource_thumbnail_url', osc_apply_filter('resource_path', osc_base_url() . $aRow['s_path']) . $aRow['pk_i_id'] . '_thumbnail.' . $aRow['s_extension']) . '" width="60" height="50"/></div>';
        $row['type'] = $aRow['s_content_type'];
        $row['action'] = '<a onclick="return delete_dialog(\'' . $aRow['pk_i_id'] . '\');" >' . __('Delete') . '</a>';
        $row['attached_to'] = '<a target="_blank" href="' . osc_item_url_ns($aRow['fk_i_item_id']) . '">item #' . $aRow['fk_i_item_id'] . '</a>';
        $row['date'] = osc_format_date($aRow['dt_pub_date']);

        $row = osc_apply_filter('media_processing_row', $row, $aRow);

        $this->addRow($row);
        $this->rawRows[] = $aRow;
      }

    }
  }

  /**
   * @param $_get
   */
  private function getDBParams( $_get )
  {
    
    foreach($_get as $k => $v) {
      if( ( $k === 'resourceId' ) && !empty($v) ) {
        $this->resourceID = (int) $v;
      }
      if( $k === 'iDisplayStart' ) {
        $this->start = (int) $v;
      }
      if( $k === 'iDisplayLength' ) {
        $this->limit = (int) $v;
      }
      if( $k === 'sEcho' ) {
        $this->sEcho = (int) $v;
      }
    }


    $this->order_by['type'] = $direction = $_get['direction'];
    $arrayDirection = array('desc', 'asc');
    if(!in_array($direction, $arrayDirection)) {
      Params::setParam('direction', 'desc');
      $this->order_by['type'] = 'desc';
    }

    // column sort
    $sort     = $_get['sort'];
    $arraySortColumns = array('date' => 'r.pk_i_id', 'attached_to' => 'r.fk_i_item_id');
    if ( ! array_key_exists( $sort , $arraySortColumns ) ) {
      $this->order_by['column_name'] = 'r.pk_i_id';
    } else {
      $this->order_by['column_name'] = $arraySortColumns[$sort];
    }

    // set start and limit using iPage param
    $start = ((int)Params::getParam('iPage')-1) * $_get['iDisplayLength'];

    $this->start = (int) $start;
    $this->limit = (int) $_get[ 'iDisplayLength' ];
  
  }
}