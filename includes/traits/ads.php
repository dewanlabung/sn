<?php

/**
 * trait -> ads
 * 
 * @package Sngine
 * @author Zamblek
 */

trait AdsTrait
{

  /* ------------------------------- */
  /* Ads */
  /* ------------------------------- */

  /**
   * ads
   * 
   * @param string $place
   * @param integer $node_id
   * @return array
   */
  public function ads($place, $node_id = null)
  {
    global $db;
    $ads = [];
    /* check if the viewer is ads free */
    if ($this->_logged_in && $this->_data['ads_free']) {
      return $ads;
    }
    /* check if ads place is pages or grpups */
    $where_query = '';
    if ($node_id) {
      $node_id = secure($node_id, 'search');
      $node_id = str_replace("'%", "'%;", $node_id);
      $node_id = str_replace("%'", "&%'", $node_id);
    }
    if ($place == 'pages') {
      $where_query = sprintf('AND ads_pages_ids LIKE %s', $node_id);
    } elseif ($place == 'groups') {
      $where_query = sprintf('AND ads_groups_ids LIKE %s', $node_id);
    }
    $get_ads = $db->query(sprintf("SELECT * FROM ads_system WHERE place = %s ", secure($place)) . $where_query);
    if ($get_ads->num_rows > 0) {
      while ($ads_unit = $get_ads->fetch_assoc()) {
        $ads_unit['code'] = html_entity_decode($ads_unit['code'], ENT_QUOTES);
        $ads[] = $ads_unit;
      }
    }
    return $ads;
  }
}
