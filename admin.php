<?php

/**
 * admin
 * 
 * @package Sngine
 * @author Zamblek
 */

// set override_shutdown
$override_shutdown = true;

// fetch bootloader
require('bootloader.php');

// user access
user_access();

// check admin|moderator permission
if (!$user->_is_admin && !$user->_is_moderator) {
  _error(__('System Message'), __("You don't have the right permission to access this"));
}

// check moderator mode
if ($user->_is_moderator && !$moderator_mode) {
  /* moderator try to access admin panel */
  _error(__('System Message'), __("You don't have the right permission to access this"));
}

try {

  // get view content
  switch ($_GET['view']) {
    case '':
      // update view
      $_GET['view'] = 'dashboard';

      // page header
      page_header($control_panel['title'] . " " . __("Panel"));

      // get insights
      $insights = [];
      /* total users */
      $get_users = $db->query("SELECT COUNT(*) as count FROM users");
      $insights['users'] = $get_users->fetch_assoc()['count'];
      /* pending */
      $get_pending = $db->query("SELECT COUNT(*) as count FROM users WHERE user_approved = '0'");
      $insights['pending'] = $get_pending->fetch_assoc()['count'];
      /* not activated */
      $get_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
      $insights['not_activated'] = $get_not_activated->fetch_assoc()['count'];
      /* banned */
      $get_banned = $db->query("SELECT COUNT(*) as count FROM users WHERE user_banned = '1'");
      $insights['banned'] = $get_banned->fetch_assoc()['count'];
      /* online */
      $get_online = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($system['offline_time'], 'int', false)));
      $insights['online'] = $get_online->fetch_assoc()['count'];
      /* get total vistors (log_sessions) */
      $get_vistors = $db->query("SELECT COUNT(*) as count FROM log_sessions");
      $insights['vistors'] = $get_vistors->fetch_assoc()['count'];
      /* get total visitors today (log_sessions) */
      $get_vistors_today = $db->query("SELECT COUNT(*) as count FROM log_sessions WHERE DATE(session_date) = CURDATE()");
      $insights['vistors_today'] = $get_vistors_today->fetch_assoc()['count'];
      /* get total visitors this month (log_sessions) */
      $get_vistors_month = $db->query("SELECT COUNT(*) as count FROM log_sessions WHERE YEAR(session_date) = YEAR(CURRENT_DATE()) AND MONTH(session_date) = MONTH(CURRENT_DATE())");
      $insights['vistors_month'] = $get_vistors_month->fetch_assoc()['count'];
      /* posts */
      $get_posts = $db->query("SELECT COUNT(*) as count FROM posts");
      $insights['posts'] = $get_posts->fetch_assoc()['count'];
      /* comments */
      $get_comments = $db->query("SELECT COUNT(*) as count FROM posts_comments");
      $insights['comments'] = $get_comments->fetch_assoc()['count'];
      /* pages */
      $get_pages = $db->query("SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id");
      $insights['pages'] = $get_pages->fetch_assoc()['count'];
      /* groups */
      $get_groups = $db->query("SELECT COUNT(*) as count FROM `groups`");
      $insights['groups'] = $get_groups->fetch_assoc()['count'];
      /* events */
      $get_events = $db->query("SELECT COUNT(*) as count FROM `events`");
      $insights['events'] = $get_events->fetch_assoc()['count'];
      /* messages */
      $get_messages = $db->query("SELECT COUNT(*) as count FROM conversations_messages");
      $insights['messages'] = $get_messages->fetch_assoc()['count'];
      /* notifications */
      $get_notifications = $db->query("SELECT COUNT(*) as count FROM notifications");
      $insights['notifications'] = $get_notifications->fetch_assoc()['count'];

      // get chart data
      for ($i = 1; $i <= 12; $i++) {
        /* get users */
        $get_monthly_users = $db->query("SELECT COUNT(*) as count FROM users WHERE YEAR(user_registered) = YEAR(CURRENT_DATE()) AND MONTH(user_registered) = $i");
        $chart['users'][$i] = $get_monthly_users->fetch_assoc()['count'];
        /* get pages */
        $get_monthly_pages = $db->query("SELECT COUNT(*) as count FROM pages WHERE YEAR(page_date) = YEAR(CURRENT_DATE()) AND MONTH(page_date) = $i");
        $chart['pages'][$i] = $get_monthly_pages->fetch_assoc()['count'];
        /* get groups */
        $get_monthly_groups = $db->query("SELECT COUNT(*) as count FROM `groups` WHERE YEAR(group_date) = YEAR(CURRENT_DATE()) AND MONTH(group_date) = $i");
        $chart['groups'][$i] = $get_monthly_groups->fetch_assoc()['count'];
        /* get events */
        $get_monthly_events = $db->query("SELECT COUNT(*) as count FROM `events` WHERE YEAR(event_date) = YEAR(CURRENT_DATE()) AND MONTH(event_date) = $i");
        $chart['events'][$i] = $get_monthly_events->fetch_assoc()['count'];
        /* get posts */
        $get_monthly_posts = $db->query("SELECT COUNT(*) as count FROM posts WHERE YEAR(time) = YEAR(CURRENT_DATE()) AND MONTH(time) = $i");
        $chart['posts'][$i] = $get_monthly_posts->fetch_assoc()['count'];
      }

      // assign variables
      $smarty->assign('insights', $insights);
      $smarty->assign('chart', $chart);
      break;

    case 'settings':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("System Settings"));

          // get currencies
          $smarty->assign('system_currencies', $user->get_currencies());
          break;

        case 'posts':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts Settings"));
          break;

        case 'ai':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("AI Settings"));
          break;

        case 'registration':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Registration Settings"));

          // get users groups
          $smarty->assign('user_groups', $user->get_users_groups());
          break;

        case 'accounts':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Accounts Settings"));
          break;

        case 'email':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Email Settings"));
          break;

        case 'sms':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("SMS Settings"));
          break;

        case 'notifications':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Notifications Settings"));
          break;

        case 'chat':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Chat Settings"));
          break;

        case 'live':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Live Stream Settings"));
          break;

        case 'uploads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Uploads Settings"));

          // get PHP upload_max_filesize
          $max_upload_size = ini_get("upload_max_filesize");
          /* assign variables */
          $smarty->assign('max_upload_size', $max_upload_size);
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Payments Settings"));
          break;

        case 'security':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Security Settings"));
          break;

        case 'limits':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Limits Settings"));
          break;

        case 'analytics':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Analytics Settings"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'themes':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Themes"));

          // get data
          $get_rows = $db->query("SELECT * FROM system_themes");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_themes WHERE theme_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Themes") . " &rsaquo; " . $data['name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Themes") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'design':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Design"));
      break;

    case 'languages':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Languages"));

          // get data
          $get_rows = $db->query("SELECT * FROM system_languages");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['flag'] = get_picture($row['flag'], 'flag');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_languages WHERE language_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Languages") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Languages") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'countries':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Countries"));

          // get data
          $get_rows = $db->query("SELECT * FROM system_countries");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_countries WHERE country_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Countries") . " &rsaquo; " . $data['country_name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Countries") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'currencies':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Currencies"));

          // get data
          $rows = $user->get_currencies(true);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_currencies WHERE currency_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Currencies") . " &rsaquo; " . $data['name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Currencies") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'genders':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Genders"));

          // get data
          $rows = $user->get_genders(false);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_genders WHERE gender_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Genders") . " &rsaquo; " . $data['gender_name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Genders") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'users':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_users_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users"));

          // check cug
          $cug_where_query = "";
          $cug_where_and_query = "";
          $query_string = "";
          if (isset($_GET['cug'])) {
            $cug = $user->get_user_group($_GET['cug']);
            if (!$cug) {
              _error(404);
            }
            $cug_where_query = " WHERE user_group_custom = " . secure($_GET['cug'], 'int') . " ";
            $cug_where_and_query = " AND user_group_custom =  " . secure($_GET['cug'], 'int') . " ";
            $query_string = "&cug=" . $_GET['cug'];
            $smarty->assign('cug', $cug);
          } elseif (isset($_GET['ncug'])) {
            $cug_where_query = " WHERE user_group = '3' AND user_group_custom = '0' ";
            $cug_where_and_query = " AND user_group = '3' AND user_group_custom =  '0' ";
            $query_string = "&ncug=true";
            $smarty->assign('ncug', true);
          }

          // get insights
          $insights = [];
          /* total users */
          $get_users = $db->query("SELECT COUNT(*) as count FROM users" . $cug_where_query);
          $insights['users'] = $get_users->fetch_assoc()['count'];
          $get_pending = $db->query("SELECT COUNT(*) as count FROM users WHERE user_approved = '0'" . $cug_where_and_query);
          $insights['pending'] = $get_pending->fetch_assoc()['count'];
          $get_banned = $db->query("SELECT COUNT(*) as count FROM users WHERE user_banned = '1'" . $cug_where_and_query);
          $insights['banned'] = $get_banned->fetch_assoc()['count'];
          $get_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'" . $cug_where_and_query);
          $insights['not_activated'] = $get_not_activated->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users" . $cug_where_query);
          $params['total_items'] = $insights['users'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users?page=%s' . $query_string;
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users " . $cug_where_query . "ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'admins':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Admins"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '1'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/admins?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_group = '1' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'moderators':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Moderators"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '2'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/moderators?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_group = '2' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'online':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Online"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($system['offline_time'], 'int', false)));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/online?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf("SELECT * FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s)) ORDER BY user_id ASC " . $limit_query, secure($system['offline_time'], 'int', false)));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'banned':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Banned"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_banned = '1'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/banned?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_banned = '1' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'not_activated':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Not Activated"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/not_activated?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_activated = '0' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'pending':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Pending"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_approved = '0'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/pending?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_approved = '0' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'stats':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Stats"));

          // prepare where query
          $where_query = "";
          $query_string = "";
          /* check from & to */
          $from = (isset($_GET['from'])) ? date('Y-m-d', strtotime($_GET['from'])) : null;
          $to = (isset($_GET['to'])) ? date('Y-m-d', strtotime($_GET['to'])) : null;
          if ($from && $to) {
            $where_query = sprintf(" AND (DATE(posts.time) BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
            $query_string = "&from=" . $_GET['from'] . "&to=" . $_GET['to'];
          }
          /* check query */
          if (isset($_GET['query'])) {
            $where_users_query = sprintf(' WHERE (users.user_name LIKE %1$s OR users.user_firstname LIKE %1$s OR users.user_lastname LIKE %1$s OR CONCAT(users.user_firstname,  " ", users.user_lastname) LIKE %1$s OR users.user_email LIKE %1$s OR users.user_phone LIKE %1$s)', secure($_GET['query'], 'search'));
            $query_string .= "&query=" . $_GET['query'];
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/stats?page=%s' . $query_string;
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT users.*, (SELECT COUNT(*) FROM posts WHERE posts.user_id = users.user_id AND posts.user_type = 'user' " . $where_query . ") AS posts_count FROM users " . $where_users_query . " ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          $smarty->assign('from', $from);
          $smarty->assign('to', $to);
          $smarty->assign('query', ($_GET['query']) ? htmlentities($_GET['query'], ENT_QUOTES, 'utf-8') : null);
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM users WHERE (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s OR user_email LIKE %1$s OR user_phone LIKE %1$s) ORDER BY user_firstname ASC', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT * FROM users WHERE (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s OR user_email LIKE %1$s OR user_phone LIKE %1$s) ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM users LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id WHERE users.user_id = %s ", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get user full name */
          $data['user_fullname'] = ($system['show_usernames_enabled']) ? $data['user_name'] : $data['user_firstname'] . " " . $data['user_lastname'];
          /* get users groups */
          $data['user_groups'] = $user->get_users_groups();
          /* get user picture */
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get user's connections */
          $data['friends'] = $user->get_friends_count($data['user_id']);
          $data['followings'] = $user->get_followings_count($data['user_id']);
          $data['followers'] = $user->get_followers_count($data['user_id']);
          /* get posts count */
          $data['posts_count'] = $user->get_posts_count($data['user_id'], 'user');
          /* check if user online */
          $data['is_online'] = $user->user_online($data['user_id']);
          /* get user referrer */
          $data['user_referrer'] = $user->get_user($data['user_referrer_id']);
          /* parse birthdate */
          $data['user_birthdate_parsed'] = ($data['user_birthdate']) ? date_parse($data['user_birthdate']) : null;
          /* get user sessions */
          $get_sessions = $db->query(sprintf("SELECT * FROM users_sessions WHERE user_id = %s", secure($_GET['id'], 'int')));
          if ($get_sessions->num_rows > 0) {
            while ($session = $get_sessions->fetch_assoc()) {
              $data['sessions'][] = $session;
            }
          }
          /* prepare packages */
          if ($system['packages_enabled']) {
            /* prepare user package */
            if ($data['user_subscribed']) {
              switch ($data['period']) {
                case 'day':
                  $duration = 86400;
                  break;

                case 'week':
                  $duration = 604800;
                  break;

                case 'month':
                  $duration = 2629743;
                  break;

                case 'year':
                  $duration = 31556926;
                  break;
              }
              $data['subscription_end'] = strtotime($data['user_subscription_date']) + ($data['period_num'] * $duration);
              $data['subscription_timeleft'] = ceil(($data['subscription_end'] - time()) / (60 * 60 * 24));
            }
            /* get packages */
            $packages = method_exists($user, 'get_packages') ? $user->get_packages(true) : [];
            $smarty->assign('packages', $packages);
          }
          /* prepare monetization */
          $data['can_monetize_content'] = $system['monetization_enabled'] && $user->check_user_permission($data['user_id'], 'monetization_permission');
          // get monetozaion plans
          if ($data['can_monetize_content'] && method_exists($user, 'get_monetization_plans')) {
            $smarty->assign('monetization_plans', $user->get_monetization_plans($data['user_id']));
          }
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "user", "get" => "settings", "node_id" => $_GET['id']]));

          // get genders
          $smarty->assign('genders',  $user->get_genders());

          // get countries
          if (!$countries) {
            $smarty->assign('countries', $user->get_countries());
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . $data['user_firstname'] . " " . $data['user_lastname']);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'users_groups':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users Groups"));

          // get data
          /* get system groups counts */
          /* get admins count */
          $get_admins = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '1'");
          $counters['admins_count'] = $get_admins->fetch_assoc()['count'];
          /* get moderators count */
          $get_moderators = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '2'");
          $counters['moderators_count'] = $get_moderators->fetch_assoc()['count'];
          /* get users count */
          $get_users = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '3' AND user_group_custom = '0'");
          $counters['users_count'] = $get_users->fetch_assoc()['count'];
          /* get custom groups */
          $rows = $user->get_users_groups();

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('counters', $counters);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $data = $user->get_user_group($_GET['id']);
          if (!$data) {
            _error(404);
          }
          /* get permissions groups */
          $data['permissions_groups'] = $user->get_permissions_groups();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users Groups") . " &rsaquo; " . $data['user_group_title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users Groups") . " &rsaquo; " . __("Add New"));

          // get permissions groups
          $smarty->assign('permissions_groups', $user->get_permissions_groups());
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'permissions_groups':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Permissions Groups"));

          // get data
          $rows = $user->get_permissions_groups();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || (!in_array($_GET['id'], ['mods', 'users', 'verified']) && !is_numeric($_GET['id']))) {
            _error(404);
          }
          if ($_GET['id'] == "mods") {
            $data['edit_mods'] = true;
            $data['permissions_group_title'] = __("Moderators");
          } else {
            if ($_GET['id'] == "users") {
              $_GET['id'] = '1';
            } elseif ($_GET['id'] == "verified") {
              $_GET['id'] = '2';
            }

            // get data
            $data = $user->get_permissions_group($_GET['id']);
            if (!$data) {
              _error(404);
            }
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Permissions Groups") . " &rsaquo; " . $data['permissions_group_title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Permissions Groups") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'posts':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_posts_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts"));

          // get insights
          $insights = [];
          $get_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '1' OR posts.has_approved = '1') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['posts'] = $get_posts->fetch_assoc()['count'];
          $get_pending_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '0' AND posts.has_approved = '0') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['pending_posts'] = $get_pending_posts->fetch_assoc()['count'];
          $get_posts_comments = $db->query("SELECT COUNT(*) as count FROM posts_comments");
          $insights['posts_comments'] = $get_posts_comments->fetch_assoc()['count'];
          $get_posts_likes = $db->query("SELECT COUNT(*) as count FROM posts_reactions");
          $insights['posts_likes'] = $get_posts_likes->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $params['total_items'] = $insights['posts'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '1' OR posts.has_approved = '1') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND (posts.post_id = %1$s OR posts.text LIKE %2$s)', secure($_GET['query'], 'int'), secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND (posts.post_id = %1$s OR posts.text LIKE %2$s) ' . $limit_query, secure($_GET['query'], 'int'), secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'pending':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Pending"));

          // get insights
          $insights = [];
          $get_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '1' OR posts.has_approved = '1') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['posts'] = $get_posts->fetch_assoc()['count'];
          $get_pending_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '0' AND posts.has_approved = '0') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['pending_posts'] = $get_pending_posts->fetch_assoc()['count'];
          $get_posts_comments = $db->query("SELECT COUNT(*) as count FROM posts_comments");
          $insights['posts_comments'] = $get_posts_comments->fetch_assoc()['count'];
          $get_posts_likes = $db->query("SELECT COUNT(*) as count FROM posts_reactions");
          $insights['posts_likes'] = $get_posts_likes->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $params['total_items'] = $insights['pending_posts'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts/pending?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '0' AND posts.has_approved = '0') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'videos_categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Videos Categories"));

          // get data
          $rows = $user->get_categories("posts_videos_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_videos_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM posts_videos_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("posts_videos_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Videos Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_videos_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Videos Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("posts_videos_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'pages':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_pages_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages"));

          // get insights
          $insights = [];
          $get_pages = $db->query("SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id");
          $insights['pages'] = $get_pages->fetch_assoc()['count'];
          $get_pages_verified = $db->query("SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_verified = '1'");
          $insights['pages_verified'] = $get_pages_verified->fetch_assoc()['count'];
          $get_pages_likes = $db->query("SELECT COUNT(*) as count FROM pages_likes INNER JOIN users ON pages_likes.user_id = users.user_id INNER JOIN pages ON pages_likes.page_id = pages.page_id");
          $insights['pages_likes'] = $get_pages_likes->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $params['total_items'] = $insights['pages'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/pages?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT pages.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM pages INNER JOIN users ON pages.page_admin = users.user_id ORDER BY pages.page_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_name LIKE %1$s OR pages.page_title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/pages/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT pages.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_name LIKE %1$s OR pages.page_title LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit_page':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT pages.*, users.* FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          $data['page_picture'] = get_picture($data['page_picture'], 'page');
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get categories */
          $data['categories'] = $user->get_categories("pages_categories");
          /* get countries */
          $data['countries'] = (!$countries) ? $user->get_countries() : $countries;
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "page", "get" => "settings", "node_id" => $_GET['id']]));
          /* prepare monetization */
          $data['can_monetize_content'] = $system['monetization_enabled'] && $user->check_user_permission($data['page_admin'], 'monetization_permission');
          // get monetozaion plans
          if ($data['can_monetize_content'] && method_exists($user, 'get_monetization_plans')) {
            $smarty->assign('monetization_plans', $user->get_monetization_plans($data['page_id'], 'page'));
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . $data['page_title']);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("pages_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM pages_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("pages_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("pages_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'groups':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_groups_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/groups?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT `groups`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id ORDER BY `groups`.group_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['group_picture'] = get_picture($row['group_picture'], 'group');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id WHERE `groups`.group_name LIKE %1$s OR `groups`.group_title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/groups/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT `groups`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id WHERE `groups`.group_name LIKE %1$s OR `groups`.group_title LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['group_picture'] = get_picture($row['group_picture'], 'group');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit_group':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT `groups`.*, users.* FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id WHERE `groups`.group_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          $data['group_picture'] = get_picture($data['group_picture'], 'group');
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get categories */
          $data['categories'] = $user->get_categories("groups_categories");
          /* get countries */
          $data['countries'] = (!$countries) ? $user->get_countries() : $countries;
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "group", "get" => "settings", "node_id" => $_GET['id']]));
          /* prepare monetization */
          $data['can_monetize_content'] = $system['monetization_enabled'] && $user->check_user_permission($data['group_admin'], 'monetization_permission');
          if ($data['can_monetize_content'] && method_exists($user, 'get_monetization_plans')) {
            // get monetozaion plans
            $smarty->assign('monetization_plans', $user->get_monetization_plans($data['group_id'], 'group'));
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . $data['group_title']);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("groups_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM groups_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("groups_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("groups_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'events':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_events_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/events?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT `events`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id ORDER BY `events`.event_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id WHERE `events`.event_title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/events/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT `events`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id WHERE `events`.event_title LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit_event':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT `events`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id WHERE `events`.event_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          $data['event_picture'] = get_picture($data['event_cover'], 'event');
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get categories */
          $data['categories'] = $user->get_categories("events_categories");
          /* get countries */
          $data['countries'] = (!$countries) ? $user->get_countries() : $countries;
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "event", "get" => "settings", "node_id" => $_GET['id']]));

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . $data['event_title']);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("events_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM events_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("events_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("events_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'blogs':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_blogs_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = 'article' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/blogs?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_articles.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = 'article' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['blog_title_url'] = get_url_text($row['title']);
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['blog_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['blog_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['blog_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['blog_author_picture'] = get_picture($row['page_picture'], "page");
                $row['blog_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['blog_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = "article" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_articles.title LIKE %1$s OR posts_articles.text LIKE %1$s OR posts_articles.tags LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/blogs/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_articles.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = "article" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_articles.title LIKE %1$s OR posts_articles.text LIKE %1$s OR posts_articles.tags LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['blog_title_url'] = get_url_text($row['title']);
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['blog_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['blog_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['blog_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['blog_author_picture'] = get_picture($row['page_picture'], "page");
                $row['blog_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['blog_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("blogs_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM blogs_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("blogs_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("blogs_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'offers':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_offers_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = 'offer' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/offers?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_offers.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = 'offer' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['offer_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['offer_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['offer_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['offer_author_picture'] = get_picture($row['page_picture'], "page");
                $row['offer_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['offer_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = "offer" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_offers.title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/offers/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_offers.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = "offer" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_offers.title LIKE %1$s' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['offer_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['offer_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['offer_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['offer_author_picture'] = get_picture($row['page_picture'], "page");
                $row['offer_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['offer_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("offers_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM offers_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("offers_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("offers_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'jobs':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_jobs_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = 'job' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/jobs?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_jobs.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = 'job' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['job_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['job_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['job_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['job_author_picture'] = get_picture($row['page_picture'], "page");
                $row['job_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['job_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = "job" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_jobs.title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/jobs/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_jobs.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = "job" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_jobs.title LIKE %1$s' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['job_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['job_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['job_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['job_author_picture'] = get_picture($row['page_picture'], "page");
                $row['job_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['job_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("jobs_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM jobs_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("jobs_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("jobs_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'courses':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_courses_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Courses"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_courses ON posts.post_id = posts_courses.post_id AND posts.post_type = 'course' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/courses?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_courses.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_courses ON posts.post_id = posts_courses.post_id AND posts.post_type = 'course' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['course_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['course_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['course_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['course_author_picture'] = get_picture($row['page_picture'], "page");
                $row['course_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['course_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Courses") . " &rsaquo; " . __("Find"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_courses ON posts.post_id = posts_courses.post_id AND posts.post_type = "course" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_courses.title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/courses/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_courses.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_courses ON posts.post_id = posts_courses.post_id AND posts.post_type = "course" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_courses.title LIKE %1$s' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['course_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['course_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['course_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['course_author_picture'] = get_picture($row['page_picture'], "page");
                $row['course_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['course_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Courses") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("courses_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM courses_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("courses_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Courses") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Courses") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("courses_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'forums':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_forums_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums"));

          // get data
          $rows = $user->get_forums();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_forum':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $data = $user->get_forum($_GET['id']);
          if (!$data) {
            _error(404);
          }
          /* get sections */
          $data['sections'] = $user->get_forums();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . $data['forum_name']);
          break;

        case 'add_forum':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Add New Forum"));

          // get data
          $forums = $user->get_forums();

          // assign variables
          $smarty->assign('forums', $forums);
          break;

        case 'threads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Threads"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/threads?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT forums_threads.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id ORDER BY forums_threads.thread_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['thread_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['thread_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['thread_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find_threads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Threads"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.title LIKE %1$s OR forums_threads.text LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/find_threads?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT forums_threads.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.title LIKE %1$s OR forums_threads.text LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['thread_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['thread_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['thread_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'replies':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Replies"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/replies?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT forums_replies.*, forums_threads.title, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id ORDER BY forums_replies.reply_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['reply_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['reply_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['reply_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find_replies':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Replies"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id WHERE forums_replies.text LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/find_replies?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT forums_replies.*, forums_threads.title, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id WHERE forums_replies.text LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['reply_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['reply_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['reply_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'movies':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_movies_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies"));

          // get data
          $get_rows = $db->query("SELECT * FROM movies");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['movie_url'] = get_url_text($row['title']);
              $row['poster'] = get_picture($row['poster'], 'movie');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_movie':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM movies WHERE movie_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get movie genres */
          $data['genres'] = ($data['genres']) ? explode(',', $data['genres']) : [];

          /* get genres */
          $data['movies_genres'] = $user->get_movies_genres();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . $data['title']);
          break;

        case 'add_movie':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Add New Movie"));

          // get data
          $genres = $user->get_movies_genres();

          // assign variables
          $smarty->assign('genres', $genres);
          break;

        case 'genres':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Genres"));

          // get data
          $rows = $user->get_movies_genres(false);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_genre':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM movies_genres WHERE genre_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Genres") . " &rsaquo; " . $data['genre_name']);
          break;

        case 'add_genre':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Genres") . " &rsaquo; " . __("Add New Genre"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'games':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_games_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games"));

          // get data
          $get_rows = $db->query("SELECT * FROM games");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['title_url'] = get_url_text($row['title']);
              $row['thumbnail'] = get_picture($row['thumbnail'], 'game');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM games WHERE game_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get game genres */
          $data['genres'] = ($data['genres']) ? explode(',', $data['genres']) : [];

          /* get genres */
          $data['games_genres'] = $user->get_games_genres();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Add New"));

          // get data
          $genres = $user->get_games_genres();

          // assign variables
          $smarty->assign(
            'genres',
            $genres
          );
          break;

        case 'genres':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Genres"));

          // get data
          $rows = $user->get_games_genres(false);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_genre':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM games_genres WHERE genre_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Genres") . " &rsaquo; " . $data['genre_name']);
          break;

        case 'add_genre':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Genres") . " &rsaquo; " . __("Add New Genre"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'earnings':
    case 'wallet':
    case 'pro':
    case 'paid_modules':
    case 'funding':
    case 'monetization':
    case 'tips':
    case 'coinpayments':
    case 'bank':
      if ($licence_monetization && is_file(ABSPATH . 'includes/admin-monetization.php')) {
        require_once ABSPATH . 'includes/admin-monetization.php';
        admin_monetization_view();
      } else {
        page_header($control_panel['title']);
      }
      break;

    case 'ads':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_ads_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("Settings"));
          break;

        case 'users_ads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("Users Ads"));

          if ($licence_monetization && is_file(ABSPATH . 'includes/admin-monetization.php')) {
            require_once ABSPATH . 'includes/admin-monetization.php';
            admin_monetization_users_ads();
          }
          break;

        case 'system_ads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("System Ads"));

          // get data
          $get_rows = $db->query("SELECT * FROM ads_system");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM ads_system WHERE ads_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'affiliates':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_affiliates_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Affiliates"));
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Affiliates") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT affiliates_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM affiliates_payments INNER JOIN users ON affiliates_payments.user_id = users.user_id WHERE affiliates_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['affiliate_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'points':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_points_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Points System"));
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Points System") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT points_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM points_payments INNER JOIN users ON points_payments.user_id = users.user_id WHERE points_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['points_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'market':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_marketplace_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace"));
          break;

        case 'products':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Products"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = 'product' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/products?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_products.name, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = 'product' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Products") . " &rsaquo; " . __("Find"));

          // valid inputs
          if (!isset($_GET['query']) || is_empty($_GET['query'])) {
            _error(404);
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = "product" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_products.name LIKE %1$s OR posts_products.location LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_products.name, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = "product" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_products.name LIKE %1$s OR posts_products.location LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'orders':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Orders"));

          if (!licence_has_entitlement('monetization')) {
            break;
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/orders?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT orders.*, u1.user_name AS buyer_username, u1.user_firstname AS buyer_firstname, u1.user_lastname AS buyer_lastname, u1.user_picture AS buyer_user_picture, u1.user_gender AS buyer_user_gender, u2.user_name AS seller_username, u2.user_firstname AS seller_firstname, u2.user_lastname AS seller_lastname, u2.user_picture AS seller_user_picture, u2.user_gender AS seller_user_gender FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id ORDER BY orders.order_id DESC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* prepare buyer's info */
              $row['buyer_fullname'] = ($system['show_usernames_enabled']) ? $row['buyer_username'] : $row['buyer_firstname'] . ' ' . $row['buyer_lastname'];
              $row['buyer_picture'] = get_picture($row['buyer_user_picture'], $row['buyer_user_gender']);
              /* prepare seller's info */
              $row['seller_fullname'] = ($system['show_usernames_enabled']) ? $row['seller_username'] : $row['seller_firstname'] . ' ' . $row['seller_lastname'];
              $row['seller_picture'] = get_picture($row['seller_user_picture'], $row['seller_user_gender']);
              /* prepare total commission */
              $row['total_commission'] = $row['sub_total'] * ($row['commission'] / 100);
              /* prepare final price */
              $row['final_price'] = $row['sub_total'] - $row['total_commission'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'find_orders':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Orders"));

          if (!licence_has_entitlement('monetization')) {
            break;
          }

          // valid inputs
          if (!isset($_GET['query']) || is_empty($_GET['query'])) {
            _error(404);
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id WHERE orders.order_hash LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/find_orders?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT orders.*, u1.user_name AS buyer_username, u1.user_firstname AS buyer_firstname, u1.user_lastname AS buyer_lastname, u1.user_picture AS buyer_user_picture, u1.user_gender AS buyer_user_gender, u2.user_name AS seller_username, u2.user_firstname AS seller_firstname, u2.user_lastname AS seller_lastname, u2.user_picture AS seller_user_picture, u2.user_gender AS seller_user_gender FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id WHERE orders.order_hash LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* prepare buyer's info */
              $row['buyer_fullname'] = ($system['show_usernames_enabled']) ? $row['buyer_username'] : $row['buyer_firstname'] . ' ' . $row['buyer_lastname'];
              $row['buyer_picture'] = get_picture($row['buyer_user_picture'], $row['buyer_user_gender']);
              /* prepare seller's info */
              $row['seller_fullname'] = ($system['show_usernames_enabled']) ? $row['seller_username'] : $row['seller_firstname'] . ' ' . $row['seller_lastname'];
              $row['seller_picture'] = get_picture($row['seller_user_picture'], $row['seller_user_gender']);
              /* prepare total commission */
              $row['total_commission'] = $row['sub_total'] * ($row['commission'] / 100);
              /* prepare final price */
              $row['final_price'] = $row['sub_total'] - $row['total_commission'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("market_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM market_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("market_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("market_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Payment Requests"));

          if (!licence_has_entitlement('monetization')) {
            break;
          }

          // get data
          $get_rows = $db->query("SELECT market_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM market_payments INNER JOIN users ON market_payments.user_id = users.user_id WHERE market_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['market_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'developers':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_developers_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Settings"));
          break;

        case 'apps':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Apps"));

          // get data
          $get_rows = $db->query("SELECT developers_apps.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM developers_apps INNER JOIN users ON developers_apps.app_user_id = users.user_id");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("developers_apps_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM developers_apps_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("developers_apps_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("developers_apps_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'reports':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_reports_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports"));

          // get data
          $get_rows = $db->query("SELECT reports.*, reports_categories.category_name, users.user_name, users.user_firstname, users.user_lastname, users.user_picture, users.user_gender FROM reports INNER JOIN users ON reports.user_id = users.user_id LEFT JOIN reports_categories ON reports.category_id = reports_categories.category_id");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              /* get reported node */
              if ($row['node_type'] == "user") {
                $get_node = $db->query(sprintf("SELECT user_name, user_firstname, user_lastname, user_gender, user_picture FROM users WHERE user_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['user_picture'] = get_picture($node['user_picture'], $node['user_gender']);
                $node['color'] = 'primary';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'page') {
                $get_node = $db->query(sprintf("SELECT page_name, page_title, page_picture FROM pages WHERE page_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['page_picture'] = get_picture($node['page_picture'], 'page');
                $node['color'] = 'info';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'group') {
                $get_node = $db->query(sprintf("SELECT group_name, group_title, group_picture FROM `groups` WHERE group_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['group_picture'] = get_picture($node['group_picture'], 'group');
                $node['color'] = 'warning';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'event') {
                $get_node = $db->query(sprintf("SELECT event_title, event_cover FROM `events` WHERE event_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['event_picture'] = get_picture($node['event_cover'], 'event');
                $node['color'] = 'success';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'comment') {
                $comment = $user->get_comment($row['node_id']);
                if (!$comment) continue;
                if ($comment['node_type'] == "post") {
                  $_handle = '/posts/';
                  $_node_id = $comment['node_id'];
                } elseif ($comment['node_type'] == "photo") {
                  $_handle = '/photos/';
                  $_node_id = $comment['node_id'];
                } elseif ($comment['node_type'] == "comment") {
                  $_handle = ($comment['parent_comment']['node_type'] == "post") ? '/posts/' : '/photos/';
                  $_node_id = $comment['parent_comment']['node_id'];
                }
                $row['url'] = $system['system_url'] . $_handle . $_node_id . '?notify_id=comment_' . $row['node_id'];
                $node['color'] = 'secondary';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'post') {
                $node['color'] = 'danger';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'forum_thread') {
                $thread = $user->get_forum_thread($row['node_id']);
                if (!$thread) continue;
                $row['url'] = $system['system_url'] . '/forums/thread/' . $thread['thread_id'] . '/' . $thread['title_url'];
                $node['color'] = 'secondary';
                $node['name'] = __("Forum Thread");
              } elseif ($row['node_type'] == 'forum_reply') {
                $reply = $user->get_forum_reply($row['node_id']);
                if (!$reply) continue;
                $row['url'] = $system['system_url'] . '/forums/thread/' . $reply['thread']['thread_id'] . '/' . $reply['thread']['title_url'] . '/#reply-' . $reply['reply_id'];
                $node['color'] = 'secondary';
                $node['name'] = __("Forum Reply");
              } elseif ($row['node_type'] == 'ads_campaign') {
                $row['url'] = $system['system_url'] . '/ads/edit/' . $row['node_id'];
                $node['color'] = 'warning';
                $node['name'] = __("Ads Campaign");
              }
              $row['node'] = $node;
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("reports_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM reports_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("reports_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("reports_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'blacklist':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_blacklist_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blacklist"));

          // get data
          $get_rows = $db->query("SELECT * FROM blacklist");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blacklist") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'verification':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_verifications_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Verification") . " &rsaquo; " . __("Requests"));

          // get data
          $get_rows = $db->query("SELECT verification_requests.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.page_name, pages.page_title, pages.page_picture FROM verification_requests LEFT JOIN users ON verification_requests.node_type = 'user' AND verification_requests.node_id = users.user_id LEFT JOIN pages ON verification_requests.node_type = 'page' AND verification_requests.node_id = pages.page_id WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND verification_requests.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* get node */
              if ($row['node_type'] == "user") {
                $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['color'] = 'primary';
              } elseif ($row['node_type'] == 'page') {
                $row['page_picture'] = get_picture($row['page_picture'], 'page');
                $row['color'] = 'info';
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'users':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Verification") . " &rsaquo; " . __("Verified Users"));

          // get data
          $get_rows = $db->query("SELECT * FROM users WHERE user_verified = '1'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'pages':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Verification") . " &rsaquo; " . __("Verified Pages"));

          // get data
          $get_rows = $db->query("SELECT * FROM pages WHERE page_verified = '1'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'tools':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case 'faker':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Fake Generator"));

          // get countries if not defined
          if (!$countries) {
            $smarty->assign('countries', $user->get_countries());
          }

          // get pages categories
          $smarty->assign('pages_categories', $user->get_categories("pages_categories"));

          // get groups categories
          $smarty->assign('groups_categories', $user->get_categories("groups_categories"));
          break;

        case 'auto-connect':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Auto Connect"));

          // get all custom auto-connect
          $get_rows = $db->query("SELECT * FROM auto_connect");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[$row['type']][] = $row;
            }
          }
          /* assign variables */
          $smarty->assign('rows', $rows);

          // get countries if not defined
          if (!$countries) {
            $smarty->assign('countries', $user->get_countries());
          }
          break;

        case 'garbage-collector':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Garbage Collector"));

          // prepare counts
          /* users not activated */
          $get_users_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
          $insights['users_not_activated'] = $get_users_not_activated->fetch_assoc()['count'];
          /* users not logged in 1 week */
          $get_users_not_logged_week = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 WEEK");
          $insights['users_not_logged_week'] = $get_users_not_logged_week->fetch_assoc()['count'];
          /* users not logged in 1 month */
          $get_users_not_logged_month = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 MONTH");
          $insights['users_not_logged_month'] = $get_users_not_logged_month->fetch_assoc()['count'];
          /* users not logged in 3 month3 */
          $get_users_not_logged_3_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 3 MONTH");
          $insights['users_not_logged_3_months'] = $get_users_not_logged_3_months->fetch_assoc()['count'];
          /* users not logged in 1 month */
          $get_users_not_logged_6_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 6 MONTH");
          $insights['users_not_logged_6_months'] = $get_users_not_logged_6_months->fetch_assoc()['count'];
          /* users not logged in 1 month */
          $get_users_not_logged_9_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 9 MONTH");
          $insights['users_not_logged_9_months'] = $get_users_not_logged_9_months->fetch_assoc()['count'];
          /* users not logged in 1 year */
          $get_users_not_logged_year = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 YEAR");
          $insights['users_not_logged_year'] = $get_users_not_logged_year->fetch_assoc()['count'];
          /* posts longer than 1 week */
          $get_posts_longer_week = $db->query("SELECT COUNT(*) as count FROM posts WHERE time < NOW() - INTERVAL 1 WEEK");
          $insights['posts_longer_week'] = $get_posts_longer_week->fetch_assoc()['count'];
          /* posts longer than 1 month */
          $get_posts_longer_month = $db->query("SELECT COUNT(*) as count FROM posts WHERE posts.time < NOW() - INTERVAL 1 MONTH");
          $insights['posts_longer_month'] = $get_posts_longer_month->fetch_assoc()['count'];
          /* posts longer than 1 year */
          $get_posts_longer_year = $db->query("SELECT COUNT(*) as count FROM posts WHERE posts.time < NOW() - INTERVAL 1 YEAR");
          $insights['posts_longer_year'] = $get_posts_longer_year->fetch_assoc()['count'];
          /* get fake users */
          $get_fake_users = $db->query("SELECT COUNT(*) as count FROM users WHERE is_fake = '1'");
          $insights['fake_users'] = $get_fake_users->fetch_assoc()['count'];
          /* get fake pages */
          $get_fake_pages = $db->query("SELECT COUNT(*) as count FROM pages WHERE is_fake = '1'");
          $insights['fake_pages'] = $get_fake_pages->fetch_assoc()['count'];
          /* get fake groups */
          $get_fake_groups = $db->query("SELECT COUNT(*) as count FROM `groups` WHERE is_fake = '1'");
          $insights['fake_groups'] = $get_fake_groups->fetch_assoc()['count'];
          /* get undelivered shipped orders */
          $get_orders = $db->query(sprintf("SELECT COUNT(*) as count FROM orders WHERE `status` = 'shipped' AND update_time < NOW() - INTERVAL %s DAY", $system['market_delivery_days']));
          $insights['undelivered_orders'] = $get_orders->fetch_assoc()['count'];
          /* get pending uploads */
          $get_pending_uploads = $db->query("SELECT COUNT(*) as count FROM users_uploads_pending");
          $insights['pending_uploads'] = $get_pending_uploads->fetch_assoc()['count'];
          /* get pending uploads size */
          $get_pending_uploads_size = $db->query("SELECT SUM(file_size) as size FROM users_uploads_pending");
          $insights['pending_uploads_size'] = round($get_pending_uploads_size->fetch_assoc()['size'] / 1024 / 1024, 2);

          // assign variables
          $smarty->assign('insights', $insights);
          break;

        case 'backups':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Backup Database & Files"));
          break;

        case 'cronjob':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Cronjob Settings"));

          // check if $system cronjob hash is set
          if (empty($system['cronjob_hash'])) {
            $system['cronjob_hash'] = md5(uniqid());
            update_system_options([
              'cronjob_hash' => secure($system['cronjob_hash']),
            ]);
          }
          break;

        case 'reset':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Factory Reset"));
          break;

        case 'forum-migration':
          // Run forum schema migrations and output result as plain text
          header('Content-Type: text/plain; charset=utf-8');
          $migrations = [
            "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_pinned` TINYINT(1) NOT NULL DEFAULT 0",
            "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_locked` TINYINT(1) NOT NULL DEFAULT 0",
            "ALTER TABLE `forums_replies` ADD COLUMN IF NOT EXISTS `reply_is_answer` TINYINT(1) NOT NULL DEFAULT 0",
            "CREATE TABLE IF NOT EXISTS `forums_moderation_log` (
              `log_id` INT(11) NOT NULL AUTO_INCREMENT,
              `moderator_id` INT(11) NOT NULL,
              `action_type` VARCHAR(50) NOT NULL,
              `target_type` VARCHAR(20) NOT NULL,
              `target_id` INT(11) NOT NULL,
              `reason` TEXT,
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`log_id`),
              KEY `idx_mod_log_moderator` (`moderator_id`),
              KEY `idx_mod_log_target` (`target_type`, `target_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `forums_subscriptions` (
              `subscription_id` INT(11) NOT NULL AUTO_INCREMENT,
              `user_id` INT(11) NOT NULL,
              `forum_id` INT(11),
              `thread_id` INT(11),
              `subscription_type` ENUM('forum','thread') NOT NULL,
              `notify_email` TINYINT(1) NOT NULL DEFAULT 1,
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`subscription_id`),
              UNIQUE KEY `uq_sub_forum` (`user_id`, `forum_id`, `subscription_type`),
              UNIQUE KEY `uq_sub_thread` (`user_id`, `thread_id`, `subscription_type`),
              KEY `idx_sub_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
          ];
          // Clear Smarty compiled template cache
          $smarty->clearAllCache();
          $smarty->clearCompiledTemplate();
          echo "=== Forum Migration & Cache Clear ===\n\n";
          echo "-- SQL Migrations --\n";
          $ok = 0; $errs = [];
          foreach ($migrations as $sql) {
            $short = substr(trim(preg_replace('/\s+/', ' ', $sql)), 0, 70);
            if ($db->query($sql) !== false) {
              echo "OK    : $short\n"; $ok++;
            } else {
              $e = $db->error;
              if (stripos($e, 'duplicate') !== false || stripos($e, 'already') !== false) {
                echo "SKIP  : $short\n"; $ok++;
              } else {
                echo "ERROR : $short\n       $e\n"; $errs[] = $e;
              }
            }
          }
          echo "\n-- Cache --\n";
          echo "OK    : Smarty cache cleared\n";
          echo "\n-- Verify --\n";
          foreach (['forums_moderation_log', 'forums_subscriptions'] as $t) {
            $r = $db->query("SHOW TABLES LIKE '$t'");
            echo ($r && $r->num_rows > 0 ? "EXISTS " : "MISSING") . ": $t\n";
          }
          foreach ([
            ['forums_threads',  'thread_pinned'],
            ['forums_threads',  'thread_locked'],
            ['forums_replies',  'reply_is_answer'],
          ] as [$tbl, $col]) {
            $r = $db->query("SHOW COLUMNS FROM `$tbl` LIKE '$col'");
            echo ($r && $r->num_rows > 0 ? "EXISTS " : "MISSING") . ": $tbl.$col\n";
          }
          echo "\n=== Done: $ok/" . count($migrations) . " statements ===\n";
          if (!empty($errs)) { echo "\nFailed:\n"; foreach ($errs as $e) echo "  - $e\n"; }
          exit;

        default:
          _error(404);
          break;
      }
      break;

    case 'custom_fields':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Custom Fields"));

          // get data
          $get_rows = $db->query("SELECT * FROM custom_fields");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM custom_fields WHERE field_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Custom Fields") . " &rsaquo; " . $data['label']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Custom Fields") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'static':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Static Pages"));

          // get data
          $get_rows = $db->query("SELECT * FROM static_pages");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['url'] = ($row['page_is_redirect']) ? $row['page_redirect_url'] : $system['system_url'] . '/static/' . $row['page_url'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM static_pages WHERE page_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Static Pages") . " &rsaquo; " . $data['page_title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Static Pages") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'colored_posts':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Colored Posts"));

          // get data
          $rows = $user->get_posts_colored_patterns();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM posts_colored_patterns WHERE pattern_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Colored Posts") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Colored Posts") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'widgets':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Widgets"));

          // get data
          $get_rows = $db->query("SELECT widgets.*, system_languages.title AS language_title FROM widgets LEFT JOIN system_languages ON widgets.language_id = system_languages.language_id");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM widgets WHERE widget_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Widgets") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Widgets") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'reactions':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reactions"));

          // get data
          $rows = $system['reactions'];

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_reactions WHERE reaction_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reactions") . " &rsaquo; " . __("Edit Reaction"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'emojis':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Emojis"));

          // get data
          $rows = $emojis;

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM emojis WHERE emoji_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Emojis") . " &rsaquo; " . __("Edit Emoji"));
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Emojis") . " &rsaquo; " . __("Add New Emoji"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'stickers':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Stickers"));

          // get data
          $rows = $user->get_stickers();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM stickers WHERE sticker_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Stickers") . " &rsaquo; " . __("Edit Sticker"));
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Stickers") . " &rsaquo; " . __("Add New Sticker"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'gifts':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_customization_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Gifts"));

          // get data
          $rows = $user->get_gifts();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM gifts WHERE gift_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Gifts") . " &rsaquo; " . __("Edit Gift"));
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Gifts") . " &rsaquo; " . __("Add New Gift"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'announcements':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_reach_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Announcements"));

          // get data
          $get_rows = $db->query("SELECT * FROM announcements");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM announcements WHERE announcement_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Announcements") . " &rsaquo; " . $data['name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Announcements") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'notifications':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_reach_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Mass Notifications"));
      break;

    case 'newsletter':
      // check admin|moderator permission
      if ($user->_is_moderator && !$system['mods_reach_permission']) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Newsletter"));

      // prepare counts
      /* all users */
      $get_users_all = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '1'");
      $insights['users_all'] = $get_users_all->fetch_assoc()['count'];
      /* users activated */
      $get_users_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '1'");
      $insights['users_activated'] = $get_users_activated->fetch_assoc()['count'];
      /* users not activated */
      $get_users_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
      $insights['users_not_activated'] = $get_users_not_activated->fetch_assoc()['count'];
      /* users not logged in from 1 week */
      $get_users_not_logged_week = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 WEEK");
      $insights['users_not_logged_week'] = $get_users_not_logged_week->fetch_assoc()['count'];
      /* users not logged in from 1 month */
      $get_users_not_logged_month = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 MONTH");
      $insights['users_not_logged_month'] = $get_users_not_logged_month->fetch_assoc()['count'];
      /* users not logged in from 3 month */
      $get_users_not_logged_3_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 3 MONTH");
      $insights['users_not_logged_3_months'] = $get_users_not_logged_3_months->fetch_assoc()['count'];
      /* users not logged in from 6 month */
      $get_users_not_logged_6_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 6 MONTH");
      $insights['users_not_logged_6_months'] = $get_users_not_logged_6_months->fetch_assoc()['count'];
      /* users not logged in from 9 month */
      $get_users_not_logged_9_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 9 MONTH");
      $insights['users_not_logged_9_months'] = $get_users_not_logged_9_months->fetch_assoc()['count'];
      /* not logged in 1 year */
      $get_users_not_logged_year = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 YEAR");
      $insights['users_not_logged_year'] = $get_users_not_logged_year->fetch_assoc()['count'];

      // assign variables
      $smarty->assign('insights', $insights);
      break;

    case 'merits':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Merits"));

          // get data
          $cycle_dates = $user->get_merits_cycle_dates();

          // assign variables
          $smarty->assign('cycle_dates', $cycle_dates);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Merits") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("merits_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM merits_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("merits_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Merits") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Merits") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("merits_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'pwa':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("PWA"));
      break;

    case 'apis':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("APIs"));
      break;

    case 'apps':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Apps"));
      break;

    case 'license':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("License"));

      $licence_info = [
        'status' => 'missing',
        'message' => '',
        'api_warning' => '',
        'payload' => null,
        'key_masked' => licence_mask_key(defined('LICENCE_KEY') ? LICENCE_KEY : ''),
      ];

      $licence_file = ABSPATH . 'includes/sys_verif.php';
      if (!is_file($licence_file)) {
        $licence_info['status'] = 'missing';
        $licence_info['message'] = __("No license file found.");
      } else {
        $stored = include $licence_file;
        try {
          if (!is_array($stored)) {
            throw new Exception("License verification failed.");
          }
          licence_validate_payload($stored);
          $licence_info['payload'] = $stored;
          $licence_info['status'] = 'valid';
        } catch (Exception $e) {
          $licence_info['message'] = $e->getMessage();
          $licence_info['status'] = (strpos($e->getMessage(), 'expired') !== false) ? 'expired' : 'invalid';
        }
      }

      if (!$user->_data['user_demo'] && defined('LICENCE_KEY') && LICENCE_KEY !== '') {
        try {
          $fresh = get_licence_key(LICENCE_KEY, 'admin');
          licence_store_payload($fresh);
          $licence_info['payload'] = $fresh;
          $licence_info['status'] = 'valid';
          $licence_info['message'] = '';
        } catch (Exception $e) {
          $api_message = $e->getMessage();
          if (strpos($api_message, 'temporarily unavailable') !== false) {
            if ($licence_info['status'] == 'valid') {
              $licence_info['status'] = 'unreachable';
              $licence_info['api_warning'] = __("License server unreachable; showing last verified payload.");
            } else {
              $licence_info['api_warning'] = $api_message;
            }
          } else {
            $licence_info['status'] = (strpos($api_message, 'expired') !== false) ? 'expired' : 'invalid';
            $licence_info['message'] = $api_message;
          }
        }
      }

      if (is_array($licence_info['payload'])) {
        unset($licence_info['payload']['signature']);
      } else {
        $licence_info['payload'] = [];
      }

      // normalize the payload so the template never touches an undefined key
      $licence_info['payload'] = array_merge([
        'source' => '',
        'license_type' => '',
        'domain' => '',
        'issued_at' => '',
        'expires_at' => '',
        'support_expires_at' => '',
        'billing_cycle' => '',
        'entitlements' => [],
        'modules' => [],
      ], $licence_info['payload']);
      $licence_info['payload']['entitlements'] = array_merge(['monetization' => false], (array) $licence_info['payload']['entitlements']);
      foreach ($licence_info['payload']['modules'] as $_i => $_module) {
        $licence_info['payload']['modules'][$_i] = array_merge(['slug' => '', 'version' => '', 'available' => false], (array) $_module);
      }

      // days left before support & payload verification lapse
      $licence_info['support_timeleft'] = null;
      $licence_info['payload_timeleft'] = null;
      if (!empty($licence_info['payload']['support_expires_at'])) {
        $licence_info['support_timeleft'] = ceil((strtotime($licence_info['payload']['support_expires_at']) - time()) / (60 * 60 * 24));
      }
      if (!empty($licence_info['payload']['expires_at'])) {
        $licence_info['payload_timeleft'] = ceil((strtotime($licence_info['payload']['expires_at']) - time()) / (60 * 60 * 24));
      }

      $kind = strtolower(trim((string) $licence_info['payload']['license_type']));
      if (!in_array($kind, ['regular', 'extended', 'subscription'], true)) {
        $kind = 'regular';
      }
      $licence_info['kind'] = $kind;
      $licence_info['perpetual'] = ($kind !== 'subscription');
      $licence_info['support_ended'] = ($licence_info['support_timeleft'] !== null && $licence_info['support_timeleft'] <= 0);
      $licence_info['monetization'] = !empty($licence_info['payload']['entitlements']['monetization']);
      $licence_info['module_installed'] = licence_module_installed();

      $status = $licence_info['status'];
      if ($status == 'missing') {
        $ui = 'missing';
      } elseif ($status == 'invalid') {
        $ui = 'invalid';
      } elseif ($status == 'unreachable') {
        $ui = 'unreachable';
      } elseif ($kind == 'subscription' && ($licence_info['support_ended'] || $status == 'expired')) {
        $ui = 'lapsed';
      } elseif ($licence_info['perpetual'] && $licence_info['support_ended']) {
        $ui = 'support_ended';
      } else {
        $ui = 'active';
      }
      $licence_info['ui'] = $ui;

      $issued_ts = !empty($licence_info['payload']['issued_at']) ? strtotime($licence_info['payload']['issued_at']) : false;
      $support_ts = !empty($licence_info['payload']['support_expires_at']) ? strtotime($licence_info['payload']['support_expires_at']) : false;
      $timeline = [
        'mode' => 'none',
        'today_pct' => 0,
        'today_at_end' => false,
      ];
      if ($issued_ts && $support_ts && !in_array($ui, ['missing', 'invalid'], true)) {
        $timeline['mode'] = $licence_info['perpetual'] ? 'perpetual' : 'term';
        $span = $support_ts - $issued_ts;
        if ($span <= 0 || $licence_info['support_ended'] || time() >= $support_ts) {
          $timeline['today_pct'] = 100;
          $timeline['today_at_end'] = true;
        } else {
          $timeline['today_pct'] = (int) round(((time() - $issued_ts) / $span) * 100);
          $timeline['today_pct'] = max(0, min(100, $timeline['today_pct']));
          $timeline['today_at_end'] = ($timeline['today_pct'] >= 100);
        }
      }
      $licence_info['timeline'] = $timeline;
      $licence_info['rel_support'] = licence_relative_days($licence_info['support_timeleft']);
      $licence_info['rel_verify'] = licence_relative_days($licence_info['payload_timeleft']);
      $licence_info['billing'] = ($kind == 'subscription') ? licence_billing_cycle($licence_info['payload']) : null;

      $smarty->assign('licence_info', $licence_info);
      break;

    case 'changelog':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Changelog"));
      break;

    default:
      // check admin|moderator permission
      if (!$user->_is_admin || !$user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }
      _error(404);
  }
  /* assign variables */
  $smarty->assign('view', $_GET['view']);
  $smarty->assign('sub_view', $_GET['sub_view']);
  $smarty->assign('control_panel', $control_panel);
  $smarty->assign('licence_lock', licence_admin_lock());

  // global insights
  if ($user->_is_admin) {
    if ($licence_monetization && is_file(ABSPATH . 'includes/admin-monetization.php')) {
      require_once ABSPATH . 'includes/admin-monetization.php';
      admin_monetization_insights();
    }
    /* affiliates payments insights */
    $get_affiliates_payments = $db->query("SELECT COUNT(*) as count FROM affiliates_payments INNER JOIN users ON affiliates_payments.user_id = users.user_id WHERE affiliates_payments.status = '0'");
    $affiliates_payments_insights = $get_affiliates_payments->fetch_assoc()['count'];
    $smarty->assign('affiliates_payments_insights', $affiliates_payments_insights);
    /* points payments insights */
    $get_points_payments = $db->query("SELECT COUNT(*) as count FROM points_payments INNER JOIN users ON points_payments.user_id = users.user_id WHERE points_payments.status = '0'");
    $points_payments_insights = $get_points_payments->fetch_assoc()['count'];
    $smarty->assign('points_payments_insights', $points_payments_insights);
    /* marketplace payments insights */
    $get_marketplace_payments = $db->query("SELECT COUNT(*) as count FROM market_payments INNER JOIN users ON market_payments.user_id = users.user_id WHERE market_payments.status = '0'");
    $marketplace_payments_insights = $get_marketplace_payments->fetch_assoc()['count'];
    $smarty->assign('marketplace_payments_insights', $marketplace_payments_insights);
  }
  /* reports insights */
  $get_reports = $db->query("SELECT COUNT(*) as count FROM reports INNER JOIN users ON reports.user_id = users.user_id");
  $reports_insights = $get_reports->fetch_assoc()['count'];
  $smarty->assign('reports_insights', $reports_insights);
  /* verification requests insights */
  $get_verification_requests = $db->query("SELECT COUNT(*) as count FROM verification_requests LEFT JOIN users ON verification_requests.node_type = 'user' AND verification_requests.node_id = users.user_id LEFT JOIN pages ON verification_requests.node_type = 'page' AND verification_requests.node_id = pages.page_id WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND verification_requests.status = '0'");
  $verification_requests_insights = $get_verification_requests->fetch_assoc()['count'];
  $smarty->assign('verification_requests_insights', $verification_requests_insights);
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page footer
page_footer('admin');
