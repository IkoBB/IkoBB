INSERT INTO `iko_modules` VALUES ('IkoBB','iko','Core Engine','1.0.0a',1),
('IkoBB','forum','Forum Engine','1.0.0a',1),
('IkoBB','cms','Template Engine','1.0.0a',1),
('IkoBB','user','User Engine','1.0.0a',1),
('IkoBB','language','Language Engine','1.0.0a',1);


INSERT INTO `iko_configs` VALUES ('site_name', 's:10:"Test Value";', 'The name of the site', 'iko'),
	('site_template', "i:1;", 'Insert the ID of the template which should be the default template.', 'cms'),
	('site_email', 's:13:"test@test.com";', 'The contact email of the forum. Also used for sending emails.', 'iko'),
	('site_maintenance', "i:0;", 'Indicates if the site is maintenance. 1 - Maintenance Mode on; 2 - Maintenance Mode off','iko'),
	('date_format', 's:9:"d-m-Y H:i";', 'Default date format', 'iko');



INSERT INTO `iko_templates` (`template_name`, `template_author`, `template_version`, `template_directory`, `template_required_core_version`)
VALUES ('Default Template', 'IkoBB', '0.0.1', 'default', '1.0.0a');

INSERT INTO `iko_users` (`user_id`, `user_name`, `user_password`, `user_email`, `user_signature`, `user_about_user`, `user_location_id`, `user_gender`, `user_date_joined`, `user_birthday`, `user_timezone_id`, `user_last_login`, `user_language`, `user_template`)
VALUES
	(1, 'Administrator', '3eb1e1c0e7045742a8e0bbe74a9cdaefcba8c1b80c3e4a994a0b7d3ae4d123de', 'admin@ikobb.de',
		'signature', 'about me', 1, 1, 11021996, '0000-00-00', 1, 1478377625, 'german', 1);


INSERT INTO `iko_usergroups` VALUES ('1','Admin','<span class="color: red; ">%% group_name %%</span>'),
('2','Moderator','<span class="color: green; ">%% group_name %%</span>'),
('3','User','%% group_name %%');


INSERT INTO `iko_user_assignment` VALUES ('1','1');

INSERT INTO `iko_group_assignment` (`parent_group_id`, `child_group_id`) VALUES
	(2, 1),
	(3, 2);

INSERT INTO `iko_permissions` VALUES ('*', 'iko', 'Can do what they want to do.'),
	('iko.user.set.user_name', 'user', 'Change the Username'),
	('iko.user.set.user_email', 'user', 'Change the User Email address'),
	('iko.user.set.user_avatar', 'user', 'Change the User Avatar'),
	('iko.user.set.user_template', 'user', 'Change the User choosen Template'),
	('iko.user.set.user_language', 'user', 'Change the User choosen Language'),
	('iko.user.groups.add', 'user', 'Allows User to Add member to a group'),
	('iko.user.groups.remove', 'user', 'Allows User to Remove member from a group'),
	('iko.user.profile.fields.user.set', 'user', 'Change User Field value'),
	('iko.user.profile.fields.user.property', 'user', 'Change User field property'),
	('iko.user.profile.fields.set.name', 'user', 'Allows User to rename a Field'),
	('iko.user.profile.fields.create', 'user', 'Allow User to create new Fields'),
	('iko.user.permissions.add', 'user', 'Allows user to add Permissions from Group or User'),
	('iko.user.permissions.remove', 'user', 'Allows user to remove Permissions from Group or User'),
	('iko.language.keys.set.lang', 'language', 'Allows to change language value for a key');


INSERT INTO `iko_group_permissions` VALUES ('1', '*');


INSERT INTO `iko_translation` VALUES ('user_name','Benutzername','username'),
('password','Kennwort','password');

INSERT INTO `iko_bbcodes` (`bbcode_id`, `bbcode_tag`, `pattern`, `replacement`) VALUES
	(1, '[noparse]', '/\\[noparse\\](.*?)\\[\\/noparse\\]/muis', NULL),
	(2, '[tt]', '/\\[tt\\](.*?)\\[\\/tt\\]/uis', NULL),
	(3, '[code]', '/\\[code\\](.*?)\\[\\/code\\]/muis', NULL),
	(4, '[code=', '/\\[code=([^\\]]*?)\\](.*?)\\[\\/code\\]/muis', NULL),
	(5, '[b]', '/\\[b\\](.*?)\\[\\/b\\]/muis', '<b>$1</b>'),
	(6, '[i]', '/\\[i\\](.*?)\\[\\/i\\]/uis', '<i>$1</i>'),
	(7, '[u]', '/\\[u\\](.*?)\\[\\/u\\]/uis', '<u>$1</u>'),
	(8, '[s]', '/\\[s\\](.*?)\\[\\/s\\]/uis', '<del>$1</del>'),
	(9, '[sup]', '/\\[sup\\](.*?)\\[\\/sup\\]/uis', '<sup>$1</sup>'),
	(10, '[sub]', '/\\[sub\\](.*?)\\[\\/sub\\]/uis', '<sub>$1</sub>'),
	(11, '[left]', '/\\[left\\](.*?)\\[\\/left\\]/uis', '<p align="left">$1</p>'),
	(12, '[center]', '/\\[center\\](.*?)\\[\\/center\\]/uis', '<p align="center">$1</p>'),
	(13, '[right]', '/\\[right\\](.*?)\\[\\/right\\]/uis', '<p align="right">$1</p>'),
	(14, '[size=', '/\\[size=([^\\]]*?)\\](.*?)\\[\\/size\\]/uis', '<span style="font-size: $1px;">$2</span>'),
	(15, '[color=', '/\\[color=([^\\]]*?)\\](.*?)\\[\\/color\\]/uis', '<span style="color: $1;">$2</span>'),
	(16, '[font=', '/\\[font=([^\\]]*?)\\](.*?)\\[\\/font\\]/uis', '<span style="font-family: $1;">$2</span>'),
	(17, '[quote]', '/\\[quote\\](.*?)\\[\\/quote\\]/uis', '<blockquote>$1</blockquote>'),
	(18, '[quote=', '/\\[quote=([^\\]]*?)\\](.*?)\\[\\/quote\\]/uis', '<blockquote>$1 wrote: <br>$2</blockquote>'),
	(19, '[table]', '/\\[table\\](.*?)\\[\\/table\\]/uis', '<table>$1</table>'),
	(20, '[tr]', '/\\[tr\\](.*?)\\[\\/tr\\]/uis', '<tr>$1</tr>'),
	(21, '[td]', '/\\[td\\](.*?)\\[\\/td\\]/uis', '<td>$1</td>'),
	(22, '[th]', '/\\[th\\](.*?)\\[\\/th\\]/uis', '<th>$1</th>'),
	(23, '[fa]', '/\\[fa\\](.*?)\\[\\/fa\\]/uis', '<i class="fa $1" aria-hidden="true"></i>'),
	(24, '[ul]', '/\\[ul\\](.*?)\\[\\/ul\\]/uis', '<ul>$1</ul>'),
	(25, '[list]', '/\\[list\\](.*?)\\[\\/list\\]/uis', '<ul>$1</ul>'),
	(26, '[ol]', '/\\[ol\\](.*?)\\[\\/ol\\]/uis', '<ol>$1</ol>'),
	(27, '[ol=', '/\\[ol=([^\\]]*?)\\](.*?)\\[\\/ol\\]/uis', '<ol type="$1">$2</ol>'),
	(28, '[list=', '/\\[list=([^\\]]*?)\\](.*?)\\[\\/list\\]/uis', '<ol type="$1">$2</ol>'),
	(29, '[*]', '/\\[\\*\\](.*?)\\<br\\>/uis', '<li>$1</li>'),
	(30, '[img]', '/\\[img\\](.*?)\\[\\/img\\]/uis', NULL),
	(31, '[url]', '/\\[url\\](.*?)\\[\\/url\\]/uis', NULL),
	(32, '[url=', '/\\[url=([^\\]]*?)\\](.*?)\\[\\/url\\]/uis', NULL),
	(33, '[media=', '/\\[media=([^\\]]*?)\\](.*?)\\[\\/media\\]/uis', NULL),
	(34, '[youtube]', '/\\[youtube\\](.*?)\\[\\/youtube\\]/uis', '<iframe width="560" height="315" src="//www.youtube.com/embed/$1?rel=0" frameborder="0" allowfullscreen></iframe>');

INSERT INTO `iko_user_fields` (`user_field_name`, `user_field_options`, `user_field_display`, `user_field_owner`) VALUES
	('Facebook', 'a:1:{i:0;a:1:{s:6:"prefix";s:25:"https://www.facebook.com/";}}', '{$prefix}{$value}', 1),
	('Youtube', 'a:3:{i:0;a:1:{s:6:"prefix";s:26:"https://www.youtube.com/c/";}i:1;a:1:{s:6:"prefix";s:32:"https://www.youtube.com/channel/";}i:2;a:1:{s:6:"prefix";s:29:"https://www.youtube.com/user/";}}', '{$prefix}{$value}', 1);
