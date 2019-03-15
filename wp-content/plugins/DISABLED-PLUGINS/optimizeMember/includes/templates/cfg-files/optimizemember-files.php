<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");

global $base; /* A Multisite ``$base`` configuration? */
$ws_plugin__optimizemember_temp_s_base = (!empty ($base)) ? $base : c_ws_plugin__optimizemember_utils_urls::parse_url (network_home_url ("/"), PHP_URL_PATH);
/* This works on Multisite installs too. The function ``network_home_url ()`` defaults to ``home_url ()`` on standard WordPress installs. */
/* Do NOT use ``site`` URL. Must use the `home` URL here, because that's what WordPress uses in its own `mod_rewrite` implementation. */
?>

Options +FollowSymLinks -MultiViews -Indexes

<IfModule mod_env.c>
# No GZIP for script-based file downloads.
	SetEnv no-gzip 1
</IfModule>

<IfModule mod_rewrite.c>
# Enable rewrite and configure base.
	RewriteEngine On
	RewriteBase <?php echo $ws_plugin__optimizemember_temp_s_base . "\n"; ?>

# Initialize all environment variables we're using below.
	RewriteCond %{ENV:optimizemember_file_download_setup} !^complete$
	RewriteRule ^(.*)$ - [E=optimizemember_file_download_wp_vdir:0,E=optimizemember_file_download:$1,E=optimizemember_file_stream:0,E=optimizemember_file_inline:0,E=optimizemember_file_storage:0,E=optimizemember_file_remote:0,E=optimizemember_file_ssl:0,E=optimizemember_file_download_key:0,E=optimizemember_skip_confirmation:0,E=optimizemember_file_download_setup:complete]

# Handle virtual directories, common on multisite networks.
	RewriteCond %{ENV:optimizemember_file_download_wp_vdir_check} !^complete$
	RewriteCond %{THE_REQUEST} ^(?:GET|HEAD)(?:[\ ]+)(?:<?php echo preg_quote ($ws_plugin__optimizemember_temp_s_base, " "); ?>)([_0-9a-zA-Z\-]+/)(?:wp-content/)
	RewriteRule ^(.*)$ - [E=optimizemember_file_download_wp_vdir:,E=optimizemember_file_download_wp_vdir:%1,E=optimizemember_file_download_wp_vdir_check:complete]

# Handle streaming download requests via the rewrite engine.
	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-stream/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%2,E=optimizemember_file_stream:,E=optimizemember_file_stream:&optimizemember_file_stream=yes]

	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-stream-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%3,E=optimizemember_file_stream:,E=optimizemember_file_stream:&optimizemember_file_stream=%2]

# Handle inline file requests via the rewrite engine.
	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-inline/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%2,E=optimizemember_file_inline:,E=optimizemember_file_inline:&optimizemember_file_inline=yes]

	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-inline-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%3,E=optimizemember_file_inline:,E=optimizemember_file_inline:&optimizemember_file_inline=%2]

# Handle storage specifications via the rewrite engine.
	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-storage-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%3,E=optimizemember_file_storage:,E=optimizemember_file_storage:&optimizemember_file_storage=%2]

# Handle remote authorization requests via the rewrite engine.
	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-remote/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%2,E=optimizemember_file_remote:,E=optimizemember_file_remote:&optimizemember_file_remote=yes]

	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-remote-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%3,E=optimizemember_file_remote:,E=optimizemember_file_remote:&optimizemember_file_remote=%2]

# Handle SSL file requests via the rewrite engine.
	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-ssl/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%2,E=optimizemember_file_ssl:,E=optimizemember_file_ssl:&optimizemember_file_ssl=yes]

	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-ssl-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%3,E=optimizemember_file_ssl:,E=optimizemember_file_ssl:&optimizemember_file_ssl=%2]

# Handle file download keys via the rewrite engine.
	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:optimizemember-file-download-key-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%3,E=optimizemember_file_download_key:,E=optimizemember_file_download_key:&optimizemember_file_download_key=%2]

# Handle confirmations having beek skipped via the rewrite engine.
	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:s2member-skip-confirmation/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%2,E=optimizemember_skip_confirmation:,E=optimizemember_skip_confirmation:&optimizemember_skip_confirmation=yes]

	RewriteCond %{ENV:optimizemember_file_download} ^(.*?)(?:s2member-skip-confirmation-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=optimizemember_file_download:,E=optimizemember_file_download:%1%3,E=optimizemember_skip_confirmation:,E=optimizemember_skip_confirmation:&optimizemember_skip_confirmation=%2]

# Cleanup variables not used in this request. Looking for `0` values.
	RewriteCond %{ENV:optimizemember_file_download_wp_vdir} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_file_download_wp_vdir:]
	
	RewriteCond %{ENV:optimizemember_file_stream} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_file_stream:]
	
	RewriteCond %{ENV:optimizemember_file_inline} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_file_inline:]
	
	RewriteCond %{ENV:optimizemember_file_storage} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_file_storage:]
	
	RewriteCond %{ENV:optimizemember_file_remote} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_file_remote:]
	
	RewriteCond %{ENV:optimizemember_file_ssl} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_file_ssl:]
	
	RewriteCond %{ENV:optimizemember_file_download_key} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_file_download_key:]
	
	RewriteCond %{ENV:optimizemember_skip_confirmation} ^0$
	RewriteRule ^(.*)$ - [E=optimizemember_skip_confirmation:]
	
# Put everything together now and process the internal rewrite.
	RewriteRule ^(.*)$ %{ENV:optimizemember_file_download_wp_vdir}?optimizemember_file_download=%{ENV:optimizemember_file_download}%{ENV:optimizemember_file_stream}%{ENV:optimizemember_file_inline}%{ENV:optimizemember_file_storage}%{ENV:optimizemember_file_remote}%{ENV:optimizemember_file_ssl}%{ENV:optimizemember_file_download_key}%{ENV:optimizemember_skip_confirmation} [QSA,L]
</IfModule>

<IfModule !mod_rewrite.c>
	deny from all
</IfModule>

<?php unset ($ws_plugin__optimizemember_temp_s_base); ?>