<?php
/**
 * A class for the page providing the basic settings.
 * 
 * @author Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 2016, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The page Basic Settings.
 * 
 * @since 1.0.0
 */
class OPanda_PermissionsSettings extends OPanda_Settings  {
 
    public $id = 'permissions';

    public function __construct($page) {
            parent::__construct($page);

            global $wp_roles;

            $this->wp_roles = $wp_roles;

            if ( !isset( $wp_roles ) )
                    $this->wp_roles = new WP_Roles();

            $this->roles = $this->wp_roles->get_names();
    }
    
    /**
     * Shows the header html of the settings screen.
     * 
     * @since 1.0.0
     * @return void
     */
    public function header() {
        global $optinpanda;
        ?>
        <p>
            <?php _e('Configure roles and permissions for getting access to the plugin features.', 'bizpanda' )?>
        </p>
        <?php
    }
    
    /**
     * Returns options for the Basic Settings screen. 
     * 
     * @since 1.0.0
     * @return void
     */
    public function getOptions() {

        $options = array();
        
        $options[] = array(
            'type' => 'separator'
        );

	    foreach ($this->roles as $role_value => $role_name) {
		    if( $role_value == 'administrator' )
			    continue;

		    $options[] = array(
			    'type'      => 'checkbox',
			    'way'       => 'buttons',
			    'name'      => 'user_role_' . $role_value,
			    'title'     => sprintf(__( '%s Role', 'bizpanda' ), $role_name),
			    'default'   => false,
			    'hint'      => sprintf(__( 'Grants access for users with the %s role.', 'bizpanda' ), $role_name)
		    );

		    $options[] =array(
			        'type'      => 'div',
				    'class'     => 'opanda-user-role-options-group',
				    'id'        => 'opanda_user_role_' . $role_value . '_options_group',
				    'items'   => array(
                                        array(
                                            'type' => 'div',
                                            'cssClass' => 'permissions-set',
                                            'items' => array(
                                                array(
                                                        'type'      => 'checkbox',
                                                        'name'      => 'allow_user_role_' . $role_value . '_edit',
                                                        'title'     => __( 'Lockers', 'bizpanda' ),
                                                        'default'   => false,
                                                        'hint'      => sprintf(__( 'Allows to view and edit lockers.', 'bizpanda' ), $role_name)
                                                ),
                                                array(
                                                        'type'      => 'checkbox',
                                                        'name'      => 'allow_user_role_' . $role_value . '_leads',
                                                        'title'     => __( 'Leads', 'bizpanda' ),
                                                        'default'   => false,
                                                        'hint'      => sprintf(__( 'Grants access to the Leads page.', 'bizpanda' ), $role_name)
                                                ),
                                                array(
                                                        'type'      => 'checkbox',
                                                        'name'      => 'allow_user_role_' . $role_value . '_stats',
                                                        'title'     => __( 'Stats & Reports', 'bizpanda' ),
                                                        'default'   => true,
                                                        'hint'      => sprintf(__( 'Grants access to the Stats & Reports page.', 'bizpanda' ), $role_name)
                                                ),
                                                array(
                                                        'type'      => 'checkbox',
                                                        'name'      => 'allow_user_role_' . $role_value . '_setting',
                                                        'title'     => __( 'Settings', 'bizpanda' ),
                                                        'default'   => false,
                                                        'hint'      => sprintf(__( 'Grants access to the Global Settings page.', 'bizpanda' ), $role_name)
                                                ),
                                                array(
                                                        'type'      => 'checkbox',
                                                        'name'      => 'allow_user_role_' . $role_value . '_licensing',
                                                        'title'     => __( 'License Manager', 'bizpanda' ),
                                                        'default'   => false,
                                                        'hint'      => sprintf(__( 'Grants access to the License Manager page.', 'bizpanda' ), $role_name)
                                                )
                                            )
                                        )
				    )
			);

		    $options[] = array(
			    'type' => 'separator'
		    );
	    }

        return $options;
    }

	public function onSaving() {
		foreach ($this->roles as $role_value => $role_name) {
			if( $role_value == 'administrator' )
				continue;

			$this->editCapabilityOption($role_value, 'edit');
			$this->editCapabilityOption($role_value, 'leads');
			$this->editCapabilityOption($role_value, 'stats');
			$this->editCapabilityOption($role_value, 'setting');
			$this->editCapabilityOption($role_value, 'licensing');
		}
	}

	public function editCapabilityOption($role_name, $capabilityPrefix) {
		$role = $GLOBALS [ 'wp_roles' ]->role_objects[$role_name];

		if( isset($_POST['opanda_allow_user_role_' . $role_name . '_'. $capabilityPrefix]) && !empty($_POST['opanda_allow_user_role_' . $role_name . '_' . $capabilityPrefix]) ) {

			if( $capabilityPrefix != 'edit' )
				$this->wp_roles->add_cap( $role_name, 'manage_opanda_' . $capabilityPrefix );
			else {
				$this->wp_roles->add_cap( $role_name, 'read_opanda-item' );
				$this->wp_roles->add_cap( $role_name, 'read_private_opanda-items' );
				$this->wp_roles->add_cap( $role_name, 'delete_opanda-item' );
				$this->wp_roles->add_cap( $role_name, 'delete_opanda-items' );
				$this->wp_roles->add_cap( $role_name, 'edit_opanda-item' );
				$this->wp_roles->add_cap( $role_name, 'edit_opanda-items' );
				$this->wp_roles->add_cap( $role_name, 'edit_others_opanda-items' );
				$this->wp_roles->add_cap( $role_name, 'publish_opanda-items' );
			}
		} else {

			if( $role->has_cap( 'manage_opanda_' . $capabilityPrefix ) && $capabilityPrefix != 'edit' )
				$role->remove_cap( 'manage_opanda_' . $capabilityPrefix );
			else if( $capabilityPrefix == 'edit' ) {
				if( $role->has_cap( 'read_opanda-item' ) )
					$this->wp_roles->remove_cap( $role_name, 'read_opanda-item' );
				if( $role->has_cap( 'read_private_opanda-items' ) )
					$this->wp_roles->remove_cap( $role_name, 'read_private_opanda-items' );
				if( $role->has_cap( 'delete_opanda-item' ) )
					$this->wp_roles->remove_cap( $role_name, 'delete_opanda-item' );
				if( $role->has_cap( 'delete_opanda-items' ) )
					$this->wp_roles->remove_cap( $role_name, 'delete_opanda-items' );
				if( $role->has_cap( 'edit_opanda-item' ) )
					$this->wp_roles->remove_cap( $role_name, 'edit_opanda-item' );
				if( $role->has_cap( 'edit_opanda-items' ) )
					$this->wp_roles->remove_cap( $role_name, 'edit_opanda-items' );
				if( $role->has_cap( 'edit_others_opanda-items' ) )
					$this->wp_roles->remove_cap( $role_name, 'edit_others_opanda-items' );
				if( $role->has_cap( 'publish_opanda-items' ) )
					$this->wp_roles->remove_cap( $role_name, 'publish_opanda-items' );
			}

		}
	}
}

