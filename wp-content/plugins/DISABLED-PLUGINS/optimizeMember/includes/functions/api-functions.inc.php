<?php
/**
* Core API Functions *( for site owners )*.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember\API_Functions
* @since 3.5
*/
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**
* Conditional to determine if the current User is NOT logged in.
*
* Counterpart {@link http://codex.wordpress.org/Function_Reference/is_user_logged_in is_user_logged_in()} already exists in the WordPress core.
*
* ———— Code Sample Using Both Functions ————
* ```
* <!php
* if(is_user_logged_in())
* 	echo 'You ARE logged in.';
* 	
* else if(is_user_not_logged_in())
* 	echo 'You are NOT logged in.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_user_logged_in()]
* 	You ARE logged in.
* [/opmIf]
* [opmIf is_user_not_logged_in()]
* 	You are NOT logged in.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @return bool True if the current User is NOT logged in, else false.
*
* @see http://codex.wordpress.org/Function_Reference/is_user_logged_in is_user_logged_in()
*/
if(!function_exists("is_user_not_logged_in"))
	{
		function is_user_not_logged_in()
			{
				return (!is_user_logged_in());
			}
	}
/**
* Conditional to determine if a specific User is/has a specific Role.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/user_can user_can()} already exists in the WordPress core.
*
* ———— Code Sample Using Both Functions ————
* ```
* <!php
* if(user_is(123, "subscriber"))
* 	echo 'User ID# 123 is a Free Subscriber at Level #0.';
* 	
* else if(user_is(123, "optimizemember_level1"))
* 	echo 'User ID# 123 is a Member at Level #1.';
* 	
* else if(user_can(123, "access_optimizemember_level2"))
* 	echo 'User ID# 123 has access to content protected at Level #2.';
* 	# But, (important) they could actually be a Level #3 or #4 Member;
* 	# because Membership Levels provide incremental access.
* !>
* ```
*
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf user_is(123, subscriber)]
* 	User ID# 123 is a Free Subscriber at Level #0.
* [/opmIf]
* [opmIf user_is(123, optimizemember_level1)]
* 	User ID# 123 is a Member at Level #1.
* [/opmIf]
* [opmIf user_can(123, access_optimizemember_level2)]
* 	User ID# 123 has access to content protected at Level #2.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 110524RC
*
* @param int|str $id A numeric WordPress User ID.
* @param str $role A WordPress Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the specific User is/has the specified Role, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("user_is"))
	{
		function user_is($id = FALSE, $role = FALSE)
			{
				$role = ($role === "optimizemember_level0") ? "subscriber" : preg_replace("/^access_/i", "", $role);
				/**/
				if(($role === "super_administrator" || $role === "administrator") && is_multisite() && is_super_admin($id))
					return true; /* Return true, Super Admins are always considered an Admnistrator, for all Blogs. */
				/**/
				else if(is_multisite() && is_super_admin($id)) /* Else return false for Super Admins here. */
					return false; /* Super Admins can access all Capabilities, so the default handling would fail. */
				/**/
				return user_can($id, $role);
			}
	}
/**
* Conditional to determine if a specific User is/does NOT have a specific Role.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/user_can user_can()} already exists in the WordPress core.
*
* ———— Code Sample Using Three Functions ————
* ```
* <!php
* if(user_is(123, "subscriber"))
* 	echo 'User ID# 123 is a Free Subscriber at Level #0.';
* 	
* else if(user_is(123, "optimizemember_level1"))
* 	echo 'User ID# 123 is a Member at Level #1.';
* 	
* else if(user_can(123, "access_optimizemember_level2") && user_is_not(123, "optimizemember_level2"))
* 	echo 'User ID# 123 has access to content protected at Level #2, but they are NOT a Level #2 Member.';
* 	# So, (important) they could actually be a Level #3 or #4 Member;
* 	# because Membership Levels provide incremental access.
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf user_is(123, subscriber)]
* 	User ID# 123 is a Free Subscriber at Level #0.
* [/opmIf]
* [opmIf user_is(123, optimizemember_level1)]
* 	User ID# 123 is a Member at Level #1.
* [/opmIf]
* [opmIf user_can(123, access_optimizemember_level2) AND user_is_not(123, optimizemember_level2)]
* 	User ID# 123 has access to content protected at Level #2, but they are NOT a Level #2 Member.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 110524RC
*
* @param int|str $id A numeric WordPress User ID.
* @param str $role A WordPress Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the specific User is/does NOT have the specified Role, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("user_is_not"))
	{
		function user_is_not($id = FALSE, $role = FALSE)
			{
				return (!user_is($id, $role));
			}
	}
/**
* Conditional to determine if the current User is/has a specific Role.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()} already exists in the WordPress core.
*
* ———— Code Sample Using Both Functions ————
* ```
* <!php
* if(current_user_is("subscriber"))
* 	echo 'You ARE a Free Subscriber at Level #0.';
* 	
* else if(current_user_is("optimizemember_level1"))
* 	echo 'You ARE a Member at Level #1.';
* 	
* else if(current_user_can("access_optimizemember_level2"))
* 	echo 'You DO have access to content protected at Level #2.';
* 	# But, (important) they could actually be a Level #3 or #4 Member;
* 	# because Membership Levels provide incremental access.
* !>
* ```
*
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf current_user_is(subscriber)]
* 	You ARE a Free Subscriber at Level #0.
* [/opmIf]
* [opmIf current_user_is(optimizemember_level1)]
* 	You ARE a Member at Level #1.
* [/opmIf]
* [opmIf current_user_can(access_optimizemember_level2)]
* 	You DO have access to content protected at Level #2.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $role A WordPress Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the current User is/has the specified Role, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("current_user_is"))
	{
		function current_user_is($role = FALSE)
			{
				$role = ($role === "optimizemember_level0") ? "subscriber" : preg_replace("/^access_/i", "", $role);
				/**/
				if(($role === "super_administrator" || $role === "administrator") && is_multisite() && is_super_admin())
					return true; /* Return true, Super Admins are always considered an Admnistrator, for all Blogs. */
				/**/
				else if(is_multisite() && is_super_admin()) /* Else return false for Super Admins here. */
					return false; /* Super Admins can access all Capabilities, so the default handling would fail. */
				/**/
				return current_user_can($role);
			}
	}
/**
* Conditional to determine if the current User is/does NOT have a specific Role.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()} already exists in the WordPress core.
*
* ———— Code Sample Using Three Functions ————
* ```
* <!php
* if(current_user_is("subscriber"))
* 	echo 'You ARE a Free Subscriber at Level #0.';
* 	
* else if(current_user_is("optimizemember_level1"))
* 	echo 'You ARE a Member at Level #1.';
* 	
* else if(current_user_can("access_optimizemember_level2") && current_user_is_not("optimizemember_level2"))
* 	echo 'You DO have access to content protected at Level #2, but you are NOT a Level #2 Member.';
* 	# So, (important) they could actually be a Level #3 or #4 Member;
* 	# because Membership Levels provide incremental access.
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf current_user_is(subscriber)]
* 	You ARE a Free Subscriber at Level #0.
* [/opmIf]
* [opmIf current_user_is(optimizemember_level1)]
* 	You ARE a Member at Level #1.
* [/opmIf]
* [opmIf current_user_can(access_optimizemember_level2) AND current_user_is_not(optimizemember_level2)]
* 	You DO have access to content protected at Level #2, but you are NOT a Level #2 Member.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $role A WordPress Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the current User is/does NOT have the specified Role, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("current_user_is_not"))
	{
		function current_user_is_not($role = FALSE)
			{
				return (!current_user_is($role));
			}
	}
/**
* Conditional to determine if the current User is/has a specific Role, on a specific Blog within a Multisite Network.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()} already exists in the WordPress core.
*
* ———— Code Sample Using Three Functions ————
* ```
* <!php
* if(current_user_is("subscriber"))
* 	echo 'You ARE a Free Subscriber at Level #0 ( on this Blog ).';
* 	
* else if(current_user_is_for_blog(5, "subscriber"))
* 	echo 'You ARE a Free Subscriber at Level #0 ( on Blog ID 5 ).';
* 	
* else if(current_user_is_for_blog(5, "optimizemember_level1"))
* 	echo 'You ARE a Member at Level #1 ( on Blog ID 5 ).';
* 	
* else if(current_user_can_for_blog(5, "access_optimizemember_level2"))
* 	echo 'You DO have access to content protected at Level #2 ( on Blog ID 5 ).';
* 	# But, (important) they could actually be a Level #3 or #4 Member ( on Blog ID 5 );
* 	# because Membership Levels provide incremental access.
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf current_user_is(subscriber)]
* 	You ARE a Free Subscriber at Level #0 ( on this Blog ).
* [/opmIf]
* [opmIf current_user_is_for_blog(5, subscriber)]
* 	You ARE a Free Subscriber at Level #0 ( on Blog ID 5 ).
* [/opmIf]
* [opmIf current_user_is_for_blog(5, optimizemember_level1)]
* 	You ARE a Member at Level #1 ( on Blog ID 5 ).
* [/opmIf]
* [opmIf current_user_can_for_blog(5, access_optimizemember_level2)]
* 	You DO have access to content protected at Level #2 ( on Blog ID 5 ).
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $blog_id A WordPress Blog ID *( must be numeric )*.
* @param str $role A WordPress Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the current User is/has the specified Role, on the specified Blog, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("current_user_is_for_blog"))
	{
		function current_user_is_for_blog($blog_id = FALSE, $role = FALSE)
			{
				$role = ($role === "optimizemember_level0") ? "subscriber" : preg_replace("/^access_/i", "", $role);
				/**/
				if(($role === "super_administrator" || $role === "administrator") && is_multisite() && is_super_admin())
					return true; /* Return true, Super Admins are always considered an Admnistrator, for all Blogs. */
				/**/
				else if(is_multisite() && is_super_admin()) /* Else return false for Super Admins here. */
					return false; /* Super Admins can access all Capabilities, so the default handling would fail. */
				/**/
				return current_user_can_for_blog($blog_id, $role);
			}
	}
/**
* Conditional to determine if the current User is/does NOT have a specific Role, on a specific Blog within a Multisite Network.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()} already exists in the WordPress core.
*
* ———— Code Sample Using Three Functions ————
* ```
* <!php
* if(current_user_is_for_blog(5, "subscriber"))
* 	echo 'You ARE a Free Subscriber at Level #0 ( on Blog ID 5 ).';
* 	
* else if(current_user_can_for_blog(5, "access_optimizemember_level1") && current_user_is_not_for_blog(5, "optimizemember_level1"))
* 	echo 'You DO have access to content protected at Level #1 ( on Blog ID 5 ), but you are NOT a Level #1 Member ( on Blog ID 5 ).';
* 	# So, (important) they could actually be a Level #2 or #3 or #4 Member ( on Blog ID 5 );
* 	# because Membership Levels provide incremental access.
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf current_user_is_for_blog(5, subscriber)]
* 	You ARE a Free Subscriber at Level #0 ( on Blog ID 5 ).
* [/opmIf]
* [opmIf current_user_can_for_blog(5, access_optimizemember_level1) AND current_user_is_not_for_blog(5, optimizemember_level1)]
* 	You DO have access to content protected at Level #1 ( on Blog ID 5 ), but you are NOT a Level #1 Member ( on Blog ID 5 ).
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $blog_id A WordPress Blog ID *( must be numeric )*.
* @param str $role A WordPress Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the current User is/does NOT have the specified Role, on the specified Blog, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("current_user_is_not_for_blog"))
	{
		function current_user_is_not_for_blog($blog_id = FALSE, $role = FALSE)
			{
				return (!current_user_is_for_blog($blog_id, $role));
			}
	}
/**
* Conditional to determine if a specific User does NOT have a specific Capability or Role.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/user_can user_can()} already exists in the WordPress core.
*
* ———— Code Sample Using Both Functions ————
* ```
* <!php
* if(user_can(123, "access_optimizemember_level0"))
* 	echo 'User ID# 123 CAN access content protected at Level #0.';
* 	
* else if(user_cannot(123, "access_optimizemember_level0"))
* 	echo 'User ID# 123 CANNOT access content at Level #0.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf user_can(123, access_optimizemember_level0)]
* 	User ID# 123 CAN access content protected at Level #0.
* [/opmIf]
* [opmIf user_cannot(123, access_optimizemember_level0)]
* 	User ID# 123 CANNOT access content at Level #0.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $id A numeric WordPress User ID.
* @param str $capability A WordPress Capability ID *( i.e. `access_optimizemember_level[0-9]+`, `access_optimizemember_ccap_music` )*.
* @return bool True if the specific User does NOT have the specified Capability or Role, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("user_cannot"))
	{
		function user_cannot($id = FALSE, $capability = FALSE)
			{
				return (!user_can($id, $capability));
			}
	}
/**
* Conditional to determine if the current User does NOT have a specific Capability or Role.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()} already exists in the WordPress core.
*
* ———— Code Sample Using Both Functions ————
* ```
* <!php
* if(current_user_can("access_optimizemember_level0"))
* 	echo 'You CAN access content protected at Level #0.';
* 	
* else if(current_user_cannot("access_optimizemember_level0"))
* 	echo 'You CANNOT access content protected at Level #0.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf current_user_can(access_optimizemember_level0)]
* 	You CAN access content protected at Level #0.
* [/opmIf]
* [opmIf current_user_cannot(access_optimizemember_level0)]
* 	You CANNOT access content protected at Level #0.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $capability A WordPress Capability ID *( i.e. `access_optimizemember_level[0-9]+`, `access_optimizemember_ccap_music` )*.
* 	Or a Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the current User does NOT have the specified Capability or Role, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("current_user_cannot"))
	{
		function current_user_cannot($capability = FALSE)
			{
				return (!current_user_can($capability));
			}
	}
/**
* Conditional to determine if the current User does NOT have a specific Capability or Role, on a specific Blog within a Multisite Network.
*
* Another function {@link http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()} already exists in the WordPress core.
*
* ———— Code Sample Using Both Functions ————
* ```
* <!php
* if(current_user_can_for_blog(5, "access_optimizemember_level0"))
* 	echo 'You CAN access content protected at Level #0 ( on Blog ID 5 ).';
* 	
* else if(current_user_cannot_for_blog(5, "access_optimizemember_level0"))
* 	echo 'You CANNOT access content protected at Level #0 ( on Blog ID 5 ).';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf current_user_can_for_blog(5, access_optimizemember_level0)]
* 	You CAN access content protected at Level #0 ( on Blog ID 5 ).
* [/opmIf]
* [opmIf current_user_cannot_for_blog(5, access_optimizemember_level0)]
* 	You CANNOT access content protected at Level #0 ( on Blog ID 5 ).
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* ———— Membership Levels Provide Incremental Access ————
*
* o A Member with Level 4 access, will also be able to access Levels 0, 1, 2, 3.
* o A Member with Level 3 access, will also be able to access Levels 0, 1, 2.
* o A Member with Level 2 access, will also be able to access Levels 0, 1
* o A Member with Level 1 access, will also be able to access Level 0.
* o A Subscriber with Level 0 access, can ONLY access Level 0.
* o A public Visitor will have NO access to protected content.
*
* WordPress Subscribers are at Membership Level 0. If you're allowing Open Registration, Subscribers will be at Level 0 *( a Free Subscriber )*.
* WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to optimizeMember.
* All of their other {@link http://codex.wordpress.org/Roles_and_Capabilities Roles/Capabilities} are left untouched.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $blog_id A WordPress Blog ID *( must be numeric )*.
* @param str $capability A WordPress Capability ID *( i.e. `access_optimizemember_level[0-9]+`, `access_optimizemember_ccap_music` )*.
* 	Or a Role ID *( i.e. `optimizemember_level[0-9]+`, `administrator`, `editor`, `author`, `contributor`, `subscriber` )*.
* @return bool True if the current User does NOT have the specified Capability or Role, else false.
*
* @see optimizeMember\API_Functions\user_is()
* @see optimizeMember\API_Functions\user_is_not()
*
* @see optimizeMember\API_Functions\current_user_is()
* @see optimizeMember\API_Functions\current_user_is_not()
* @see optimizeMember\API_Functions\current_user_is_for_blog()
* @see optimizeMember\API_Functions\current_user_is_not_for_blog()
*
* @see optimizeMember\API_Functions\user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot()
* @see optimizeMember\API_Functions\current_user_cannot_for_blog()
*
* @see http://codex.wordpress.org/Function_Reference/user_can user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
* @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
*/
if(!function_exists("current_user_cannot_for_blog"))
	{
		function current_user_cannot_for_blog($blog_id = FALSE, $capability = FALSE)
			{
				return (!current_user_can_for_blog($blog_id, $capability));
			}
	}
/**
* Conditional to determine if a specific Category, Tag, Post, Page, URL or URI is protected by optimizeMember;
* without considering the current User's Role/Capabilites.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $what ( int|str Optional ).**
* 	Defaults to the current $post ID when called from within {@link http://codex.wordpress.org/The_Loop The Loop}.
* 	If passed in, this should be a WordPress Category ID, Tag ID, Post ID, or Page ID. Or a full URL. A URI is also fine.
*
* 	o If you pass in an ID, optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* 	o If you pass in a URL or URI, optimizeMember will ONLY check URI Restrictions, because it has no ID to work with.
* 	This is useful though. Some protected content is not associated with an ID. In those cases, URI Restrictions are all the matter.
*
* 	o Note: when passing in a URL or URI, the $type parameter must be set to `URI` or `uri`. Case insensitive.
*
* **Parameter $type ( str Optional ).**
* 	One of `category`, `tag`, `post`, `page`, `singular` or `uri`. Defaults to `singular` *( i.e. a Post or Page )*.
*
* **Parameter $check_user ( bool Optional ).**
* 	Consider the current User? Defaults to false.
*
* 	o In other words, by default, this Conditional function is only checking to see if the content is protected, and that's it.
* 	o So this function does NOT consider the current User's Role or Capabilities. If you set $check_user to true, it will.
* 	o When $check_user is true, this function behaves like {@link optimizeMember\API_Functions\is_permitted_by_optimizemember()}.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_protected_by_optimizemember(123))
* 	echo 'Post or Page ID #123 is protected by optimizeMember.';
* 	
* else if(is_protected_by_optimizemember(332, "tag"))
* 	echo 'Tag ID #332 is protected by optimizeMember.';
* 	
* else if(is_protected_by_optimizemember(554, "category"))
* 	echo 'Category ID #554 is protected by optimizeMember.';
* 	
* else if(is_protected_by_optimizemember("http://example.com/members/", "uri"))
* 	echo 'This URL is protected by URI Restrictions.';
* 	
* else if(is_protected_by_optimizemember("/members/", "uri"))
* 	echo 'This URI is protected by URI Restrictions.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_protected_by_optimizemember(123)]
* 	Post or Page ID #123 is protected by optimizeMember.
* [/opmIf]
* [opmIf is_protected_by_optimizemember(332, tag)]
* 	Tag ID #332 is protected by optimizeMember.
* [/opmIf]
* [opmIf is_protected_by_optimizemember(554, category)]
* 	Category ID #554 is protected by optimizeMember.
* [/opmIf]
* [opmIf is_protected_by_optimizemember(http://example.com/members/, uri)]
* 	This URL is protected by URI Restrictions.
* [/opmIf]
* [opmIf is_protected_by_optimizemember(/members/, uri)]
* 	This URI is protected by URI Restrictions.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $what Optional. Defaults to the current $post ID when called from within {@link http://codex.wordpress.org/The_Loop The Loop}.
* 	If passed in, this should be a WordPress Category ID, Tag ID, Post ID, or Page ID. Or a full URL. A URI is also fine.
* @param str $type Optional. One of `category`, `tag`, `post`, `page`, `singular` or `uri`. Defaults to `singular` *( i.e. a Post or Page )*.
* @param bool $check_user Optional. Consider the current User? Defaults to false.
* @return array|bool A non-empty array *( meaning true )*, or false if the content is not protected *( i.e. available publicly )*.
* 	When/if content IS protected, the return array will include one of these keys ``["optimizemember_(level|sp|ccap)_req"]``
* 	indicating the Level #, Specific Post/Page ID #, or Custom Capability required to access the content.
* 	In other words, the reason why it's protected; based on your optimizeMember configuration.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_protected_by_optimizemember"))
	{
		function is_protected_by_optimizemember($what = FALSE, $type = FALSE, $check_user = FALSE)
			{
				global $post; /* Global reference to $post in The Loop. */
				/**/
				$what = ($what) ? $what : ((is_object($post) && $post->ID) ? $post->ID : false);
				$type = ($type) ? strtolower($type) : "singular";
				/**/
				if($type === "category" && ($array = c_ws_plugin__optimizemember_catgs_sp::check_specific_catg_level_access($what, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_level_req"]. */
				/**/
				else if($type === "tag" && ($array = c_ws_plugin__optimizemember_ptags_sp::check_specific_ptag_level_access($what, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_level_req"]. */
				/**/
				else if(($type === "post" || $type === "singular") && ($array = c_ws_plugin__optimizemember_posts_sp::check_specific_post_level_access($what, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_(level|sp|ccap)_req"]. */
				/**/
				else if(($type === "page" || $type === "singular") && ($array = c_ws_plugin__optimizemember_pages_sp::check_specific_page_level_access($what, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_(level|sp|ccap)_req"]. */
				/**/
				else if($type === "uri" && ($array = c_ws_plugin__optimizemember_ruris_sp::check_specific_ruri_level_access($what, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_level_req"]. */
				/**/
				return false;
			}
	}
/**
* Conditional to determine if a specific Category, Tag, Post, Page, URL or URI is permitted by optimizeMember,
* with consideration given to the current User's Role/Capabilites.
*
* This function is similar to {@link optimizeMember\API_Functions\is_protected_by_optimizemember()}, except this function considers the current User's Role/Capabilites.
* Also, this function does NOT return the array like {@link optimizeMember\API_Functions\is_protected_by_optimizemember()} does; it only returns true|false.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $what ( int|str Optional ).**
* 	Defaults to the current $post ID when called from within {@link http://codex.wordpress.org/The_Loop The Loop}.
* 	If passed in, this should be a WordPress Category ID, Tag ID, Post ID, or Page ID. Or a full URL. A URI is also fine.
*
* 	o If you pass in an ID, optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* 	o If you pass in a URL or URI, optimizeMember will ONLY check URI Restrictions, because it has no ID to work with.
* 	This is useful though. Some protected content is not associated with an ID. In those cases, URI Restrictions are all the matter.
*
* 	o Note: when passing in a URL or URI, the $type parameter must be set to `URI` or `uri`. Case insensitive.
*
* **Parameter $type ( str Optional ).**
* 	One of `category`, `tag`, `post`, `page`, `singular` or `uri`. Defaults to `singular` *( i.e. a Post or Page )*.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_permitted_by_optimizemember(123))
* 	echo 'Post or Page ID #123 is permitted by optimizeMember.';
* 	
* else if(is_permitted_by_optimizemember(332, "tag"))
* 	echo 'Tag ID #332 is permitted by optimizeMember.';
* 	
* else if(is_permitted_by_optimizemember(554, "category"))
* 	echo 'Category ID #554 is permitted by optimizeMember.';
* 	
* else if(is_permitted_by_optimizemember("http://example.com/members/", "uri"))
* 	echo 'This URL is permitted by optimizeMember.';
* 	
* else if(is_permitted_by_optimizemember("/members/", "uri"))
* 	echo 'This URI is permitted by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_permitted_by_optimizemember(123)]
* 	Post or Page ID #123 is permitted by optimizeMember.
* [/opmIf]
* [opmIf is_permitted_by_optimizemember(332, tag)]
* 	Tag ID #332 is permitted by optimizeMember.
* [/opmIf]
* [opmIf is_permitted_by_optimizemember(554, category)]
* 	Category ID #554 is permitted by optimizeMember.
* [/opmIf]
* [opmIf is_permitted_by_optimizemember(http://example.com/members/, uri)]
* 	This URL is permitted by optimizeMember.
* [/opmIf]
* [opmIf is_permitted_by_optimizemember(/members/, uri)]
* 	This URI is permitted by optimizeMember.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $what Optional. Defaults to the current $post ID when called from within {@link http://codex.wordpress.org/The_Loop The Loop}.
* 	If passed in, this should be a WordPress Category ID, Tag ID, Post ID, or Page ID. Or a full URL. A URI is also fine.
* @param str $type Optional. One of `category`, `tag`, `post`, `page`, `singular` or `uri`. Defaults to `singular` *( i.e. a Post or Page )*.
* @return bool True if the current User IS permitted, else false if the content is NOT available to the current User;
* 	based on your configuration of optimizeMember, and based on the current User's Role/Capabilities.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_permitted_by_optimizemember"))
	{
		function is_permitted_by_optimizemember($what = FALSE, $type = FALSE)
			{
				global $post; /* Global reference to $post in The Loop. */
				/**/
				$what = ($what) ? $what : ((is_object($post) && $post->ID) ? $post->ID : false);
				$type = ($type) ? strtolower($type) : "singular";
				/**/
				if($type === "category" && c_ws_plugin__optimizemember_catgs_sp::check_specific_catg_level_access($what, true))
					return false;
				/**/
				else if($type === "tag" && c_ws_plugin__optimizemember_ptags_sp::check_specific_ptag_level_access($what, true))
					return false;
				/**/
				else if(($type === "post" || $type === "singular") && c_ws_plugin__optimizemember_posts_sp::check_specific_post_level_access($what, true))
					return false;
				/**/
				else if(($type === "page" || $type === "singular") && c_ws_plugin__optimizemember_pages_sp::check_specific_page_level_access($what, true))
					return false;
				/**/
				else if($type === "uri" && c_ws_plugin__optimizemember_ruris_sp::check_specific_ruri_level_access($what, true))
					return false;
				/**/
				return true;
			}
	}
/**
* Conditional to determine if a specific Category is protected by optimizeMember;
* without considering the current User's Role/Capabilites.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $cat_id ( int Required ).** This should be a WordPress Category ID.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* **Parameter $check_user ( bool Optional ).**
* 	Consider the current User? Defaults to false.
*
* 	o In other words, by default, this Conditional function is only checking to see if the Category is protected, and that's it.
* 	o So this function does NOT consider the current User's Role or Capabilities. If you set $check_user to true, it will.
* 	o When $check_user is true, this function behaves like {@link optimizeMember\API_Functions\is_category_permitted_by_optimizemember()}.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_category_protected_by_optimizemember(123))
* 	echo 'Category ID #123 is protected by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_category_protected_by_optimizemember(123)]
* 	Category ID #123 is protected by optimizeMember.
* [/opmIf]
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int $cat_id Required. This should be a WordPress Category ID.
* @param bool $check_user Optional. Consider the current User? Defaults to false.
* @return array|bool A non-empty array *( meaning true )*, or false if the Category is not protected *( i.e. available publicly )*.
* 	When/if the Category IS protected, the return array will include one of these keys ``["optimizemember_(level|sp|ccap)_req"]``
* 	indicating the Level #, Specific Post/Page ID #, or Custom Capability required to access the Category.
* 	In other words, the reason why it's protected; based on your optimizeMember configuration.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_category_protected_by_optimizemember"))
	{
		function is_category_protected_by_optimizemember($cat_id = FALSE, $check_user = FALSE)
			{
				if($cat_id && ($array = c_ws_plugin__optimizemember_catgs_sp::check_specific_catg_level_access($cat_id, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_level_req"]. */
				/**/
				return false;
			}
	}
/**
* Conditional to determine if a specific Category is permitted by optimizeMember,
* with consideration given to the current User's Role/Capabilites.
*
* This function is similar to {@link optimizeMember\API_Functions\is_category_protected_by_optimizemember()}, except this function considers the current User's Role/Capabilites.
* Also, this function does NOT return the array like {@link optimizeMember\API_Functions\is_category_protected_by_optimizemember()} does; it only returns true|false.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $cat_id ( int Required ).** This should be a WordPress Category ID.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_category_permitted_by_optimizemember(123))
* 	echo 'Category ID #123 is permitted by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_category_permitted_by_optimizemember(123)]
* 	Category ID #123 is permitted by optimizeMember.
* [/opmIf]
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int $cat_id Required. This should be a WordPress Category ID.
* @return bool True if the current User IS permitted, else false if the Category is NOT available to the current User;
* 	based on your configuration of optimizeMember, and based on the current User's Role/Capabilities.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_category_permitted_by_optimizemember"))
	{
		function is_category_permitted_by_optimizemember($cat_id = FALSE)
			{
				if($cat_id && c_ws_plugin__optimizemember_catgs_sp::check_specific_catg_level_access($cat_id, true))
					return false;
				/**/
				return true;
			}
	}
/**
* Conditional to determine if a specific Tag is protected by optimizeMember;
* without considering the current User's Role/Capabilites.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $tag_id_slug_or_name ( int|str Required ).** This should be a WordPress Tag ID, Tag Slug, or Tag Name.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID, Slug, or Name.
* 	In other words, optimizeMember is capable of determining a URI based on the ID, or Slug, or Name that you pass in.
* 	So using an ID, or Slug, or Name results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* **Parameter $check_user ( bool Optional ).**
* 	Consider the current User? Defaults to false.
*
* 	o In other words, by default, this Conditional function is only checking to see if the Tag is protected, and that's it.
* 	o So this function does NOT consider the current User's Role or Capabilities. If you set $check_user to true, it will.
* 	o When $check_user is true, this function behaves like {@link optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()}.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_tag_protected_by_optimizemember(123))
* 	echo 'Tag ID #123 is protected by optimizeMember.';
* 	
* else if(is_tag_protected_by_optimizemember("members-only"))
* 	echo 'Tag Slug (members-only) is protected by optimizeMember.';
* 	
* else if(is_tag_protected_by_optimizemember("Members Only"))
* 	echo 'Tag Name (Members Only) is protected by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_tag_protected_by_optimizemember(123)]
* 	Tag ID #123 is protected by optimizeMember.
* [/opmIf]
* [opmIf is_tag_protected_by_optimizemember(members-only)]
* 	Tag Slug (members-only) is protected by optimizeMember.
* [/opmIf]
* NOTE: It's NOT possible to check a Tag Named "Members Only" with [opmIf /],
* because Shortcode Conditionals may NOT contain spaces in their argument values.
* If you're using [opmIf /] to check a Tag, please use the Slug or ID instead.
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $tag_id_slug_or_name Required. This should be a WordPress Tag ID, Tag Slug, or Tag Name.
* @param bool $check_user Optional. Consider the current User? Defaults to false.
* @return array|bool A non-empty array *( meaning true )*, or false if the Tag is not protected *( i.e. available publicly )*.
* 	When/if the Tag IS protected, the return array will include one of these keys ``["optimizemember_(level|sp|ccap)_req"]``
* 	indicating the Level #, Specific Post/Page ID #, or Custom Capability required to access the Tag.
* 	In other words, the reason why it's protected; based on your optimizeMember configuration.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_tag_protected_by_optimizemember"))
	{
		function is_tag_protected_by_optimizemember($tag_id_slug_or_name = FALSE, $check_user = FALSE)
			{
				if($tag_id_slug_or_name && ($array = c_ws_plugin__optimizemember_ptags_sp::check_specific_ptag_level_access($tag_id_slug_or_name, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_level_req"]. */
				/**/
				return false;
			}
	}
/**
* Conditional to determine if a specific Tag is permitted by optimizeMember,
* with consideration given to the current User's Role/Capabilites.
*
* This function is similar to {@link optimizeMember\API_Functions\is_tag_protected_by_optimizemember()}, except this function considers the current User's Role/Capabilites.
* Also, this function does NOT return the array like {@link optimizeMember\API_Functions\is_tag_protected_by_optimizemember()} does; it only returns true|false.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $tag_id_slug_or_name ( int|str Required ).** This should be a WordPress Tag ID, Tag Slug, or Tag Name.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID, or Slug, or Name.
* 	In other words, optimizeMember is capable of determining a URI based on the ID, or Slug, or Name that you pass in.
* 	So using an ID, or Slug, or Name results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_tag_permitted_by_optimizemember(123))
* 	echo 'Tag ID #123 is permitted by optimizeMember.';
* 	
* else if(is_tag_permitted_by_optimizemember("members-only"))
* 	echo 'Tag Slug (members-only) is permitted by optimizeMember.';
* 	
* else if(is_tag_permitted_by_optimizemember("Members Only"))
* 	echo 'Tag Name (Members Only) is permitted by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_tag_permitted_by_optimizemember(123)]
* 	Tag ID #123 is permitted by optimizeMember.
* [/opmIf]
* [opmIf is_tag_permitted_by_optimizemember(members-only)]
* 	Tag Slug (members-only) is permitted by optimizeMember.
* [/opmIf]
* NOTE: It's NOT possible to check a Tag Named "Members Only" with [opmIf /],
* because Shortcode Conditionals may NOT contain spaces in their argument values.
* If you're using [opmIf /] to check a Tag, please use the Slug or ID instead.
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int|str $tag_id_slug_or_name Required. This should be a WordPress Tag ID, Tag Slug, or Tag Name.
* @return bool True if the current User IS permitted, else false if the Tag is NOT available to the current User;
* 	based on your configuration of optimizeMember, and based on the current User's Role/Capabilities.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_tag_permitted_by_optimizemember"))
	{
		function is_tag_permitted_by_optimizemember($tag_id_slug_or_name = FALSE)
			{
				if($tag_id_slug_or_name && c_ws_plugin__optimizemember_ptags_sp::check_specific_ptag_level_access($tag_id_slug_or_name, true))
					return false;
				/**/
				return true;
			}
	}
/**
* Conditional to determine if a specific Post ( or Custom Post Type ) is protected by optimizeMember;
* without considering the current User's Role/Capabilites.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $post_id ( int Required ).** This should be a WordPress Post ID, or a Custom Post Type ID.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* **Parameter $check_user ( bool Optional ).**
* 	Consider the current User? Defaults to false.
*
* 	o In other words, by default, this Conditional function is only checking to see if the Post is protected, and that's it.
* 	o So this function does NOT consider the current User's Role or Capabilities. If you set $check_user to true, it will.
* 	o When $check_user is true, this function behaves like {@link optimizeMember\API_Functions\is_post_permitted_by_optimizemember()}.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_post_protected_by_optimizemember(123))
* 	echo 'Post ID #123 is protected by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_post_protected_by_optimizemember(123)]
* 	Post ID #123 is protected by optimizeMember.
* [/opmIf]
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int $post_id Required. This should be a WordPress Post ID, or a Custom Post Type ID.
* @param bool $check_user Optional. Consider the current User? Defaults to false.
* @return array|bool A non-empty array *( meaning true )*, or false if the Post is not protected *( i.e. available publicly )*.
* 	When/if the Post IS protected, the return array will include one of these keys ``["optimizemember_(level|sp|ccap)_req"]``
* 	indicating the Level #, Specific Post/Page ID #, or Custom Capability required to access the Post.
* 	In other words, the reason why it's protected; based on your optimizeMember configuration.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_post_protected_by_optimizemember"))
	{
		function is_post_protected_by_optimizemember($post_id = FALSE, $check_user = FALSE)
			{
				if($post_id && ($array = c_ws_plugin__optimizemember_posts_sp::check_specific_post_level_access($post_id, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_(level|sp|ccap)_req"]. */
				/**/
				return false;
			}
	}
/**
* Conditional to determine if a specific Post or Custom Post Type is permitted by optimizeMember,
* with consideration given to the current User's Role/Capabilites.
*
* This function is similar to {@link optimizeMember\API_Functions\is_post_protected_by_optimizemember()}, except this function considers the current User's Role/Capabilites.
* Also, this function does NOT return the array like {@link optimizeMember\API_Functions\is_post_protected_by_optimizemember()} does; it only returns true|false.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $post_id ( int Required ).** This should be a WordPress Post ID, or a Custom Post Type ID.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_post_permitted_by_optimizemember(123))
* 	echo 'Post ID #123 is permitted by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_post_permitted_by_optimizemember(123)]
* 	Post ID #123 is permitted by optimizeMember.
* [/opmIf]
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int $post_id Required. This should be a WordPress Post ID, or a Custom Post Type ID.
* @return bool True if the current User IS permitted, else false if the Post is NOT available to the current User;
* 	based on your configuration of optimizeMember, and based on the current User's Role/Capabilities.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_post_permitted_by_optimizemember"))
	{
		function is_post_permitted_by_optimizemember($post_id = FALSE)
			{
				if($post_id && c_ws_plugin__optimizemember_posts_sp::check_specific_post_level_access($post_id, true))
					return false;
				/**/
				return true;
			}
	}
/**
* Conditional to determine if a specific Page is protected by optimizeMember;
* without considering the current User's Role/Capabilites.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $page_id ( int Required ).** This should be a WordPress Page ID.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* **Parameter $check_user ( bool Optional ).**
* 	Consider the current User? Defaults to false.
*
* 	o In other words, by default, this Conditional function is only checking to see if the Page is protected, and that's it.
* 	o So this function does NOT consider the current User's Role or Capabilities. If you set $check_user to true, it will.
* 	o When $check_user is true, this function behaves like {@link optimizeMember\API_Functions\is_page_permitted_by_optimizemember()}.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_page_protected_by_optimizemember(123))
* 	echo 'Page ID #123 is protected by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_page_protected_by_optimizemember(123)]
* 	Page ID #123 is protected by optimizeMember.
* [/opmIf]
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int $page_id Required. This should be a WordPress Page ID.
* @param bool $check_user Optional. Consider the current User? Defaults to false.
* @return array|bool A non-empty array *( meaning true )*, or false if the Page is not protected *( i.e. available publicly )*.
* 	When/if the Page IS protected, the return array will include one of these keys ``["optimizemember_(level|sp|ccap)_req"]``
* 	indicating the Level #, Specific Post/Page ID #, or Custom Capability required to access the Page.
* 	In other words, the reason why it's protected; based on your optimizeMember configuration.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_page_protected_by_optimizemember"))
	{
		function is_page_protected_by_optimizemember($page_id = FALSE, $check_user = FALSE)
			{
				if($page_id && ($array = c_ws_plugin__optimizemember_pages_sp::check_specific_page_level_access($page_id, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_(level|sp|ccap)_req"]. */
				/**/
				return false;
			}
	}
/**
* Conditional to determine if a specific Page is permitted by optimizeMember,
* with consideration given to the current User's Role/Capabilites.
*
* This function is similar to {@link optimizeMember\API_Functions\is_page_protected_by_optimizemember()}, except this function considers the current User's Role/Capabilites.
* Also, this function does NOT return the array like {@link optimizeMember\API_Functions\is_page_protected_by_optimizemember()} does; it only returns true|false.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $page_id ( int Required ).** This should be a WordPress Page ID.
*
* 	o optimizeMember will check everything, including your configured URI Restrictions against the ID.
* 	In other words, optimizeMember is capable of determining a URI based on the ID that you pass in.
* 	So using an ID results in an all-inclusive scan against your configured Restrictions,
* 	including any URI Restrictions that you may have configured.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_page_permitted_by_optimizemember(123))
* 	echo 'Page ID #123 is permitted by optimizeMember.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_page_permitted_by_optimizemember(123)]
* 	Page ID #123 is permitted by optimizeMember.
* [/opmIf]
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int $page_id Required. This should be a WordPress Page ID.
* @return bool True if the current User IS permitted, else false if the Page is NOT available to the current User;
* 	based on your configuration of optimizeMember, and based on the current User's Role/Capabilities.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_page_permitted_by_optimizemember"))
	{
		function is_page_permitted_by_optimizemember($page_id = FALSE)
			{
				if($page_id && c_ws_plugin__optimizemember_pages_sp::check_specific_page_level_access($page_id, true))
					return false;
				/**/
				return true;
			}
	}
/**
* Conditional to determine if a specific URI or URL is protected by optimizeMember;
* without considering the current User's Role/Capabilites.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $uri_or_full_url ( str Required ).** This should be a URI starting with `/`, or a full URL is also fine.
*
* **Parameter $check_user ( bool Optional ).**
* 	Consider the current User? Defaults to false.
*
* 	o In other words, by default, this Conditional function is only checking to see if the URI or URL is protected, and that's it.
* 	o So this function does NOT consider the current User's Role or Capabilities. If you set $check_user to true, it will.
* 	o When $check_user is true, this function behaves like {@link optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()}.
*
* ———— Important Notes About This Function ————
*
* This function will ONLY test against URI Restrictions you've configured with optimizeMember.
* If you need an all-inclusive test, please use {@link optimizeMember\API_Functions\is_protected_by_optimizemember()} with an ID.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_uri_protected_by_optimizemember("/members-only/sub-section"))
* 	echo 'The URI (/members-only/sub-section) is protected by URI Restrictions.';
* 	
* else if(is_uri_protected_by_optimizemember("http://example.com/members-only/sub-section"))
* 	echo 'The URL (http://example.com/members-only/sub-section) is protected by URI Restrictions.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_uri_protected_by_optimizemember(/members-only/sub-section)]
* 	The URI (/members-only/sub-section) is protected by URI Restrictions.
* [/opmIf]
* [opmIf is_uri_protected_by_optimizemember(http://example.com/members-only/sub-section)]
* 	The URL (http://example.com/members-only/sub-section) is protected by URI Restrictions.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $uri_or_full_url Required. This should be a URI starting with `/`, or a full URL is also fine.
* @param bool $check_user Optional. Consider the current User? Defaults to false.
* @return array|bool A non-empty array *( meaning true )*, or false if the URI or URL is not protected *( i.e. available publicly )*.
* 	When/if the URI or URL IS protected, the return array will include one of these keys ``["optimizemember_(level|sp|ccap)_req"]``
* 	indicating the Level #, Specific Post/Page ID #, or Custom Capability required to access the URI or URL.
* 	In other words, the reason why it's protected; based on your optimizeMember configuration.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_uri_protected_by_optimizemember"))
	{
		function is_uri_protected_by_optimizemember($uri_or_full_url = FALSE, $check_user = FALSE)
			{
				if($uri_or_full_url && ($array = c_ws_plugin__optimizemember_ruris_sp::check_specific_ruri_level_access($uri_or_full_url, $check_user)))
					return $array; /* A non-empty array with ["optimizemember_level_req"]. */
				/**/
				return false;
			}
	}
/**
* Conditional to determine if a specific URI or URL is permitted by optimizeMember,
* with consideration given to the current User's Role/Capabilites.
*
* This function is similar to {@link optimizeMember\API_Functions\is_uri_protected_by_optimizemember()}, except this function considers the current User's Role/Capabilites.
* Also, this function does NOT return the array like {@link optimizeMember\API_Functions\is_uri_protected_by_optimizemember()} does; it only returns true|false.
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $uri_or_full_url ( str Required ).** This should be a URI starting with `/`, or a full URL is also fine.
*
* ———— Important Notes About This Function ————
*
* This function will ONLY test against URI Restrictions you've configured with optimizeMember.
* If you need an all-inclusive test, please use {@link optimizeMember\API_Functions\is_permitted_by_optimizemember()} with an ID.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(is_uri_permitted_by_optimizemember("/members-only/sub-section"))
* 	echo 'The URI (/members-only/sub-section) is permitted by URI Restrictions.';
* 	
* else if(is_uri_permitted_by_optimizemember("http://example.com/members-only/sub-section"))
* 	echo 'The URL (http://example.com/members-only/sub-section) is permitted by URI Restrictions.';
* !>
* ```
* ———— Shortcode Conditional Equivalent ————
* ```
* [opmIf is_uri_permitted_by_optimizemember(/members-only/sub-section)]
* 	The URI (/members-only/sub-section) is permitted by URI Restrictions.
* [/opmIf]
* [opmIf is_uri_permitted_by_optimizemember(http://example.com/members-only/sub-section)]
* 	The URL (http://example.com/members-only/sub-section) is permitted by URI Restrictions.
* [/opmIf]
* ```
* *but please note, `else if()` logic is not possible with `[opmIf /]`.*
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $uri_or_full_url Required. This should be a URI starting with `/`, or a full URL is also fine.
* @return bool True if the current User IS permitted, else false if the URI or URL is NOT available to the current User;
* 	based on your configuration of optimizeMember, and based on the current User's Role/Capabilities.
*
* @see optimizeMember\API_Functions\is_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_category_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_category_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_tag_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_tag_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_post_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_post_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_page_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_page_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\is_uri_protected_by_optimizemember()
* @see optimizeMember\API_Functions\is_uri_permitted_by_optimizemember()
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("is_uri_permitted_by_optimizemember"))
	{
		function is_uri_permitted_by_optimizemember($uri_or_full_url = FALSE)
			{
				if($uri_or_full_url && c_ws_plugin__optimizemember_ruris_sp::check_specific_ruri_level_access($uri_or_full_url, true))
					return false;
				/**/
				return true;
			}
	}
/**
* Allows plugin/theme developers to pre-filter WP Queries easily, so that protected content
* *( i.e content NOT available to the current User )*, is excluded automatically.
*
* This functionality is already built right into optimizeMember's UI configuration panels,
* but in cases where a plugin/theme developer needs more control, this may come in handy.
* In the UI configuration for optimizeMember, please see: `Alternative View Protection`.
*
* ———— Code Sample Using optimizeMember's Query Filters ————
* ```
* <!php
* attach_optimizemember_query_filters();
* 	query_posts("posts_per_page=5");
* 	
* 	if (have_posts()):
* 		while (have_posts()):
* 			the_post();
* 		# Protected content will be excluded automatically.
* 		# ( based on the current User's Role/Capabilities )
* 		endwhile;
* 	endif;
* 	
* 	wp_reset_query();
* detach_optimizemember_query_filters();
* !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this.
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @return null
*
* @see optimizeMember\API_Functions\detach_optimizemember_query_filters()
*/
if(!function_exists("attach_optimizemember_query_filters"))
	{
		function attach_optimizemember_query_filters()
			{
				remove_action("pre_get_posts", "c_ws_plugin__optimizemember_security::security_gate_query", 100);
				add_action("pre_get_posts", "c_ws_plugin__optimizemember_querys::force_query_level_access", 100);
			}
	}
/**
* Allows plugin/theme developers to pre-filter WP Queries easily, so that protected content
* *( i.e content NOT available to the current User )*, is excluded automatically.
*
* This functionality is already built right into optimizeMember's UI configuration panels,
* but in cases where a plugin/theme developer needs more control, this may come in handy.
* In the UI configuration for optimizeMember, please see: `Alternative View Protection`.
*
* ———— Code Sample Using optimizeMember's Query Filters ————
* ```
* <!php
* attach_optimizemember_query_filters();
* 	query_posts("posts_per_page=5");
* 	
* 	if (have_posts()):
* 		while (have_posts()):
* 			the_post();
* 		# Protected content will be excluded automatically.
* 		# ( based on the current User's Role/Capabilities )
* 		endwhile;
* 	endif;
* 	
* 	wp_reset_query();
* detach_optimizemember_query_filters();
* !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this.
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @return null
*
* @see optimizeMember\API_Functions\attach_optimizemember_query_filters()
*/
if(!function_exists("detach_optimizemember_query_filters"))
	{
		function detach_optimizemember_query_filters()
			{
				remove_action("pre_get_posts", "c_ws_plugin__optimizemember_querys::force_query_level_access", 100);
				add_action("pre_get_posts", "c_ws_plugin__optimizemember_security::security_gate_query", 100);
			}
	}
/**
* Generates a File Download URL that provides access to a File protected by optimizeMember.
*
* By default, optimizeMember uses your Basic Download Restrictions. For more information on this,
* please check your Dashboard under: `optimizeMember -> Download Options -> Basic Download Restrictions`.
*
* ———— HTML/PHP Code Samples ————
* ```
* <a href="<!php echo optimizemember_file_download_url(array("file_download" => "file.zip")); !>">Download Now</a>
* <a href="<!php echo optimizemember_file_download_url(array("file_download" => "file.pdf", "file_inline" => true)); !>">View PDF</a>
* ```
* ———— Shortcode Equivalents ————
* ```
* <a href="[opmFile download="file.zip" /]">Download Now</a>
* <a href="[opmFile download="file.pdf" inline="true" /]">View PDF</a>
* ```
*
* ———— Advanced Download Restrictions  ————
*
* Or, you can also force optimizeMember to allow File Downloads, by requesting a File Download Key ( i.e. `file_download_key => true` ).
* When a File Download Key is requested through this parameter ( i.e. `file_download_key => true` ); it tells optimizeMember to allow the download of this particular file,
* regardless of Membership Level; and WITHOUT checking any Basic Restrictions, that you may, or may not have configured.
*
* ———— HTML/PHP Code Samples Using A Download Key ————
* ```
* <a href="<!php echo optimizemember_file_download_url(array("file_download" => "file.zip", file_download_key => true)); !>">Download Now</a>
* <a href="<!php echo optimizemember_file_download_url(array("file_download" => "file.pdf", file_download_key => true, "file_inline" => true)); !>">View PDF</a>
* ```
* ———— Shortcode Equivalents Using A Download Key ————
* ```
* <a href="[opmFile download="file.zip" download_key="true" /]">Download Now</a>
* <a href="[opmFile download="file.zip" download_key="true" inline="true" /]">View PDF</a>
* ```
*
* ———— Extra Detail On Function Parameters ————
*
* **Parameter $config ( array Required ).** This should be an array with one or more of the following elements.
*
* 	o ``"file_download" => "file.zip"`` Location of the file, relative to the `/optimizemember-files/` directory; or, relative to the root of your Amazon S3 Bucket, when applicable.
* 	o ``"file_download_key" => false`` Defaults to `false`. If `true`, optimizeMember will return a URL with an optimizeMember-generated File Download Key. You don't need to generate the File Download Key yourself, optimizeMember does it for you. If you set this to `ip-forever`, the File Download Key that optimizeMember generates will last forever, for a specific IP Address; otherwise, by default, all File Download Keys expire after 24 hours automatically. If you set this to `universal`, optimizeMember will generate a File Download Key that is good for anyone/everyone forever, with NO restrictions on who/where/when a file is accessed *( e.g. be careful with this one )*.
* 	o ``"file_stream" => false`` Defaults to `false`. If `true`, optimizeMember will return a URL containing a parameter/directive, which forces the File Download to take place over the RTMP protocol. This ONLY works when/if optimizeMember is configured to run with both Amazon S3/CloudFront. Please note however, it's better to use the example code provided in the your Dashboard. See: `optimizeMember -> Download Options -> JW Player and the RTMP Protocol`. Also note, if ``$get_streamer_array`` is passed, optimizeMember will automatically force ``"file_stream" => true`` for you.
* 	o ``"file_inline" => null`` Defaults to `null`. If `true`, optimizeMember will serve the file inline, instead of as an actual File Download. If empty, optimizeMember will look at your Inline File Extensions configuration, and serve the file inline; if, and only if, its extension matches one found in your configuration. By default, optimizeMember serves all files as attachments *( i.e. downloads )*. Please check your Dashboard regarding Inline File Extensions. Also note, this Shortcode Attribute does NOTHING for files served via Amazon CloudFront. See the tech-notes listed in the Amazon CloudFront section of your Dashboard for further details and workarounds.
* 	o ``"file_storage" => null`` Defaults to `null`. Can be one of `local|s3|cf`. When specified, optimizeMember will serve the file from a specific source location. For example, if you've configured Amazon S3 and/or CloudFront; but, there are a few files that you want to upload locally to the `/optimizemember-files/` directory; you can force optimizeMember to serve a file from local storage by setting ``"file_storage" => "local"`` explicitly.
* 	o ``"file_remote" => false`` Defaults to `false`. If `true`, optimizeMember will authenticate access to the File Download via Remote Header Authorization, instead of through your web site. This is similar to `.htaccess` protection routines of yester-year. Please check the Remote Authorization and Podcasting section in your Dashboard for further details about how this works.
* 	o ``"file_ssl" => null`` Defaults to `null`. If `true`, optimizeMember will generate a File Download URL with an SSL protocol *( i.e. the URL will start with `https://` or `rtmpe://` )*. If `null`, optimizeMember will only generate a File Download URL with an SSL protocol, when/if the Post/Page/URL, is also being viewed over SSL. Otherwise, optimizeMember will use a non-SSL protocol by default.
* 	o ``"file_rewrite" => false`` Defaults to `false`. If `true`, optimizeMember will generate a File Download URL that takes full advantage of optimizeMember's Advanced Mod Rewrite functionality. If you're running an Apache web server, or another server that supports `mod_rewrite`, we highly recommend turning this on. optimizeMember's `mod_rewrite` URLs do NOT contain query string parameters, making them more portable/compatible with other software applications and/or plugins for WordPress.
* 	o ``"file_rewrite_base" => null`` Defaults to `null`. If set to a URL, starting with `http` or another valid protocol, optimizeMember will generate a File Download URL that takes full advantage of optimizeMember's Advanced Mod Rewrite functionality, and it will use the rewrite base URL as a prefix. This could be useful on some WordPress installations that use advanced directory structures. It could also be useful for site owners using virtual directories that point to `/optimizemember-files/`. Note, if `rewrite_base` is set, optimizeMember will automatically force ``"rewrite" => true`` for you.
* 	o ``"skip_confirmation" => false`` Defaults to `false`. If `true`, optimizeMember will generate a File Download URL which contains a directive, telling optimizeMember NOT to introduce any JavaScript confirmation prompts on your site, for this File Download URL. Please note, optimizeMember will automatically detect links, anywhere in your content, and/or anywhere in your theme files, that contain `optimizemember_file_download` or `optimizemember-files`. Whenever a logged-in Member clicks a link that contains `optimizemember_file_download` or `optimizemember-files`, the system will politely ask the User to confirm the download using a very intuitive JavaScript confirmation prompt, which contains specific details about your configured download limitations. This way your Members will be aware of how many files they've downloaded in the current period; and they'll be able to make a conscious decision about whether to proceed with a specific download or not.
* 	o ``"url_to_storage_source" => false`` Defaults to `false`. If `true`, optimizeMember will generate a File Download URL which points directly to the storage source. This is only functional with Amazon S3 and/or CloudFront integrations. If you create a URL that points directly to the storage source *( i.e. points directly to Amazon S3 or CloudFront )*, optimizeMember will NOT be able to further authenticate the current User/Member; and, optimizeMember will NOT be able to count the File Download against the current User's account record, because the URL being generated does not pass back through optimizeMember at all, it points directly to the storage source. For this reason, if you set ``"url_to_storage_source" => true``, you should also set ``"check_user" => true`` and ``"count_against_user" => true``, telling optimizeMember to authenticate the current User, and if authenticated, count this File Download URL against the current User's account record in real-time *( i.e. as the URL is being generated )*, while it still has a chance to do so. This is useful when you stream files over the RTMP protocol; where an `http://` URL is not feasible. It also helps in situations where a 3rd-party software application will not work as intended, with optimizeMember's internal redirection to Amazon S3/CloudFront files. Important, when ``"check_user" => true`` and/or ``"count_against_user" => true``, this API Function will return `false` in situations where the current User/Member does NOT have access to the file.
* 	o ``"count_against_user" => false`` Defaults to `false`. If `true`, it will automatically force ``"check_user" => true`` as well. In other words, optimizeMember will authenticate the current User, and if authenticated, count this File Download URL against the current User's account record in real-time *( i.e. as the URL is being generated )*. This is off by default. By default, optimizeMember will simply generate a File Download URL, and upon a User/Member clicking the URL, optimizeMember will authenticate the User/Member at that time, count the File Download against their account record, and serve the File Download. In other words, under normal circumstances, there is no reason to set ``"check_user" => true`` and/or ``"count_against_user" => true`` when generating the URL itself. However, this is a useful config option when ``"url_to_storage_source" => true``. Please note, when ``"check_user" => true`` and/or ``"count_against_user" => true``, this API Function will return `false` in situations where the current User/Member does NOT have access to the file.
* 	o ``"check_user => false`` Defaults to `false`. If `true`, optimizeMember will authenticate the current User before allowing the File Download URL to be generated. This is off by default. By default, optimizeMember will simply generate a File Download URL, and upon a User/Member clicking the URL, optimizeMember will authenticate the User/Member at that time, and serve the File Download to the User/Member. In other words, under normal circumstances, there is no reason to set ``"check_user" => true`` and/or ``"count_against_user" => true`` when generating the URL itself. However, this IS a useful config option when ``"url_to_storage_source" => true``. Please note, when ``"check_user" => true`` and/or ``"count_against_user" => true``, this API Function will return `false` in situations where the current User/Member does NOT have access to the file.
*
* **Parameter $get_streamer_array ( bool Optional ).** Defaults to `false`. If `true`, this API Function will return an array with the following elements: `streamer`, `file`, `url`. For further details, please review this section in your Dashboard: `optimizeMember -> Download Options -> JW Player & RTMP Protocol Examples`. Note, if this is true, optimizeMember will automatically force ``"url_to_storage_source" => true`` and ``"file_stream" => true``. For that reason, you should carefully review the details and warning above regarding `url_to_storage_source`. If you set ``$get_streamer_array``, you should also set ``"check_user" => true`` and ``"count_against_user" => true``.
*
* @package optimizeMember\API_Functions
* @since 110926
*
* @param array $config Required. This is an array of configuration options associated with permissions being checked against the current User/Member; and also the actual URL generated by this routine.
* 	Possible ``$config`` array elements: `file_download` *(required)*, `file_download_key`, `file_stream`, `file_inline`, `file_storage`, `file_remote`, `file_ssl`, `file_rewrite`, `file_rewrite_base`, `skip_confirmation`, `url_to_storage_source`, `count_against_user`, `check_user`.
* @param bool $get_streamer_array Optional. Defaults to `false`. If `true`, this API Function will return an array with the following elements: `streamer`, `file`, `url`. For further details, please review this section in your Dashboard: `optimizeMember -> Download Options -> JW Player & RTMP Protocol Examples`. Note, if this is true, optimizeMember will automatically force ``"url_to_storage_source" => true`` and ``"file_stream" => true``. For that reason, you should carefully review the details and warning above regarding `url_to_storage_source`. If you set ``$get_streamer_array``, you should also set ``"check_user" => true`` and ``"count_against_user" => true``.
* @return str A File Download URL string on success; or an array on success, with elements `streamer`, `file`, `url` when/if ``$get_streamer_array`` is true; else false on any type of failure.
*
* @see optimizeMember\API_Functions\optimizemember_file_download_key()
*/
if(!function_exists("optimizemember_file_download_url"))
	{
		function optimizemember_file_download_url($config = FALSE, $get_streamer_array = FALSE)
			{
				return c_ws_plugin__optimizemember_files::create_file_download_url($config, $get_streamer_array);
			}
	}
/**
* Generates a File Download Key that provides access to a File protected by optimizeMember.
*
* By default, optimizeMember uses your Basic Download Restrictions. For more information on this,
* please check your Dashboard under: `optimizeMember -> Download Options -> Basic Download Restrictions`.
*
* ———— Advanced Download Restrictions  ————
*
* Or, you can also force optimizeMember to allow File Downloads, using an extra query string parameter `optimizemember_file_download_key`.
* A File Download Key is passed through this parameter; it tells optimizeMember to allow the download of this particular file,
* regardless of Membership Level; and WITHOUT checking any Basic Restrictions, that you may, or may not have configured.
*
* ———— Code Sample Using A Download Key ————
* ```
* <a href="/?optimizemember_file_download=file.zip&optimizemember_file_download_key=<!php echo optimizemember_file_download_key("file.zip"); !>">Download Now</a>
* ```
* ———— Shortcode Equivalent ————
* ```
* [opmKey file_download="file.zip" directive="" /]
* ```
*
* This API Funtion produces a time-sensitive File Download Key that is unique to each and every visitor.
* Each Key it produces *( at the time it is produced )*, will be valid for the current day, and only for a specific IP address and User-Agent string;
* as detected by optimizeMember. This makes it possible for you to create links on your site, which provide access to protected File Downloads;
* without having to worry about one visitor sharing their link with another.
*
* When `/?optimizemember_file_download_key` = `a valid Key` generated by this function, it works independently from Member Level Access.
* That is, a visitor does NOT have to be logged in to receive access; they just need a valid Key.
* Using this advanced technique, you could extend optimizeMember's file protection routines,
* or even combine them with Specific Post/Page Access, and more.
* The possibilities are limitless really.
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $file Location of the protected File, relative to the `/optimizemember-files/` directory.
* @param str|bool $directive Optional. Defaults to false. If you set this to any non-zero value ( i.e. the string `universal` ),
* 	the resulting Key will be universal *( i.e. valid for any User, at any time, from any browser )*. That is to say; universal, for this particular File.
* 	It is also possible to pass in the ``$directive`` string `ip-forever`, making the Key last forever, but only for a specific IP address.
* @return str The File Download Key. Which is an MD5 hash *( always 32 characters )*, URL-safe.
*
* @see optimizeMember\API_Functions\optimizemember_file_download_url()
*
* @todo Allow custom expiration times.
*/
if(!function_exists("optimizemember_file_download_key"))
	{
		function optimizemember_file_download_key($file = FALSE, $directive = FALSE)
			{
				return c_ws_plugin__optimizemember_files::file_download_key($file, $directive);
			}
	}
/**
* Retrieves an array of details, related to a User's File Downloads.
*
* ———— PHP Code Samples ————
* ```
* <!php
* $user_downloads = optimizemember_user_downloads();
* $specific_user_downloads = optimizemember_user_downloads(($user_id = 123));
* !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this yet.
* ```
*
* @package optimizeMember\API_Functions
* @since 111026
*
* @param str|int $user_id Optional. Defaults to the currently logged-in User's ID.
* @param str $not_counting_this_particular_file Optional. If you want to exclude a particular file, relative to the `/optimizemember-files/` directory, or relative to the root of your Amazon S3 Bucket *( when applicable )*.
* @return array An array with the following elements... File Downloads allowed for this User: (int)`allowed`, Download Period for this User in days: (int)`allowed_days`, Files downloaded by this User in the current Period: (int)`currently`, log of all Files downloaded in the current Period, with file names/dates: (array)`log`, archive of all Files downloaded in prior Periods, with file names/dates: (array)`archive`.
*
* @note Calculations returned by this function do NOT include File Downloads that were accessed with an Advanced File Download Key.
*
* @see optimizeMember\API_Functions\optimizemember_total_downloads_of()
* @see optimizeMember\API_Functions\optimizemember_total_unique_downloads_of()
*
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
*
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_CURRENTLY
*
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
*
* @see optimizeMember\API_Constants\optimizemember_LEVELn_FILE_DOWNLOADS_ALLOWED
* @see optimizeMember\API_Constants\optimizemember_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
*
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_INLINE_EXTENSIONS
*
* @todo Make it possible for optimizeMember to keep a count of files downloaded with an Advanced Download Key.
* @todo Create a Shortcode equivalent.
*/
if(!function_exists("optimizemember_user_downloads"))
	{
		function optimizemember_user_downloads($user_id = FALSE, $not_counting_this_particular_file = FALSE)
			{
				$user = ($user_id && is_object($user = new WP_User((int)$user_id)) && !empty($user->ID)) ? $user : false;
				return c_ws_plugin__optimizemember_files::user_downloads($user, $not_counting_this_particular_file);
			}
	}
/**
* Total downloads of a particular file; possibly by a particular User.
*
* ———— PHP Code Samples ————
* ```
* File: `example-file.zip`, has been downloaded a total of <!php echo optimizemember_total_downloads_of("example-file.zip"); !> times; collectively, among all Users/Members, for all time *( includes all duplicate downloads of the same file by the same User/Member )*.
* File: `example-file.zip`, has been downloaded a total of <!php echo optimizemember_total_downloads_of("example-file.zip", false, false); !> times; collectively, among all Users/Members, in this Period only *( includes all duplicate downloads of the same file by the same User/Member )*.
* File: `example-file.zip`, has been downloaded by User ID# 123, a total of <!php echo optimizemember_total_downloads_of("example-file.zip", 123); !> times; for all time, since they first became a User/Member of the site *( includes all duplicate downloads of the same file by this User/Member )*.
* File: `example-file.zip`, has been downloaded by User ID# 123, a total of <!php echo optimizemember_total_downloads_of("example-file.zip", 123, false); !> times; in this Period only *( includes all duplicate downloads of the same file by this User/Member )*.
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this yet.
* ```
*
* @package optimizeMember\API_Functions
* @since 111026
*
* @param str $file Required. Location of the file, relative to the `/optimizemember-files/` directory, or relative to the root of your Amazon S3 Bucket *( when applicable )*.
* @param str|int $user_id Optional. If specified, optimizeMember will return total downloads by a particular User/Member, instead of collectively *( i.e among all Users/Members )*.
* @param bool $check_archives_too Optional. Defaults to true. When true, optimizeMember checks its File Download Archive too, instead of ONLY looking at Files downloaded in the current Period. Period is based on your Basic Download Restrictions setting of allowed days across various Levels of Membership, for each respective User/Member. Or, if ``$user_id`` is specified, based solely on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's current Membership Level.
* @return int The total for this particular ``$file``, based on configuration of function arguments.
*
* @note Calculations returned by this function do NOT include File Downloads that were accessed with an Advanced File Download Key.
*
* @see optimizeMember\API_Functions\optimizemember_user_downloads()
* @see optimizeMember\API_Functions\optimizemember_total_unique_downloads_of()
*
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
*
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_CURRENTLY
*
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
*
* @see optimizeMember\API_Constants\optimizemember_LEVELn_FILE_DOWNLOADS_ALLOWED
* @see optimizeMember\API_Constants\optimizemember_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
*
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_INLINE_EXTENSIONS
*
* @todo Make it possible for optimizeMember to keep a count of files downloaded with an Advanced Download Key.
* @todo Create a Shortcode equivalent.
*/
if(!function_exists("optimizemember_total_downloads_of"))
	{
		function optimizemember_total_downloads_of($file = FALSE, $user_id = FALSE, $check_archives_too = TRUE)
			{
				return c_ws_plugin__optimizemember_files::total_downloads_of($file, $user_id, $check_archives_too);
			}
	}
/**
* Total unique downloads of a particular file; possibly by a particular User.
*
* ———— PHP Code Samples ————
* ```
* File: `example-file.zip`, has been downloaded a total of <!php echo optimizemember_total_unique_downloads_of("example-file.zip"); !> times; collectively, among all Users/Members, for all time *( does NOT include duplicate downloads of the same file, in a single Period, by the same User/Member )*.
* File: `example-file.zip`, has been downloaded a total of <!php echo optimizemember_total_unique_downloads_of("example-file.zip", false, false); !> times; collectively, among all Users/Members, in this Period only *( does NOT include duplicate downloads of the same file, in a single Period, by the same User/Member )*.
* File: `example-file.zip`, has been downloaded by User ID# 123, a total of <!php echo optimizemember_total_unique_downloads_of("example-file.zip", 123); !> times; for all time, since they first became a User/Member of the site *( does NOT include duplicate downloads of the same file, in a single Period, by this User/Member )*.
* File: `example-file.zip`, has been downloaded by User ID# 123, a total of <!php echo optimizemember_total_unique_downloads_of("example-file.zip", 123, false); !> times; in this Period only *( does NOT include duplicate downloads of the same file, in a single Period, by this User/Member )*.
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this yet.
* ```
*
* @package optimizeMember\API_Functions
* @since 111026
*
* @param str $file Required. Location of the file, relative to the `/optimizemember-files/` directory, or relative to the root of your Amazon S3 Bucket *( when applicable )*.
* @param str|int $user_id Optional. If specified, optimizeMember will return total downloads by a particular User/Member, instead of collectively *( i.e among all Users/Members )*.
* @param bool $check_archives_too Optional. Defaults to true. When true, optimizeMember checks its File Download Archive too, instead of ONLY looking at Files downloaded in the current Period. Period is based on your Basic Download Restrictions setting of allowed days across various Levels of Membership, for each respective User/Member. Or, if ``$user_id`` is specified, based solely on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's current Membership Level.
* @return int The total for this particular ``$file``, based on configuration of function arguments.
*
* @note Calculations returned by this function do NOT include File Downloads that were accessed with an Advanced File Download Key.
*
* @see optimizeMember\API_Functions\optimizemember_user_downloads()
* @see optimizeMember\API_Functions\optimizemember_total_downloads_of()
*
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
*
* @see optimizeMember\API_Constants\optimizemember_CURRENT_USER_DOWNLOADS_CURRENTLY
*
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
*
* @see optimizeMember\API_Constants\optimizemember_LEVELn_FILE_DOWNLOADS_ALLOWED
* @see optimizeMember\API_Constants\optimizemember_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
*
* @see optimizeMember\API_Constants\optimizemember_FILE_DOWNLOAD_INLINE_EXTENSIONS
*
* @todo Make it possible for optimizeMember to keep a count of files downloaded with an Advanced Download Key.
* @todo Create a Shortcode equivalent.
*/
if(!function_exists("optimizemember_total_unique_downloads_of"))
	{
		function optimizemember_total_unique_downloads_of($file = FALSE, $user_id = FALSE, $check_archives_too = TRUE)
			{
				return c_ws_plugin__optimizemember_files::total_unique_downloads_of($file, $user_id, $check_archives_too);
			}
	}
/**
* Obtains the Registration Time for the current User, and/or for a particular User.
*
* The Registration Time, is the time at which the Username was created for the account, that's it.
* There's nothing special about this. This simply returns a {@link http://en.wikipedia.org/wiki/Unix_time Unix Timestamp}.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* if(optimizemember_registration_time() <= strtotime("-30 days"))
* 	echo 'The current User has existed for at least 30 days.';
* 	
* else if(optimizemember_registration_time(123) <= strtotime("-30 days"))
* 	echo 'User with ID #123 has existed for at least 30 days.';
* !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param int $user_id Optional. Defaults to the current User's ID.
* @return int A {@link http://en.wikipedia.org/wiki/Unix_time Unix Timestamp}.
* 	The Registration Time, is the time at which the Username was created for the account, that's it.
*
* @see optimizeMember\API_Functions\get_user_field()
*/
if(!function_exists("optimizemember_registration_time"))
	{
		function optimizemember_registration_time($user_id = FALSE)
			{
				return c_ws_plugin__optimizemember_registration_times::registration_time($user_id);
			}
	}
/**
* Retrieves a Paid Registration Time for the current User, and/or for a particular User.
*
* **NOTE** A Paid Registration Time, is NOT necessarily related specifically to a Payment.
* optimizeMember records a Paid Registration Time, anytime a User acquires paid Membership Level Access.
*
* In other words, if you create a new User inside your Dashboard at a Membership Level greater than Level #0,
* optimizeMember will record a Paid Registration Time immediately, because Membership Levels > 0, are reserved for paying Members.
* optimizeMember monitors changes to all User accounts, and records the first Paid Registration Time for each Member, at each paid Membership Level.
* So, optimizeMember stores the first Time a Member reaches each Level of paid access; and optimizeMember does NOT care if they *actually* paid, or not.
*
* ———— Code Sample Using Function Parameters ————
* ```
* <!php
* $time = optimizemember_registration_time (); # first registration time ( free or otherwise ).
* $time = optimizemember_paid_registration_time (); # first "paid" registration and/or upgrade time.
* $time = optimizemember_paid_registration_time ("level1"); # first "paid" registration or upgrade time at Level#1.
* $time = optimizemember_paid_registration_time ("level2"); # first "paid" registration or upgrade time at Level#2.
* $time = optimizemember_paid_registration_time ("level3"); # first "paid" registration or upgrade time at Level#3.
* $time = optimizemember_paid_registration_time ("level4"); # first "paid" registration or upgrade time at Level#4.
* !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $level Optional. Defaults to the first/initial Paid Registration Time, regardless of Level#.
* @param int $user_id Optional. Defaults to the current User's ID.
* @return int A {@link http://en.wikipedia.org/wiki/Unix_time Unix Timestamp}.
*
* @see optimizeMember\API_Functions\get_user_field()
*/
if(!function_exists("optimizemember_paid_registration_time"))
	{
		function optimizemember_paid_registration_time($level = FALSE, $user_id = FALSE)
			{
				return c_ws_plugin__optimizemember_registration_times::paid_registration_time($level, $user_id);
			}
	}
/**
* A powerful function that can retrieve almost anything
* you need to know about the current User, and/or a particular User.
*
* Scans all properties of the {@link http://codex.wordpress.org/Function_Reference/wp_get_current_user WP_User object}.
* It defaults to the current User, but can also be used to obtain information about a particular User, by passing in a specific User ID.
*
* It can be used to retrieve basic information like `first_name`, `last_name`, `user_email`, `user_login`.
* It can also be used to retrieve User Meta/Options, Role/Capabilities, and even supports
* Custom Registration/Profile Fields configured with optimizeMember and many other plugins.
*
* ———— Here Are A Few Examples ————
* ```
* <!php
* $user_login = get_user_field ("user_login"); # Username for the current User.
* $user_email = get_user_field ("user_email"); # Email Address for the current User.
* $first_name = get_user_field ("first_name"); # First Name for the current User.
* $last_name = get_user_field ("last_name"); # Last Name for the current User.
* $full_name = get_user_field ("full_name"); # First and Last Name for the current User.
* $display_name = get_user_field ("display_name"); # Display Name for the current User.
* !>
* ```
* ———— Shortcode Equivalents ————
* ```
* [opmGet user_field="user_login" /] # Username for the current User.
* [opmGet user_field="user_email" /] # Email Address for the current User.
* [opmGet user_field="first_name" /] # First Name for the current User.
* [opmGet user_field="last_name" /] # Last Name for the current User.
* [opmGet user_field="full_name" /] # First and Last Name for the current User.
* [opmGet user_field="display_name" /] # Display Name for the current User.
* ```
* ———— More Examples With optimizeMember Fields ————
* ```
* <!php
* $optimizemember_custom = get_user_field ("optimizemember_custom"); # Custom String value for the current User.
* $optimizemember_subscr_id = get_user_field ("optimizemember_subscr_id"); # Paid Subscr. ID for the current User.
* $optimizemember_subscr_or_wp_id = get_user_field ("optimizemember_subscr_or_wp_id"); # Paid Subscr. ID, else WordPress User ID.
* $optimizemember_subscr_gateway = get_user_field ("optimizemember_subscr_gateway"); # Paid Subscr. Gateway Code for the current User.
* $optimizemember_registration_ip = get_user_field ("optimizemember_registration_ip"); # IP the current User had during registration.
* $optimizemember_custom_fields = get_user_field ("optimizemember_custom_fields"); # Associative array of all Custom Registration/Profile Fields.
* $optimizemember_file_download_access_log = get_user_field ("optimizemember_file_download_access_log"); # Associative array of all File Downloads by the current User, in the current Period *( Period is based on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's current Membership Level )*.
* $optimizemember_file_download_access_arc = get_user_field ("optimizemember_file_download_access_arc"); # Associative array of all File Downloads by the current User, in previous Periods *( Periods are based on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's Membership Levels in the past )*.
* $optimizemember_auto_eot_time = get_user_field ("optimizemember_auto_eot_time"); # Auto EOT-Time for the current User ( when applicable ).
* $optimizemember_last_payment_time = get_user_field ("optimizemember_last_payment_time"); # Timestamp. Last time an actual payment was received by optimizeMember.
* $optimizemember_paid_registration_times = get_user_field ("optimizemember_paid_registration_times"); # Timestamps. Associative array of all Paid Registration Times.
* $optimizemember_access_role = get_user_field ("optimizemember_access_role"); # A WordPress Role ID ( i.e. optimizemember_level[0-9]+, administrator, editor, author, contributor, subscriber ).
* $optimizemember_access_level = get_user_field ("optimizemember_access_level"); # An optimizeMember Membership Access Level number.
* $optimizemember_access_label = get_user_field ("optimizemember_access_label"); # An optimizeMember Membership Access Label ( i.e. Bronze, Gold, Silver, Platinum, or whatever is configured ).
* $optimizemember_access_ccaps = get_user_field ("optimizemember_access_ccaps"); # An array of Custom Capabilities the current User has ( i.e. music,videos ).
* $optimizemember_login_counter = get_user_field ("optimizemember_login_counter"); # Number of times the User has logged into your site.
* !>
* ```
* ———— Practical Shortcode Equivalents ————
* ```
* [opmGet user_field="optimizemember_custom" /] # Custom String value for the current User.
* [opmGet user_field="optimizemember_subscr_id" /] # Paid Subscr. ID for the current User.
* [opmGet user_field="optimizemember_subscr_or_wp_id" /] # Paid Subscr. ID, else WordPress User ID.
* [opmGet user_field="optimizemember_subscr_gateway" /] # Paid Subscr. Gateway Code for the current User.
* [opmGet user_field="optimizemember_registration_ip" /] # IP Address the current User had during registration.
* [opmGet user_field="optimizemember_access_role" /] # A WordPress Role ID ( i.e. optimizemember_level[0-9]+, administrator, editor, author, contributor, subscriber ).
* [opmGet user_field="optimizemember_access_level" /] # An optimizeMember Membership Access Level number.
* [opmGet user_field="optimizemember_access_label" /] # An optimizeMember Membership Access Label ( i.e. Bronze, Gold, Silver, Platinum, or whatever is configured ).
* [opmGet user_field="optimizemember_login_counter" /] # Number of times the User has logged into your site.
* ```
* ———— Pulling Data From Your Own Custom Fields ————
* ```
* <!php
* $my_field_data = get_user_field ("my_field_id"); # The Unique Field ID you configured with optimizeMember.
* !>
* ```
* ———— Shortcode Equivalent ————
* ```
* [opmGet user_field="my_field_id" /] # The Unique Field ID you configured with optimizeMember.
* ```
* ———— Pulling Data For A Particular User ID ————
* ```
* <!php
* $user_login = get_user_field ("user_login", 123); # Username for the User with ID #123.
* $user_email = get_user_field ("user_email", 123); # Email Address for the User with ID #123.
* $first_name = get_user_field ("first_name", 123); # First Name for the User with ID #123.
* $last_name = get_user_field ("last_name", 123); # Last Name for the User with ID #123.
* $full_name = get_user_field ("full_name", 123); # First and Last Name for the User with ID #123.
* $display_name = get_user_field ("display_name", 123); # Display Name for the User with ID #123.
* !>
* ```
* ———— Shortcode Equivalents ————
* ```
* [opmGet user_field="user_login" user_id="123" /] # Username for the User with ID #123.
* [opmGet user_field="user_email" user_id="123" /] # Email Address for the User with ID #123.
* [opmGet user_field="first_name" user_id="123" /] # First Name for the User with ID #123.
* [opmGet user_field="last_name" user_id="123" /] # Last Name for the User with ID #123.
* [opmGet user_field="full_name" user_id="123" /] # First and Last Name for the User with ID #123.
* [opmGet user_field="display_name" user_id="123" /] # Display Name for the User with ID #123.
* ```
* ———— Finding A User ID, Based On Username ————
* ```
* <!php
* $user = new WP_User("johndoe22");
* $user_id = $user->ID;
* !>
* ```
* ———— Finding A Username, Based On User ID ————
* ```
* <!php
* $user = new WP_User(123);
* $user_login = $user->user_login;
* # Or you could just use this alternate method.
* $user_login = get_user_field ("user_login", 123);
* !>
* ```
*
* ———— Alternative Using ``get_user_option()`` Native To WordPress ————
* Most of the optimizeMember fields are stored in the `usermeta` table ( a WordPress standard ),
* so they could also be retrieved with {@link http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()} if you prefer,
* which is already native to WordPress. That being said, {@link optimizeMember\API_Functions\get_user_field()} is provided by optimizeMember as a way to retrieve *almost anything*.
* ```
* <!php
* $optimizemember_custom = get_user_option ("optimizemember_custom"); # Custom String value for the current User.
* $optimizemember_subscr_id = get_user_option ("optimizemember_subscr_id"); # Paid Subscr. ID for the current User.
* $optimizemember_subscr_gateway = get_user_option ("optimizemember_subscr_gateway"); # Paid Subscr. Gateway Code for the current User.
* $optimizemember_registration_ip = get_user_option ("optimizemember_registration_ip"); # IP the current User had during registration.
* $optimizemember_custom_fields = get_user_option ("optimizemember_custom_fields"); # Associative array of all Custom Registration/Profile Fields.
* $optimizemember_file_download_access_log = get_user_option ("optimizemember_file_download_access_log"); # Associative array of all File Downloads by the current User, in the current Period *( Period is based on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's current Membership Level )*.
* $optimizemember_file_download_access_arc = get_user_option ("optimizemember_file_download_access_arc"); # Associative array of all File Downloads by the current User, in previous Periods *( Periods are based on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's Membership Levels in the past )*.
* $optimizemember_auto_eot_time = get_user_option ("optimizemember_auto_eot_time"); # Auto EOT-Time for the current User ( when applicable ).
* $optimizemember_last_payment_time = get_user_option ("optimizemember_last_payment_time"); # Timestamp. Last time an actual payment was received by optimizeMember.
* $optimizemember_paid_registration_times = get_user_option ("optimizemember_paid_registration_times"); # Timestamps. Associative array of all Paid Registration Times.
* $optimizemember_login_counter = get_user_option ("optimizemember_login_counter"); # Number of times the User has logged into your site.
* !>
* ```
* ———— Practical Shortcode Equivalents ————
* ```
* [opmGet user_option="optimizemember_custom" /] # Custom String value for the current User.
* [opmGet user_option="optimizemember_subscr_id" /] # Paid Subscr. ID for the current User.
* [opmGet user_option="optimizemember_subscr_gateway" /] # Paid Subscr. Gateway Code for the current User.
* [opmGet user_option="optimizemember_registration_ip" /] # IP the current User had during registration.
* [opmGet user_option="optimizemember_login_counter" /] # Number of times the User has logged in.
* ```
*
* @package optimizeMember\API_Functions
* @since 3.5
*
* @param str $field_id Required. A unique Custom Registration/Profile Field ID, that you configured with optimizeMember.
* 	Or, this could be set to any property that exists on the WP_User object for a particular User;
* 	( i.e. `id`, `ID`, `user_login`, `user_email`, `first_name`, `last_name`, `display_name`, `ip`, `IP`,
* 	`optimizemember_registration_ip`, `optimizemember_custom`, `optimizemember_subscr_id`, `optimizemember_subscr_or_wp_id`,
* 	`optimizemember_subscr_gateway`, `optimizemember_custom_fields`, `optimizemember_file_download_access_[log|arc]`,
* 	`optimizemember_auto_eot_time`, `optimizemember_last_payment_time`, `optimizemember_paid_registration_times`,
* 	`optimizemember_access_role`, `optimizemember_access_level`, `optimizemember_access_label`,
* 	`optimizemember_access_ccaps`, `optimizemember_login_counter`, etc, etc. ).
* @param int $user_id Optional. Defaults to the current User's ID.
* @return mixed The value of the requested field, or false if the field does not exist.
*
* @see optimizeMember\API_Functions\get_optimizemember_custom_fields()
* @see optimizeMember\API_Functions\optimizemember_registration_time()
* @see optimizeMember\API_Functions\optimizemember_paid_registration_time()
*
* @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
* @see http://codex.wordpress.org/Function_Reference/update_user_option update_user_option()
* @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
*/
if(!function_exists("get_user_field"))
	{
		function get_user_field($field_id = FALSE, $user_id = FALSE)
			{
				return c_ws_plugin__optimizemember_utils_users::get_user_field($field_id, $user_id);
			}
	}
/**
* Custom Registration/Profile Field configuration.
*
* Provides information about the configuration of each Custom Registration/Profile Field.
* Returns an associative array with all Custom Field configurations *( and User values too, if ``$user_id`` is passed in )*.
*
* ———— PHP Code Sample ————
* ```
* <!php
* $fields = get_optimizemember_custom_fields();
* print_r($fields["my_field_id"]["config"]); # The Unique Field ID you configured with optimizeMember.
* !>
* ```
* ———— PHP Code Sample ( Specific User ) ————
* ```
* <!php
* $fields = get_optimizemember_custom_fields(123);
* echo $fields["my_field_id"]["user_value"]; # The Unique Field ID you configured with optimizeMember.
* print_r($fields["my_field_id"]["config"]); # The Unique Field ID you configured with optimizeMember.
* !>
* ```
* ———— Shortcode Alternative ( Specific User ) ————
* ```
* [opmGet user_field="my_field_id" /] # The Unique Field ID you configured with optimizeMember.
* ```
*
* @package optimizeMember\API_Functions
* @since 110912
*
* @param int|str $user_id Optional. If supplied, the `user_value` for each Custom Field will be included too.
* @return array An associative array with all Custom Field configurations *( and User values too, if ``$user_id`` is supplied )*.
*
* @see optimizeMember\API_Functions\get_user_field()
* @see optimizeMember\API_Functions\optimizemember_registration_time()
* @see optimizeMember\API_Functions\optimizemember_paid_registration_time()
*
* @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
* @see http://codex.wordpress.org/Function_Reference/update_user_option update_user_option()
* @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
*/
if(!function_exists("get_optimizemember_custom_fields"))
	{
		function get_optimizemember_custom_fields($user_id = FALSE)
			{
				$fields = ($user_id) ? get_user_option("optimizemember_custom_fields", $user_id) : false;
				/**//**/
				foreach(json_decode($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"], true) as $field)
					{
						if($user_id) /* Should we try to fill the User's value for this Custom Field? */
							$optimizemember_custom_fields[$field["id"]]["user_value"] = (isset($fields[$field["id"]])) ? $fields[$field["id"]] : false;
						$optimizemember_custom_fields[$field["id"]]["config"] = $field; /* Copy configuration into config element. */
					}
				/**/
				return (isset($optimizemember_custom_fields)) ? (array)$optimizemember_custom_fields : array();
			}
	}
/**
* Can be used to auto-fill the `invoice` for PayPal Button Codes, with a unique Code~IP combination.
*
* ———— PHP Code Sample ————
* ```
* <!php echo optimizemember_value_for_pp_inv(); !>
* ```
* ———— Shortcode & JavaScript Equivalents ————
* ```
* [opmGet constant="optimizemember_VALUE_FOR_PP_INV" /]
* 
* <script type="text/javascript">
* 	document.write(optimizemember_value_for_pp_inv_gen());
* </script>
* ```
*
* @package optimizeMember\API_Functions
* @since 110720
*
* @return str A unique Invoice.
*
* @see optimizeMember\API_Constants\optimizemember_VALUE_FOR_PP_INV
*
* @todo Create a true Shortcode equivalent function.
*/
if(!function_exists("optimizemember_value_for_pp_inv"))
	{
		function optimizemember_value_for_pp_inv()
			{
				return uniqid()."~".$_SERVER["REMOTE_ADDR"];
			}
	}
/**
* Shortens a long URL, based on optimizeMember configuration.
*
* ———— PHP Code Samples ————
* ```
* <!php echo optimizemember_shorten_url("http://www.example.com/a-long-url/"); !>
* <!php echo optimizemember_shorten_url("http://www.example.com/a-long-url/", "tiny_url"); !>
* <!php echo optimizemember_shorten_url("http://www.example.com/a-long-url/", "goo_gl"); !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 111004
*
* @param str $url A full/long URL to be shortened.
* @param str $api_sp Optional. A specific URL shortening API to use. Defaults to that which is configured in the optimizeMember Dashboard. Normally `tiny_url` by default.
* @param bool $try_backups Defaults to true. If a failure occurs with the first API, we'll try others until we have success.
* @return str|bool The shortened URL on success, else false on failure.
*
* @todo Create a Shortcode equivalent for this function.
*/
if(!function_exists("optimizemember_shorten_url"))
	{
		function optimizemember_shorten_url($url = FALSE, $api_sp = FALSE, $try_backups = TRUE)
			{
				return c_ws_plugin__optimizemember_utils_urls::shorten($url, $api_sp, $try_backups);
			}
	}
/**
* Two-way RIJNDAEL 256 encryption/decryption, with a URL-safe base64 wrapper.
*
* Falls back on XOR encryption/decryption when/if mcrypt is not available.
*
* ———— PHP Code Samples ————
* ```
* <!php $encrypted = optimizemember_encrypt("hello"); !>
* <!php $decrypted = optimizemember_decrypt($encrypted); !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 111106
*
* @param str $string A string of data to encrypt.
* @param str $key Optional. Key used for encryption. Defaults to the one configured for optimizeMember. Short of that, defaults to: ``wp_salt()``.
* @param bool $w_md5_cs Optional. Defaults to true. When true, an MD5 checksum is used in the encrypted string *( recommended )*.
* @return str Encrypted string.
*
* @see optimizeMember\API_Functions\optimizemember_decrypt()
* @see optimizeMember\API_Functions\optimizemember_xencrypt()
* @see optimizeMember\API_Functions\optimizemember_xdecrypt()
*
* @todo Create a Shortcode equivalent for this function.
*/
if(!function_exists("optimizemember_encrypt"))
	{
		function optimizemember_encrypt($string = FALSE, $key = FALSE, $w_md5_cs = TRUE)
			{
				return c_ws_plugin__optimizemember_utils_encryption::encrypt($string, $key, $w_md5_cs);
			}
	}
/**
* Two-way RIJNDAEL 256 encryption/decryption, with a URL-safe base64 wrapper.
*
* Falls back on XOR encryption/decryption when/if mcrypt is not available.
*
* ———— PHP Code Samples ————
* ```
* <!php $encrypted = optimizemember_encrypt("hello"); !>
* <!php $decrypted = optimizemember_decrypt($encrypted); !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 111106
*
* @param str $base64 A string of data to decrypt. Should still be base64 encoded.
* @param str $key Optional. Key used originally for encryption. Defaults to the one configured for optimizeMember. Short of that, defaults to: ``wp_salt()``.
* @return str Decrypted string.
*
* @see optimizeMember\API_Functions\optimizemember_encrypt()
* @see optimizeMember\API_Functions\optimizemember_xencrypt()
* @see optimizeMember\API_Functions\optimizemember_xdecrypt()
*
* @todo Create a Shortcode equivalent for this function.
*/
if(!function_exists("optimizemember_decrypt"))
	{
		function optimizemember_decrypt($base64 = FALSE, $key = FALSE)
			{
				return c_ws_plugin__optimizemember_utils_encryption::decrypt($base64, $key);
			}
	}
/**
* Two-way XOR encryption/decryption, with a URL-safe base64 wrapper.
*
* ———— PHP Code Samples ————
* ```
* <!php $encrypted = optimizemember_xencrypt("hello"); !>
* <!php $decrypted = optimizemember_xdecrypt($encrypted); !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 111106
*
* @param str $string A string of data to encrypt.
* @param str $key Optional. Key used for encryption. Defaults to the one configured for optimizeMember. Short of that, defaults to: ``wp_salt()``.
* @param bool $w_md5_cs Optional. Defaults to true. When true, an MD5 checksum is used in the encrypted string *( recommended )*.
* @return str Encrypted string.
*
* @see optimizeMember\API_Functions\optimizemember_xdecrypt()
* @see optimizeMember\API_Functions\optimizemember_encrypt()
* @see optimizeMember\API_Functions\optimizemember_decrypt()
*
* @todo Create a Shortcode equivalent for this function.
*/
if(!function_exists("optimizemember_xencrypt"))
	{
		function optimizemember_xencrypt($string = FALSE, $key = FALSE, $w_md5_cs = TRUE)
			{
				return c_ws_plugin__optimizemember_utils_encryption::xencrypt($string, $key, $w_md5_cs);
			}
	}
/**
* Two-way XOR encryption/decryption, with a URL-safe base64 wrapper.
*
* ———— PHP Code Samples ————
* ```
* <!php $encrypted = optimizemember_xencrypt("hello"); !>
* <!php $decrypted = optimizemember_xdecrypt($encrypted); !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 111106
*
* @param str $base64 A string of data to decrypt. Should still be base64 encoded.
* @param str $key Optional. Key used originally for encryption. Defaults to the one configured for optimizeMember. Short of that, defaults to: ``wp_salt()``.
* @return str Decrypted string.
*
* @see optimizeMember\API_Functions\optimizemember_xencrypt()
* @see optimizeMember\API_Functions\optimizemember_encrypt()
* @see optimizeMember\API_Functions\optimizemember_decrypt()
*
* @todo Create a Shortcode equivalent for this function.
*/
if(!function_exists("optimizemember_xdecrypt"))
	{
		function optimizemember_xdecrypt($base64 = FALSE, $key = FALSE)
			{
				return c_ws_plugin__optimizemember_utils_encryption::xdecrypt($base64, $key);
			}
	}
/**
* Gets login IPs for a particular username.
*
* ———— PHP Code Samples ————
* ```
* <!php print_r($ips = optimizemember_login_ips_for("johndoe22")); !>
* ```
* ———— Shortcode Equivalent ————
* ```
* There is NO Shortcode equivalent for this ( yet ).
* ```
*
* @package optimizeMember\API_Functions
* @since 120728
*
* @param str $username A username.
* @return array An associative array of all IPs associated with a particular username, over the last 30 days.
* 	Array keys are IP addresses; array values are UTC timestamps.
*
* @todo Create a Shortcode equivalent for this function.
*/
if(!function_exists("optimizemember_login_ips_for"))
	{
		function optimizemember_login_ips_for($username)
			{
				$ips = get_transient('s2m_ipr_'.md5('optimizemember_ip_restrictions_'.strtolower($username).'_entries'));
				return (is_array($ips)) ? $ips : array();
			}
	}
?>