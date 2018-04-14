
-- SEWF PLUGIN REGISTRATION --

INSERT IGNORE INTO `swef_config_plugin`
  (
    `plugin_Dash_Allow`, `plugin_Dash_Usergroup_Preg_Match`, `plugin_Enabled`,
	  `plugin_Context_LIKE`, `plugin_Classname`, `plugin_Handle_Priority`,
	  `plugin_Configs`
	)
  VALUES
    (
      1, '<^(sysadmin)$>', 1, 'dashboard', '\\Swef\\SwefLogin', 1,
      'template::html/dashboard.default.html;;\r\ncontent_type::text/html; charset=UTF-8;;\r\n'
    ),
    (
      0, '', 1, 'www-%', '\\Swef\\SwefLogin', 1,
      'template::html/www.default.html;;\r\ncontent_type::text/html; charset=UTF-8;;\r\n'
    );
