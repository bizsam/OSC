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
 *
 */
class PluginCategory extends DAO
{
  /**
   *
   * @var
   */
  private static $instance;

  /**
   *
   * @return \PluginCategory
   */
  public static function newInstance()
  {
    if( !self::$instance instanceof self ) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  /**
   *
   */
  public function __construct()
  {
    parent::__construct();
    $this->setTableName('t_plugin_category');
    /* $this->setPrimaryKey('pk_i_id'); */
    $this->setFields( array('s_plugin_name', 'fk_i_category_id') );
  }

  /**
   * Return all information given a category id
   *
   * @access public
   * @since  unknown
   *
   * @param $categoryId
   *
   * @return array
   */
  public function findByCategoryId($categoryId)
  {
    $this->dao->select( $this->getFields() );
    $this->dao->from( $this->getTableName() );
    $this->dao->where('fk_i_category_id', $categoryId);

    $result = $this->dao->get();

    if( $result == false ) {
      return array();
    }

    return $result->result();
  }

  /**
   * Return list of categories asociated with a plugin
   *
   * @access public
   * @since unknown
   * @param string $plugin
   * @return array
   */
  public function listSelected($plugin)
  {
    $this->dao->select( $this->getFields() );
    $this->dao->from( $this->getTableName() );
    $this->dao->where('s_plugin_name', $plugin);

    $result = $this->dao->get();

    if( $result == false ) {
      return array();
    }

    $list = array();
    foreach($result->result() as $sel) {
      $list[] = $sel['fk_i_category_id'];
    }

    return $list;
  }

  /**
   * Check if a category is asociated with a plugin
   *
   * @access public
   * @since unknown
   * @param string $pluginName
   * @param int $categoryId
   * @return bool
   */
  public function isThisCategory($pluginName, $categoryId)
  {
    $this->dao->select('COUNT(*) AS numrows');
    $this->dao->from( $this->getTableName() );
    $this->dao->where('fk_i_category_id', $categoryId);
    $this->dao->where('s_plugin_name', $pluginName);

    $result = $this->dao->get();

    if( $result == false ) {
      return false;
    }

    if( $result->numRows() == 0 ) {
      return false;
    }

    $row = $result->row();

    return ! ( $row[ 'numrows' ] == 0 );
  }
}

/* file end: ./oc-includes/osclass/model/PluginCategory.php */