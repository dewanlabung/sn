<?php

/**
 * trait -> forums
 * 
 * @package Sngine
 * @author Zamblek
 */

trait ForumsTrait
{

  /* ------------------------------- */
  /* Forums */
  /* ------------------------------- */

  /**
   * get_forums
   * 
   * @param integer $node_id
   * @param boolean $reverse
   * @return array
   */
  public function get_forums($node_id = 0, $reverse = false)
  {
    global $db;
    $tree = [];
    if (!$reverse) {
      // top-down tree (default)
      $get_nodes = $db->query(sprintf("SELECT * FROM forums WHERE forum_section = %s ORDER BY forum_order ASC", secure($node_id, 'int')));
      if ($get_nodes->num_rows > 0) {
        while ($node = $get_nodes->fetch_assoc()) {
          $node['title_url'] = get_url_text($node['forum_name']);
          $node['total_threads'] = $node['forum_threads'];
          $node['total_replies'] = $node['forum_replies'];
          $childs = $this->get_child_forums($node['forum_id']);
          if ($childs) {
            $node['total_threads'] += $childs['total_threads'];
            $node['total_replies'] += $childs['total_replies'];
            $node['childs'] = $childs['childs'];
          }
          $tree[] = $node;
        }
      }
    } else {
      // bottom-up tree
      while (true) {
        $get_parent = $db->query(sprintf("SELECT * FROM forums WHERE forum_id = %s", secure($node_id, 'int')));
        if ($get_parent->num_rows == 0) {
          break;
        }
        $parent = $get_parent->fetch_assoc();
        $parent['title_url'] = get_url_text($parent['forum_name']);
        $node_id = $parent['forum_section'];
        $tree[] = $parent;
        if ($node_id == 0) {
          break;
        }
      }
    }
    return $tree;
  }


  /**
   * get_child_forums
   * 
   * @param integer $node_id
   * @param integer $iteration
   * @return array
   */
  public function get_child_forums($node_id, $iteration = 1)
  {
    global $db;
    $tree = [];
    $get_nodes = $db->query(sprintf("SELECT * FROM forums WHERE forum_section = %s ORDER BY forum_order ASC", secure($node_id)));
    if ($get_nodes->num_rows > 0) {
      while ($node = $get_nodes->fetch_assoc()) {
        $node['iteration'] = $iteration;
        $node['title_url'] = get_url_text($node['forum_name']);
        $node['total_threads'] = $node['forum_threads'];
        $node['total_replies'] = $node['forum_replies'];
        $childs = $this->get_child_forums($node['forum_id'], $iteration + 1);
        if ($childs) {
          $node['total_threads'] += $childs['total_threads'];
          $node['total_replies'] += $childs['total_replies'];
          $node['childs'] = $childs['childs'];
        }
        $tree['total_threads'] += $node['total_threads'];
        $tree['total_replies'] += $node['total_replies'];
        $tree['childs'][] = $node;
      }
    }
    return $tree;
  }


  /**
   * get_child_forums_ids
   * 
   * @param array $nodes
   * @return void
   */
  public function get_child_forums_ids($nodes, &$list = [])
  {
    if (is_array($nodes)) {
      foreach ($nodes as $node) {
        $list[] = $node['forum_id'];
        if ($node['childs']) {
          $this->get_child_forums_ids($node['childs'], $list);
        }
      }
    }
  }


  /**
   * delete_forum
   * 
   * @param integer $node_id
   * @return void
   */
  public function delete_forum($node_id)
  {
    global $db;
    /* delete forum */
    $db->query(sprintf("DELETE FROM forums WHERE forum_id = %s", secure($node_id, 'int')));
    /* delete replies */
    $db->query(sprintf("DELETE FROM forums_replies WHERE thread_id IN (SELECT thread_id FROM forums_threads WHERE forum_id = %s)", secure($node_id, 'int')));
    /* delete threads */
    $db->query(sprintf("DELETE FROM forums_threads WHERE forum_id = %s", secure($node_id, 'int')));
    /* delete childs */
    $childs = $this->get_child_forums($node_id);
    if ($childs) {
      $this->delete_child_forums($childs['childs']);
    }
  }


  /**
   * delete_child_forums
   * 
   * @param array $nodes
   * @return void
   */
  public function delete_child_forums($nodes)
  {
    global $db;
    if (is_array($nodes)) {
      foreach ($nodes as $node) {
        /* delete forum */
        $db->query(sprintf("DELETE FROM forums WHERE forum_id = %s", secure($node['forum_id'], 'int')));
        /* delete replies */
        $db->query(sprintf("DELETE FROM forums_replies WHERE thread_id IN (SELECT thread_id FROM forums_threads WHERE forum_id = %s)", secure($node['forum_id'], 'int')));
        /* delete threads */
        $db->query(sprintf("DELETE FROM forums_threads WHERE forum_id = %s", secure($node['forum_id'], 'int')));
        /* delete childs */
        if ($node['childs']) {
          $this->delete_child_forums($node['childs']);
        }
      }
    }
  }


  /**
   * get_forum
   * 
   * @param integer $forum_id
   * @param boolean $get_childs
   * @return array
   */
  public function get_forum($forum_id, $get_childs = true)
  {
    global $db;
    $get_forum = $db->query(sprintf("SELECT * FROM forums WHERE forum_id = %s", secure($forum_id, 'int')));
    if ($get_forum->num_rows == 0) {
      return false;
    }
    $forum = $get_forum->fetch_assoc();
    $forum['title_url'] = get_url_text($forum['forum_name']);
    $forum['total_threads'] = $forum['forum_threads'];
    $forum['total_replies'] = $forum['forum_replies'];
    if ($get_childs) {
      $childs = $this->get_child_forums($forum['forum_id']);
      if ($childs) {
        $forum['total_threads'] += $childs['total_threads'];
        $forum['total_replies'] += $childs['total_replies'];
        $forum['childs'] = $childs['childs'];
      }
    }
    $forum['parents'] = $this->get_forums($forum['forum_section'], true);
    return $forum;
  }


  /**
   * get_forum_threads
   * 
   * @param array $args
   * @return array
   */
  public function get_forum_threads($args = [])
  {
    global $db, $system, $smarty;
    /* initialize vars */
    $threads = [];
    /* initialize arguments */
    $forum = !isset($args['forum']) ? null : $args['forum'];
    $user_id = !isset($args['user_id']) ? null : $args['user_id'];
    $query = !isset($args['query']) ? null : $args['query'];
    $sort = !isset($args['sort']) ? 'newest' : $args['sort'];
    /* get threads */
    if ($forum !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_threads WHERE forum_id = %s", secure($forum['forum_id'], 'int')));
    } elseif ($user_id !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_threads WHERE user_id = %s", secure($user_id, 'int')));
    } elseif ($query !== null) {
      /* prepare where statement */
      $where = "";
      /* query */
      $where .= (!is_empty($query)) ? sprintf(' WHERE title LIKE %1$s OR text LIKE %1$s ', secure($query, 'search')) : "";
      /* forums list */
      if ($args['forums_list']) {
        $forums_list = $this->spread_ids($args['forums_list']);
        $where = ($where != '') ? $where . " AND forum_id IN ($forums_list) " : $where;
      }
      $get_total = $db->query("SELECT COUNT(*) as count FROM forums_threads" . $where);
    }
    $total_items = $get_total->fetch_assoc()['count'];
    if ($total_items > 0) {
      require('includes/class-pager.php');
      $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
      $params['total_items'] = $total_items;
      $params['items_per_page'] = $system['max_results'] * 2;
      /* prepare pager URL */
      if ($forum !== null) {
        $params['url'] = $system['system_url'] . '/forums/' . $forum['forum_id'] . '/' . $forum['title_url'] . '?page=%s';
      } elseif ($user_id !== null) {
        $params['url'] = $system['system_url'] . '/forums/my-threads?page=%s';
      } elseif ($query !== null) {
        $params['url'] = remove_querystring_var($_SERVER['REQUEST_URI'], 'page');
        $params['url'] = $params['url'] . '&page=%s';
      }
      $pager = new Pager($params);
      $limit_query = $pager->getLimitSql();
      /* sort order */
      switch ($sort) {
        case 'top':
          $order = "forums_threads.replies DESC, forums_threads.views DESC";
          break;
        case 'hot':
          $order = "((forums_threads.replies + forums_threads.views) / (TIMESTAMPDIFF(HOUR, forums_threads.time, NOW()) + 2)) DESC";
          break;
        case 'rising':
          $order = "forums_threads.views DESC, forums_threads.time DESC";
          break;
        case 'q&a':
          $order = "forums_threads.replies ASC, forums_threads.time DESC";
          break;
        case 'unanswered':
          $order = "forums_threads.time DESC";
          break;
        default:
          $order = "forums_threads.last_reply DESC";
      }
      if ($forum !== null) {
        $where_unanswered = ($sort === 'unanswered') ? " AND forums_threads.replies = 0" : "";
        $get_threads = $db->query(sprintf("SELECT forums_threads.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_verified FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.forum_id = %s" . $where_unanswered . " ORDER BY " . $order . " " . $limit_query, secure($forum['forum_id'], 'int')));
      } elseif ($user_id !== null) {
        $get_threads = $db->query(sprintf("SELECT forums_threads.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_verified FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.user_id = %s ORDER BY " . $order . " " . $limit_query, secure($user_id, 'int')));
      } elseif ($query !== null) {
        $get_threads = $db->query("SELECT forums_threads.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_verified FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id " . $where . " ORDER BY " . $order . " " . $limit_query);
      }
      $this->ensure_forums_votes_table();
      while ($thread = $get_threads->fetch_assoc()) {
        $thread['user_fullname'] = ($system['show_usernames_enabled']) ? $thread['user_name'] : $thread['user_firstname'] . " " . $thread['user_lastname'];
        $thread['user_picture']  = get_picture($thread['user_picture'], $thread['user_gender']);
        $thread['title_url'] = get_url_text($thread['title']);
        /* parse text */
        $thread['parsed_text'] = htmlspecialchars_decode($thread['text'], ENT_QUOTES);
        $thread['text_snippet'] = get_snippet_text($thread['text']);
        /* vote data */
        $thread['votes'] = $this->get_forum_votes($thread['thread_id'], 'thread');
        /* get forum */
        if (!$forum) {
          $thread['forum'] = $this->get_forum($thread['forum_id'], false);
        }
        $threads[] = $thread;
      }
      /* assign variables */
      $smarty->assign('total', $params['total_items']);
      $smarty->assign('pager', $pager->getPager());
    }
    return $threads;
  }


  /**
   * get_forum_thread
   * 
   * @param integer $thread_id
   * @param boolean $update_views
   * @return array
   */
  public function get_forum_thread($thread_id, $update_views = false)
  {
    global $db, $system;
    $get_thread = $db->query(sprintf("SELECT forums_threads.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE thread_id = %s", secure($thread_id, 'int')));
    if ($get_thread->num_rows == 0) {
      return false;
    }
    $thread = $get_thread->fetch_assoc();
    /* get forum */
    $thread['forum'] = $this->get_forum($thread['forum_id'], false);
    if (!$thread['forum']) {
      return false;
    }
    $thread['user_picture'] = get_picture($thread['user_picture'], $thread['user_gender']);
    $thread['user_fullname'] = ($system['show_usernames_enabled']) ? $thread['user_name'] : $thread['user_firstname'] . " " . $thread['user_lastname'];
    $thread['title_url'] = get_url_text($thread['title']);
    /* parse text */
    $thread['parsed_text'] = htmlspecialchars_decode($thread['text'], ENT_QUOTES);
    $thread['text_snippet'] = get_snippet_text($thread['text']);
    /* check if viewer can manage thread [Edit|Delete] */
    $thread['manage_thread'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $thread['manage_thread'] = true;
      }
      /* viewer is the author of thread */
      if ($this->_data['user_id'] == $thread['user_id']) {
        $thread['manage_thread'] = true;
      }
    }
    /* update thread views */
    if ($update_views) {
      $db->query(sprintf("UPDATE forums_threads SET views = views + 1 WHERE thread_id = %s", secure($thread['thread_id'], 'int')));
    }
    /* attach vote data */
    $this->ensure_forums_votes_table();
    $thread['votes'] = $this->get_forum_votes($thread['thread_id'], 'thread');
    return $thread;
  }


  /**
   * post_forum_thread
   * 
   * @param string $forum_id
   * @param string $title
   * @param string $text
   * @return integer
   */
  public function post_forum_thread($forum_id, $title, $text)
  {
    global $db, $system, $date;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get forum */
    $forum = $this->get_forum($forum_id, false);
    /* check forum */
    if (!$forum) {
      _error(403);
    }
    if ($forum['forum_section'] == 0) {
      throw new Exception(__("You can't add a thread to a main section"));
    }
    /* validate title */
    if (is_empty($title)) {
      throw new Exception(__("You must enter a title for your thread"));
    }
    if (strlen($title) < 3) {
      throw new Exception(__("Thread title must be at least 3 characters long. Please try another"));
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your thread"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $purifier = get_html_purifier();
      $clean_text = $purifier->purify($text);
    }
    /* insert thread */
    $db->query(sprintf("INSERT INTO forums_threads (forum_id, user_id, title, text, time, last_reply) VALUES (%s, %s, %s, %s, %s, %s)", secure($forum_id, 'int'), secure($this->_data['user_id'], 'int'), secure($title), secure($clean_text), secure($date), secure($date)));
    $thread_id = $db->insert_id;
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_threads = forum_threads + 1 WHERE forum_id = %s", secure($forum_id, 'int')));
    /* extract hosted images from the text */
    $uploaded_images = extract_uploaded_images_from_text($clean_text);
    /* remove pending uploads */
    if ($uploaded_images) {
      remove_pending_uploads($uploaded_images);
    }
    return $thread_id;
  }


  /**
   * edit_forum_thread
   * 
   * @param string $thread_id
   * @param string $title
   * @param string $text
   * @return void
   */
  public function edit_forum_thread($thread_id, $title, $text)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get thread */
    $thread = $this->get_forum_thread($thread_id);
    /* check thread */
    if (!$thread) {
      _error(403);
    }
    /* check permission */
    if (!$thread['manage_thread']) {
      _error(403);
    }
    /* validate title */
    if (is_empty($title)) {
      throw new Exception(__("You must enter a title for your thread"));
    }
    if (strlen($title) < 3) {
      throw new Exception(__("Thread title must be at least 3 characters long. Please try another"));
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your thread"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $purifier = get_html_purifier();
      $clean_text = $purifier->purify($text);
    }
    /* edit thread */
    $db->query(sprintf("UPDATE forums_threads SET title = %s, text = %s WHERE thread_id = %s", secure($title), secure($clean_text), secure($thread_id, 'int')));
    /* extract hosted images from the text */
    $uploaded_images = extract_uploaded_images_from_text($clean_text);
    /* remove pending uploads */
    if ($uploaded_images) {
      remove_pending_uploads($uploaded_images);
    }
  }


  /**
   * delete_forum_thread
   * 
   * @param string $thread_id
   * @return array
   */
  public function delete_forum_thread($thread_id)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get thread */
    $thread = $this->get_forum_thread($thread_id);
    /* check thread */
    if (!$thread) {
      _error(403);
    }
    /* check permission */
    if (!$thread['manage_thread']) {
      _error(403);
    }
    /* delete thread */
    $db->query(sprintf("DELETE FROM forums_threads WHERE thread_id = %s", secure($thread_id, 'int')));
    /* delete replies */
    $db->query(sprintf("DELETE FROM forums_replies WHERE thread_id = %s", secure($thread_id, 'int')));
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_threads = IF(forum_threads=0,0,forum_threads-1), forum_replies = IF(forum_replies=0,0,forum_replies-%s) WHERE forum_id = %s", secure($thread['replies'], 'int'), secure($thread['forum']['forum_id'], 'int')));
    return $thread['forum'];
  }


  /**
   * get_forum_replies
   * 
   * @param array $args
   * @return array
   */
  public function get_forum_replies($args = [])
  {
    global $db, $system, $smarty;
    /* initialize vars */
    $replies = [];
    /* initialize arguments */
    $thread = !isset($args['thread']) ? null : $args['thread'];
    $user_id = !isset($args['user_id']) ? null : $args['user_id'];
    $query = !isset($args['query']) ? null : $args['query'];
    /* get replies */
    if ($thread !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_replies WHERE thread_id = %s", secure($thread['thread_id'], 'int')));
    } elseif ($user_id !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_replies WHERE user_id = %s", secure($user_id, 'int')));
    } elseif ($query !== null) {
      /* prepare where statement */
      $where = "";
      /* query */
      $where .= (!is_empty($query)) ? sprintf(' WHERE forums_replies.text LIKE %1$s ', secure($query, 'search')) : "";
      /* forums list */
      if ($args['forums_list']) {
        $forums_list = $this->spread_ids($args['forums_list']);
        $where = ($where != '') ? $where . " AND forums_threads.forum_id IN ($forums_list) " : $where;
      }
      $get_total = $db->query("SELECT COUNT(*) as count FROM forums_replies INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id" . $where);
    }
    $total_items = $get_total->fetch_assoc()['count'];
    if ($total_items > 0) {
      require('includes/class-pager.php');
      $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
      $params['total_items'] = $total_items;
      $params['items_per_page'] = $system['max_results'] * 2;
      /* prepare pager URL */
      if ($thread !== null) {
        $params['url'] = $system['system_url'] . '/forums/thread/' . $thread['thread_id'] . '/' . $thread['title_url'] . '?page=%s';
      } elseif ($user_id !== null) {
        $params['url'] = $system['system_url'] . '/forums/my-replies?page=%s';
      } elseif ($query !== null) {
        $params['url'] = remove_querystring_var($_SERVER['REQUEST_URI'], 'page');
        $params['url'] = $params['url'] . '&page=%s';
      }
      $pager = new Pager($params);
      $limit_query = $pager->getLimitSql();
      if ($thread !== null) {
        $get_replies = $db->query(sprintf("SELECT forums_replies.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id WHERE forums_replies.thread_id = %s ORDER BY forums_replies.reply_id ASC " . $limit_query, secure($thread['thread_id'], 'int')));
      } elseif ($user_id !== null) {
        $get_replies = $db->query(sprintf("SELECT forums_replies.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id WHERE forums_replies.user_id = %s ORDER BY forums_replies.reply_id DESC " . $limit_query, secure($user_id, 'int')));
      } elseif ($query !== null) {
        $get_replies = $db->query("SELECT forums_replies.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_replies INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id INNER JOIN users ON forums_replies.user_id = users.user_id " . $where . " ORDER BY forums_replies.reply_id DESC " . $limit_query);
      }
      while ($reply = $get_replies->fetch_assoc()) {
        $reply['user_picture'] = get_picture($reply['user_picture'], $reply['user_gender']);
        $reply['user_fullname'] = ($system['show_usernames_enabled']) ? $reply['user_name'] : $reply['user_firstname'] . " " . $reply['user_lastname'];
        /* parse text */
        $reply['parsed_text'] = htmlspecialchars_decode($reply['text'], ENT_QUOTES);
        $reply['text_snippet'] = get_snippet_text($reply['text']);
        /* check if viewer can manage reply [Edit|Delete] */
        $reply['manage_reply'] = false;
        if ($this->_logged_in) {
          /* viewer is (admin|moderator) */
          if ($this->_data['user_group'] < 3) {
            $reply['manage_reply'] = true;
          }
          /* viewer is the author of reply */
          if ($this->_data['user_id'] == $reply['user_id']) {
            $reply['manage_reply'] = true;
          }
        }
        /* vote data */
        $reply['votes'] = $this->get_forum_votes($reply['reply_id'], 'reply');
        /* get thread */
        if (!$thread) {
          $reply['thread'] = $this->get_forum_thread($reply['thread_id']);
        }
        $replies[] = $reply;
      }
      /* assign variables */
      $smarty->assign('selected_page', $params['selected_page']);
      $smarty->assign('total', $params['total_items']);
      $smarty->assign('pager', $pager->getPager());
    }
    return $replies;
  }


  /**
   * get_forum_reply
   * 
   * @param integer $reply_id
   * @return array
   */
  public function get_forum_reply($reply_id)
  {
    global $db;
    $get_reply = $db->query(sprintf("SELECT * FROM forums_replies WHERE reply_id = %s", secure($reply_id, 'int')));
    if ($get_reply->num_rows == 0) {
      return false;
    }
    $reply = $get_reply->fetch_assoc();
    /* get thread */
    $reply['thread'] = $this->get_forum_thread($reply['thread_id']);
    if (!$reply['thread']) {
      return false;
    }
    /* check if viewer can manage reply [Edit|Delete] */
    $reply['manage_reply'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $reply['manage_reply'] = true;
      }
      /* viewer is the author of reply */
      if ($this->_data['user_id'] == $reply['user_id']) {
        $reply['manage_reply'] = true;
      }
    }
    return $reply;
  }


  /**
   * post_forum_reply
   * 
   * @param string $thread_id
   * @param string $text
   * @return array
   */
  public function post_forum_reply($thread_id, $text)
  {
    global $db, $system, $date;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get thread */
    $thread = $this->get_forum_thread($thread_id);
    /* check forum */
    if (!$thread) {
      _error(403);
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your reply"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $purifier = get_html_purifier();
      $clean_text = $purifier->purify($text);
    }
    /* insert reply */
    $db->query(sprintf("INSERT INTO forums_replies (thread_id, user_id, text, time) VALUES (%s, %s, %s, %s)", secure($thread_id, 'int'), secure($this->_data['user_id'], 'int'), secure($clean_text), secure($date)));
    $reply_id = $db->insert_id;
    /* update thread */
    $db->query(sprintf("UPDATE forums_threads SET last_reply = %s, replies = replies + 1 WHERE thread_id = %s", secure($date), secure($thread_id, 'int')));
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_replies = forum_replies + 1 WHERE forum_id = %s", secure($thread['forum']['forum_id'], 'int')));
    /* post notification */
    $this->post_notification(['to_user_id' => $thread['user_id'], 'action' => 'forum_reply', 'node_url' => $thread['thread_id'] . '/' . $thread['title_url'] . '/#reply-' . $reply_id]);
    /* extract hosted images from the text */
    $uploaded_images = extract_uploaded_images_from_text($clean_text);
    /* remove pending uploads */
    if ($uploaded_images) {
      remove_pending_uploads($uploaded_images);
    }
    return ['reply_id' => $reply_id, 'thread' => $thread];
  }


  /**
   * edit_forum_reply
   * 
   * @param string $reply_id
   * @param string $text
   * @return array
   */
  public function edit_forum_reply($reply_id, $text)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get reply */
    $reply = $this->get_forum_reply($reply_id);
    /* check reply */
    if (!$reply) {
      _error(403);
    }
    /* check permission */
    if (!$reply['manage_reply']) {
      _error(403);
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your thread"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $purifier = get_html_purifier();
      $clean_text = $purifier->purify($text);
    }
    /* edit reply */
    $db->query(sprintf("UPDATE forums_replies SET text = %s WHERE reply_id = %s", secure($clean_text), secure($reply_id, 'int')));
    /* extract hosted images from the text */
    $uploaded_images = extract_uploaded_images_from_text($clean_text);
    /* remove pending uploads */
    if ($uploaded_images) {
      remove_pending_uploads($uploaded_images);
    }
    return ['reply_id' => $reply_id, 'thread' => $reply['thread']];
  }


  /**
   * delete_forum_reply
   * 
   * @param string $reply_id
   * @return void
   */
  public function delete_forum_reply($reply_id)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get reply */
    $reply = $this->get_forum_reply($reply_id);
    /* check reply */
    if (!$reply) {
      _error(403);
    }
    /* check permission */
    if (!$reply['manage_reply']) {
      _error(403);
    }
    /* delete reply */
    $db->query(sprintf("DELETE FROM  forums_replies WHERE reply_id = %s", secure($reply_id, 'int')));
    /* update thread */
    $db->query(sprintf("UPDATE forums_threads SET replies = IF(replies=0,0,replies-1) WHERE thread_id = %s", secure($reply['thread_id'], 'int')));
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_replies = IF(forum_replies=0,0,forum_replies-1) WHERE forum_id = %s", secure($reply['thread']['forum']['forum_id'], 'int')));
    /* delete notification */
    $this->delete_notification($reply['thread']['user_id'], 'forum_reply', '', $reply['thread']['thread_id'] . '/' . $reply['thread']['title_url'] . '/#reply-' . $reply_id);
  }


  /**
   * search_forums
   * 
   * @param string $query
   * @param string $type
   * @param mixed $forum
   * @param boolean $recursive
   * @return array
   */
  public function search_forums($query, $type, $forum, $recursive)
  {
    global $db, $system;
    /* init vars */
    $results = [];
    $params = [];
    /* check recursive */
    $recursive = (isset($recursive)) ? true : false;
    /* check forum */
    if ($forum == "all") {
      $params['forums_list'] = false;
    } else {
      $forum = $this->get_forum($forum);
      if (!$forum) {
        return $results;
      }
      $params['forums_list'][] = $forum['forum_id'];
      if ($recursive) {
        $this->get_child_forums_ids($forum['childs'], $params['forums_list']);
      }
    }
    /* include query */
    $params['query'] = $query;
    /* validate type */
    switch ($type) {
      case 'threads':
        $results = $this->get_forum_threads($params);
        break;

      case 'replies':
        $results = $this->get_forum_replies($params);
        break;
    }
    return $results;
  }


  /**
   * get_forum_online_users
   * 
   * @return array
   */
  public function get_forum_online_users()
  {
    global $db, $system;
    $users = [];
    $get_users = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s)) AND user_chat_enabled = '1'", secure($system['offline_time'], 'int', false)));
    if ($get_users->num_rows > 0) {
      while ($user = $get_users->fetch_assoc()) {
        $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
        $users[] = $user;
      }
    }
    return $users;
  }


  /* ------------------------------- */
  /* Forum Votes                     */
  /* ------------------------------- */

  /**
   * get_forum_votes
   * Returns vote counts + viewer's own vote for a single item.
   */
  public function get_forum_votes($item_id, $item_type)
  {
    global $db;
    $result = ['upvotes' => 0, 'downvotes' => 0, 'score' => 0, 'my_vote' => null];

    $counts = $db->query(sprintf(
      "SELECT SUM(vote_type='up') AS upvotes, SUM(vote_type='down') AS downvotes
       FROM forums_votes WHERE item_id = %s AND item_type = %s",
      secure($item_id, 'int'), secure($item_type)
    ));
    if ($counts && $counts->num_rows > 0) {
      $row = $counts->fetch_assoc();
      $result['upvotes']   = (int) $row['upvotes'];
      $result['downvotes'] = (int) $row['downvotes'];
      $result['score']     = $result['upvotes'] - $result['downvotes'];
    }
    if ($this->_logged_in) {
      $my = $db->query(sprintf(
        "SELECT vote_type FROM forums_votes WHERE user_id = %s AND item_id = %s AND item_type = %s",
        secure($this->_data['user_id'], 'int'), secure($item_id, 'int'), secure($item_type)
      ));
      if ($my && $my->num_rows > 0) {
        $result['my_vote'] = $my->fetch_assoc()['vote_type'];
      }
    }
    return $result;
  }

  /**
   * ensure_forums_votes_table
   * Auto-creates forums_votes if it doesn't exist yet.
   */
  public function ensure_forums_votes_table()
  {
    global $db;
    $db->query("CREATE TABLE IF NOT EXISTS `forums_votes` (
      `vote_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(10) UNSIGNED NOT NULL,
      `item_id` int(10) UNSIGNED NOT NULL,
      `item_type` enum('thread','reply') NOT NULL DEFAULT 'thread',
      `vote_type` enum('up','down') NOT NULL DEFAULT 'up',
      `time` datetime NOT NULL,
      PRIMARY KEY (`vote_id`),
      UNIQUE KEY `unique_vote` (`user_id`,`item_id`,`item_type`),
      KEY `idx_item` (`item_id`,`item_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC");
  }


  /* ------------------------------- */
  /* Best Answer (Quora style)       */
  /* ------------------------------- */

  /**
   * mark_best_answer
   * Thread author marks a reply as the accepted/best answer.
   */
  public function mark_best_answer(int $thread_id, int $reply_id): bool
  {
    global $db;
    $thread = $this->get_forum_thread($thread_id);
    if (!$thread) return false;
    /* only thread author or admin/mod can mark best answer */
    if ($this->_data['user_id'] != $thread['user_id'] && $this->_data['user_group'] >= 3) {
      return false;
    }
    /* ensure best_reply_id column exists */
    $db->query("ALTER TABLE forums_threads ADD COLUMN IF NOT EXISTS `best_reply_id` INT UNSIGNED NULL DEFAULT NULL");
    $db->query("ALTER TABLE forums_threads ADD COLUMN IF NOT EXISTS `solved` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0");
    /* if already set to same reply → unmark (toggle) */
    if ((int)$thread['best_reply_id'] === $reply_id) {
      $db->query(sprintf("UPDATE forums_threads SET best_reply_id = NULL, solved = 0 WHERE thread_id = %s", secure($thread_id, 'int')));
    } else {
      $db->query(sprintf("UPDATE forums_threads SET best_reply_id = %s, solved = 1 WHERE thread_id = %s", secure($reply_id, 'int'), secure($thread_id, 'int')));
    }
    return true;
  }


  /* ------------------------------- */
  /* Tags / Topics                   */
  /* ------------------------------- */

  /**
   * get_all_tags
   * Returns popular topic tags from the forums_tags table (auto-created).
   */
  public function get_all_tags(int $limit = 20): array
  {
    global $db;
    $db->query("CREATE TABLE IF NOT EXISTS `forums_tags` (
      `tag_id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `tag_name`      VARCHAR(100) NOT NULL,
      `tag_slug`      VARCHAR(100) NOT NULL,
      `tag_desc`      TEXT,
      `tag_color`     VARCHAR(7) NOT NULL DEFAULT '#6366f1',
      `tag_posts`     INT UNSIGNED NOT NULL DEFAULT 0,
      `tag_followers` INT UNSIGNED NOT NULL DEFAULT 0,
      PRIMARY KEY (`tag_id`),
      UNIQUE KEY `unique_slug` (`tag_slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC");
    $result = $db->query("SELECT * FROM forums_tags ORDER BY tag_posts DESC LIMIT " . (int)$limit);
    $tags = [];
    if ($result && $result->num_rows > 0) {
      while ($t = $result->fetch_assoc()) $tags[] = $t;
    }
    return $tags;
  }
}
